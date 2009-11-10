<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * @package     Hoa_Session
 * @subpackage  Hoa_Session_Option
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Session
 */
import('Session.~');

/**
 * Hoa_Session_Exception
 */
import('Session.Exception');

/**
 * Class Hoa_Session_Option.
 *
 * This class allows to manage all natives sessions options in PHP.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Session
 * @subpackage  Hoa_Session_Option
 */

class Hoa_Session_Option {

    /**
     * Serialize handler type.
     *
     * @const string
     */
    const SERIALIZE_HANDLER_PHP  = 'php';
    const SERIALIZE_HANDLER_WDDX = 'wddx';

    /**
     * Hash function name in integer.
     *
     * @const int
     */
    const HASH_FUNCTION_MD5  = 0;
    const HASH_FUNCTION_SHA1 = 1;

    /**
     * Hash bits per character type :
     *   * 4 = 0-9a-f ;
     *   * 5 = 0-9a-v ;
     *   * 6 = 0-9a-zA-Z\-,.
     *
     * @const int
     */
    const HASH_BITS_PER_CHAR_HEXA         = 4;
    const HASH_BITS_PER_CHAR_EXTENDS_HEXA = 5;
    const HASH_BITS_PER_CHAR_ALPHANUM     = 6;

    /**
     * Cache limiter.
     *
     * @const string
     */
    const CACHE_LIMITER_NONE              = 'none';
    const CACHE_LIMITER_NOCACHE           = 'nocache';
    const CACHE_LIMITER_PRIVATE           = 'private';
    const CACHE_LIMITER_PRIVATE_NO_EXPIRE = 'private_no_expire';
    const CACHE_LIMITER_PUBLIC            = 'public';

    /**
     * Entropy file and length propositions.
     *
     * @const mixed
     */
    const ENTROPY_FILE_RANDOM  = '/dev/random';
    const ENTROPY_FILE_URANDOM = '/dev/urandom';
    const ENTROPY_ACTIVE       = 7;
    const ENTROPY_DESACTIVE    = 0;

    /**
     * All options for session.
     *
     * @var Hoa_Session_Option array
     */
    protected static $options = array(
        'save_path'               => '',
        'name'                    => 'PHPSESSID',
        'auto_start'              => 0,
        'serialize_handler'       => self::SERIALIZE_HANDLER_PHP,
        'gc_probability'          => 1,
        'gc_divisor'              => 100,
        'gc_maxlifetime'          => 1440,
        'referer_check'           => '',
        'entropy_file'            => '',
        'entropy_length'          => self::ENTROPY_DESACTIVE,
        'use_cookies'             => 1,
        'use_only_cookies'        => 1,
        // Set up in Hoa_Session::setExpireSecond.
        //'cookie_lifetime'        => 0,
        'cookie_path'             => '/',
        'cookie_domain'           => '',
        'cookie_secure'           => 0,
        'cookie_httponly'         => 1,
        'cache_limiter'           => self::CACHE_LIMITER_NOCACHE,
        'cache_expire'            => 180,
        'use_trans_sid'           => 0,
        'hash_function'           => self::HASH_FUNCTION_SHA1,
        'hash_bits_per_character' => self::HASH_BITS_PER_CHAR_ALPHANUM,
        'url_rewriter.tags'       => null
    );



    /**
     * Set option.
     *
     * @access  public
     * @param   array   $option    Option to set.
     * @return  void
     */
    public static function set ( Array $option = array() ) {

        if(empty($option))
            return;

        foreach($option as $name => $value) {

            if(false === self::optionExists($name))
                throw new Hoa_Session_Exception(
                    'Option %d does not exist.', 0, $name);

            self::setOption($name, $value);
        }

        foreach(self::getOptions() as $name => $value)
            ini_set('session.' . $name, $value);
    }

    /**
     * Check if the session is using cookie or not.
     *
     * @access  public
     * @return  bool
     */
    public static function isUsingCookie ( ) {

        return self::getOption('use_cookies') == 1;
    }

    /**
     * Check if cookie is set.
     *
     * @access  public
     * @return  bool
     */
    public static function isCookieSet ( ) {

        return    self::isUsingCookie()
               && isset($_COOKIE[Hoa_Session::getName()]);
    }

    /**
     * Check if option exists.
     *
     * @access  public
     * @param   string  $name    Option name.
     * @return  bool
     */
    public static function optionExists ( $name ) {

        return isset(self::$options[$name]);
    }

    /**
     * Set a specific option.
     *
     * @access  public
     * @param   string  $name     Option name.
     * @param   string  $value    Option value.
     * @return  mixed
     * @throw   Hoa_Session_Exception
     */
    public static function setOption ( $name, $value ) {

        if(false === self::optionExists($name))
            throw new Hoa_Session_Exception(
                'Option %s does not exist.', 1, $name);

        $old                  = self::getOption($name);
        self::$options[$name] = $value;

        return $old;
    }

    /**
     * Get a specific option value.
     *
     * @access  public
     * @param   string  $name    Option name.
     * @return  mixed
     * @throw   Hoa_Session_Exception
     */
    public static function getOption ( $name ) {

        if(false === self::optionExists($name))
            throw new Hoa_Session_Exception(
                'Option %s does not exist.', 2, $name);

        return self::$options[$name];
    }

    /**
     * Get all options.
     *
     * @access  public
     * @return  array
     */
    public static function getOptions ( ) {

        return self::$options;
    }
}
