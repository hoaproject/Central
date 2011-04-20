<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @category    Framework
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Antispam
 *
 */

/**
 * Hoa_Mail
 */
import('Mail.~');

/**
 * Class Hoa_Mail_Antispam.
 *
 * Write an encoded/decoded email address, and run mailto:.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
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
