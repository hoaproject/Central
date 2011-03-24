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
 * @package     Hoa_Uri
 *
 */

/**
 * Hoa_Uri_Exception
 */
import('Uri.Exception');

/**
 * Class Hoa_Uri.
 *
 * Uniform Resource Identifier.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Uri
 */

abstract class Hoa_Uri {

    /**
     * URI.
     *
     * @var Hoa_Uri string
     */
    protected $_uri = null;



    /**
     * The constructor
     *
     * @return void
     */
    abstract protected function __construct ( $scheme = '' );

    /**
     * Get complete URI.
     *
     * @return string
     */
    abstract protected function getUri ( );

    /**
     * Check if an URI is valid or not.
     *
     * @return boolean
     */
    abstract protected function isValid ( );

    /**
     * factory
     * Create a new Hoa_Uri object.
     *
     * @access  public
     * @param   uri     string    Hoa_Uri object or a complete Uri.
     * @return  object
     * @throw   Hoa_Uri_Exception
     */
    public static function factory ( $uri = null ) {

        $scheme = ucfirst(strtolower(self::getScheme($uri)));

        if(!ctype_alnum($scheme))
            throw new Hoa_Uri_Exception('Only alphanumerics characters are allowed for scheme.', 0);

        $class  = 'Hoa_Uri_' . $scheme;
        import('Uri.' . $scheme);

        return new $class($uri);
    }

    /**
     * getScheme
     * Detect automatically an URI scheme.
     *
     * @access  public
     * @param   uri     string    Uniform Resource Identifier.
     * @return  string
     */
    public static function getScheme ( $uri ) {

        if(false === strpos($uri, '://'))
            return $uri;

        $uri = explode('://', $uri, 2);

        if(!isset($uri[0]))
            return null;

        return $uri[0];
    }
}
