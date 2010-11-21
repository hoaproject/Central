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
 * @package     Hoa_Registry
 *
 */

/**
 * Hoa_Registry_Exception
 */
import('Registry.Exception');

/**
 * Class Hoa_Registry.
 *
 * Hold a register of objects.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Registry
 */

class Hoa_Registry extends ArrayObject {

    /**
     * Instance.
     *
     * @var Hoa_Registry object
     */
    private static $_instance = null;



    /**
     * Private constructor.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Registry_Exception
     */
    public function __construct ( ) {

        throw new Hoa_Registry_Exception(
            'Cannot instance the Hoa_Registry object. Use set, get and ' .
            'isRegistered static methods.', 0);

        return;
    }

    /**
     * Get instance of Hoa_Registry.
     *
     * @access  protected
     * @return  object
     */
    protected static function getInstance ( ) {

        if(null === self::$_instance)
            self::$_instance = new parent();

        return self::$_instance;
    }

    /**
     * Set a new registry.
     *
     * @access  public
     * @param   mixed   $index     Index of registry.
     * @param   mixed   $value     Value of registry.
     * @return  void
     */
    public static function set ( $index, $value ) {

        self::getInstance()->offsetSet($index, $value);

        return;
    }

    /**
     * Get a registry.
     *
     * @access  public
     * @param   mixed   $index     Index of registry.
     * @return  mixed
     * @throw   Hoa_Registry_Exception
     */
    public static function get ( $index ) {

        $registry = self::getInstance();

        if(!$registry->offsetExists($index))
            throw new Hoa_Registry_Exception('Registry %s does not exist.',
                1, $index);

        return $registry->offsetGet($index);
    }

    /**
     * Check if an index is registered.
     *
     * @access  public
     * @param   mixed   $index     Index of registry.
     * @return  bool
     */
    public static function isRegistered ( $index ) {

        return self::getInstance()->offsetExists($index);
    }

    /**
     * Unset an registry.
     *
     * @access  public
     * @param   mixed   $index    Index of registry.
     * @return  void
     */
    public static function delete ( $index ) {

        self::getInstance()->offsetUnset($index);

        return;
    }
}
