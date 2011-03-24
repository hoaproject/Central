<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Session
 */
-> import('Session.~')

/**
 * \Hoa\Session\Exception
 */
-> import('Session.Exception.~');

}

namespace Hoa\Session {

/**
 * Class \Hoa\Session\Option.
 *
 * This class allows to manage all natives sessions options in PHP.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Option {

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
     * @var \Hoa\Session\Option array
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
        // Set up in \Hoa\Session::setExpireSecond.
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
                throw new Exception(
                    'Option %d does not exist.', 0, $name);

            self::setOption($name, $value);
        }

        foreach(self::getOptions() as $name => $value)
            ini_set('session.' . $name, $value);

        return;
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
               && isset($_COOKIE[\Hoa\Session::getName()]);
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
     * @throw   \Hoa\Session\Exception
     */
    public static function setOption ( $name, $value ) {

        if(false === self::optionExists($name))
            throw new Exception(
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
     * @throw   \Hoa\Session\Exception
     */
    public static function getOption ( $name ) {

        if(false === self::optionExists($name))
            throw new Exception(
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

}
