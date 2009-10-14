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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Memory
 * @subpackage  Hoa_Memory_GarbageCollector
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Memory_Exception
 */
import('Memory.Exception');

/**
 * Class Hoa_Memory_GarbageCollector.
 *
 * Manage the PHP Garbage Collector.
 * Please, read http://www.research.ibm.com/people/d/dfb/papers/Bacon03Pure.pdf
 * to know more about its behavior.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Memory
 * @subpackage  Hoa_Memory_GarbageCollector
 */

class Hoa_Memory_GarbageCollector {

    /**
     * Whether version is ok.
     *
     * @var Hoa_Memory_GarbageCollector bool
     */
    protected static $_isVersionOk = false;



    /**
     * Active the garbage collector.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Memory_Exception
     */
    public static function enable ( ) {

        self::checkCompatibility(true);

        return gc_enable();
    }

    /**
     * Disactive the garbage collector.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Memory_Exception
     */
    public static function disable ( ) {

        self::checkCompatibility(true);

        return gc_disable();
    }

    /**
     * Return the circular reference collector status.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Memory_Exception
     */
    public static function isEnabled ( ) {

        self::checkCompatibility(true);

        return gc_enabled();
    }

    /**
     * Force collection of any existing garbage cycles.
     *
     * @access  public
     * @return  int
     */
    public static function collect ( ) {

        self::checkCompatibility(true);

        return gc_collect_cycles();
    }

    /**
     * Check version.
     *
     * @access  public
     * @param   bool    $exception    Throw exception is set to true, else
     *                                return false.
     * @return  bool
     * @throw   Hoa_Memory_Exception
     */
    public static function checkCompatibility ( $exception = false ) {

        if(true === self::$_isVersionOk)
            return true;

        if(PHP_VERSION_ID < 50300)
            if(true === $exception)
                throw new Hoa_Memory_Exception(
                    'PHP 5.3.0 must be installed to use this package; ' .
                    'the current version is: %s.', 0, PHP_VERSION);
            else
                return false;

        return self::$_isVersionOk = true;
    }
}
