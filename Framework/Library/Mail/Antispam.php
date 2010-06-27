<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Antispam
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Mail
 */
import('Mail.~');

/**
 * Class Hoa_Mail_Antispam.
 *
 * Write an encoded/decoded email address, and run mailto:.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Antispam
 */

class Hoa_Mail_Antispam extends Hoa_Mail {

    /**
     * $_GET[$getVar];
     *
     * @var Hoa_Mail_Antispam string
     */
    protected $getVar = 'mail';

    /**
     * Email address.
     *
     * @var Hoa_Mail_Antispam string
     */
    protected $mail = '';

    /**
     * Is already encoded ?
     *
     * @var Hoa_Mail_Antispam bool
     */
    protected $isEncoded = false;

    /**
     * Mail translation.
     *
     * @var Hoa_Mail_Antispam string
     */
    protected $lang = array();



    /**
     * __construct
     * Refresh to mailto:.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( /*$langPath = 'i18n/'*/ ) {

        if(!empty($_GET[$this->getVar])) {
            $this->mail = $this->getMailTo();
            header('Refresh: 0; url="mailto:'.$this->mail.'"');
        }

        //$this->lang = Xml::parse($langPath.'lang.mail.xml');
    }


    /**
     * getMailTo
     * Get and decode an email address.
     *
     * @access  protected
     * @return  string
     */
    protected function getMailTo ( ) {

        $this->mail = $_GET[$this->getVar];
        $this->decode($this->mail, true);

        return $this->mail;
    }


    /**
     * encode
     * Encode a string with urlencode,
     * within a shift of chars according to random integer.
     *
     * @access  protected
     * @param   str        string    String to encode.
     * @return  string
     */
    protected function encode ( &$str ) {

        if($this->isEncoded === false) {

            $key    = rand(100, 999);
            $str    = str_replace('@', '_AT_', $str);
            $str    = urlencode($str);
            $tmp    = $str;
            $length = strlen($tmp);
            $str    = '';

            for($i = 0; $i < $length; $i++)
                $str .= chr(ord($tmp{$i}) + $key);

            $str = urlencode(chr($key) . $str);
            unset($tmp);

            $this->isEncoded = true;
        }

        return $str;
    }


    /**
     * decode
     * Decode a string (encoded by $this->encode();).
     *
     * @access  protected
     * @param   str        string    String to encode.
     * @param   force      bool      Force to decode.
     * @return  string
     */
    protected function decode ( &$str, $force = false ) {

        if($this->isEncoded === true || $force === true) {

            $str    = urldecode($str);
            $key    = ord($str[0]);
            $tmp    = $str;
            $length = strlen($tmp);
            $str    = '';

            for($i = 1; $i < $length; $i++)
                $str .= chr(ord($tmp{$i}) - $key);

            $str = str_replace('_AT_', '@', $str);
            $str = urldecode($str);
            unset($tmp);

            $this->isEncoded = false;
        }

        return $str;
    }


    /**
     * write
     * Encode and write an email address.
     *
     * @access  public
     * @param   str     string    String to encode and write.
     * @return  string
     */
    public function write ( $str ) {

        $this->mail = $str;

        if($this->isEncoded === false)
            return $this->encode($this->mail);
        else
            return $this->rmArobase($this->mail);
    }


    /**
     * rmArobase
     * Remove arobase and replace it by a string.
     *
     * @access  public
     * @param   str     string    String to encode and write.
     * @return  string
     */
    public function rmArobase ( $str ) {

        return str_replace('@', '&nbsp;_AT_&nbsp;', $str);
        //return str_replace('@', '&nbsp;'.$this->lang['arobase'].'&nbsp;', $str);
    }
}
