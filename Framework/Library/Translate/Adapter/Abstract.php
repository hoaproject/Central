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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Translate
 * @subpackage  Hoa_Translate_Adapter_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Translate_Adapter_Abstract.
 *
 * Abstract layer for adapters.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Translate
 * @subpackage  Hoa_Translate_Adapter_Abstract
 */

abstract class Hoa_Translate_Adapter_Abstract {

    /**
     * Path.
     *
     * @var Hoa_Translate_Adapter_Abstract string
     */
    protected $path = '';

    /**
     * Locale.
     *
     * @var Hoa_Translate_Adapter_Abstract string
     */
    protected $locale = '';

    /**
     * Headers.
     *
     * @var Hoa_Translate_Adapter_Abstract array
     */
    protected $_headers = array();

    /**
     * Translatation array.
     *
     * @var Hoa_Translate_Adapter_Abstract array
     */
    protected $_translate = array();



    /**
     * __construct
     * Set options.
     *
     * @access  public
     * @param   path    string    Path to locale directory.
     * @param   locale  string    Locale value.
     * @param   domain  string    Domain.
     * @return  void
     * @throw   Hoa_Translate_Exception
     */
    public function __construct ( $path = '', $locale = '', $domain = null ) {

        if(empty($locale))
            throw new Hoa_Translate_Exception('Locale could not be empty.', 0);

        $this->setPath($path);
        $this->setLocale($locale);

        if($domain !== null)
            $this->setDomain($domain);

        return;
    }

    /**
     * setPath
     * Set path to locale directory.
     *
     * @access  public
     * @param   path    string    Path.
     * @return  string
     */
    public function setPath ( $path = '' ) {

        $old        = $this->path;
        $this->path = $path;

        return $old;
    }

    /**
     * setLocale
     * Set locale.
     *
     * @access  public
     * @param   locale  string    Locale.
     * @return  string
     * @throw   Hoa_Translate_Exception
     */
    public function setLocale ( $locale = '' ) {

        if(empty($locale))
            throw new Hoa_Translate_Exception('Locale could not be empty.', 1);

        $old          = $this->locale;
        $this->locale = $locale;

        return $old;
    }

    /**
     * setDomain
     * Set domain.
     *
     * @access  protected
     * @param   domain     string    Domain.
     * @return  mixed
     * @throw   Hoa_Translate_Exception
     */
    abstract protected function setDomain ( $domain = '' );



    /**
     * get
     * Translate a message.
     *
     * @access  public
     * @param   message  string    Message.
     * @param   -        -         For printf.
     * @return  string
     */
    public function get ( $message = '' ) {

        if(!isset($this->_translate[$message]))
            return $message;

        $parameters = func_get_args();
        array_shift($parameters);
        if(false === $return = @vsprintf($this->_translate[$message], $parameters))
            return $message;

        return $return;
    }

    /**
     * getn
     * Translate a message in plurial mode.
     * Help could be found here : http://gnu.org/software/gettext/manual/gettext.txt.
     * See chapter "11.2.6 Additional functions for plural forms".
     * Note : Header "Plural-Forms" must have brackets to be compatible with PHP.
     *
     * @access  public
     * @param   message         string    Message.
     * @param   message_plural  string    Message in plurial.
     * @param   n               int       n.
     * @param   -               -         For printf.
     * @return  string
     */
    public function getn ( $message = '', $message_plural = '', $n = 2 ) {

        if(empty($message) && empty($message_plural) || empty($n))
            return '';

        if($n <= 0)
            $n = 1;

        $parameters = array_slice(func_get_args(), 3);
        $n          = ceil($n);
        $key        = $message . "\0" . $message_plural;

        if(!isset($this->_translate[$key]))
            return $message_plural;

        $plurals = explode("\0", $this->_translate[$key]);

        if(!isset($this->_headers['Plural-Forms']))
            return $plurals[1];

        if(false === preg_match('#^nplurals=([0-9]+);\s*plural=(.*)$#s',
                                $this->_headers['Plural-Forms'], $matches))
            return $plurals[1];

        list(, $nplurals, $plural) = $matches;

        $plural = str_replace('n', $n, $plural);
        eval("\$i = " . $plural);

        if(!isset($plurals[$i]))
            return $plurals[1];

        if(false === $return = @vsprintf($plurals[$i], $parameters))
            return $message_plural;

        return $return;
    }
}
