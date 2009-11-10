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
 * @package     Hoa_Uri
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
