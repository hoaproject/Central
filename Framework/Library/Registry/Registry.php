<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Registry\Exception
 */
-> import('Registry.Exception');

}

namespace Hoa\Registry {

/**
 * Class \Hoa\Registry.
 *
 * Hold a register of objects.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Registry extends \ArrayObject {

    /**
     * Instance.
     *
     * @var \Hoa\Registry object
     */
    private static $_instance = null;



    /**
     * Private constructor.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Registry\Exception
     */
    public function __construct ( ) {

        throw new Exception(
            'Cannot instance the \Hoa\Registry object. Use set, get and ' .
            'isRegistered static methods.', 0);

        return;
    }

    /**
     * Get instance of \Hoa\Registry.
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
     * @throw   \Hoa\Registry\Exception
     */
    public static function get ( $index ) {

        $registry = self::getInstance();

        if(!$registry->offsetExists($index))
            throw new Exception('Registry %s does not exist.',
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
    public static function remote ( $index ) {

        self::getInstance()->offsetUnset($index);

        return;
    }
}

/**
 * Class \Hoa\Registry\_Protocol.
 *
 * hoa://Library/Registry component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class _Protocol extends \Hoa\Core\Protocol {

    /**
     * Component's name.
     *
     * @var \Hoa\Core\Protocol string
     */
    protected $_name = 'Registry';



    /**
     * ID of the component.
     * Generic one. Should be overload in childs classes.
     *
     * @access  public
     * @param   string  $id    ID of the component.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function reachId ( $id ) {

        return \Hoa\Registry::get($id);
    }
}

}

namespace {

/**
 * Add the hoa://Library/Registry component. Should be use to reach/get an entry
 * in the \Hoa\Registry, e.g.: resolve('hoa://Library/Registry#AnID').
 */
\Hoa\Core::getInstance()
    ->getProtocol()
    ->getComponent('Library')
    ->addComponent(new \Hoa\Registry\_Protocol());

}
