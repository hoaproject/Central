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
 * @package     Hoa_Prototype
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Prototype_Exception
 */
import('Prototype.Exception');

/**
 * Class Hoa_Prototype.
 *
 * Enable to do a Prototype-based programming.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Prototype
 */

class Hoa_Prototype {

    /**
     * Where user believes to set its prototype.
     */
    //public $prototype      = null;

    /**
     * Prototype instance.
     *
     * @var Hoa_Prototype object
     */
    private  $_prototype   = null;

    /**
     * Prototype class type.
     *
     * @var Hoa_Prototype string
     */
    protected $_classType  = '';

    /**
     * Prototype object type.
     *
     * @var Hoa_Prototype string
     */
    protected $_objectType = '';



    /**
     * Set the prototype or attempt to set a prototype slot.
     *
     * @access  public
     * @param   string  $name     'prototype' if want to set the prototype,
     *                            slot name else.
     * @param   mixed   $value    The prototype object or the slot value.
     * @return  mixed
     * @throw   Hoa_Prototype_Exception
     */
    public function __set ( $name, $value ) {

        if(true === property_exists($this, 'prototype'))
            throw new Hoa_Prototype_Exception(
                'You must not have a prototype attribute declared in your ' .
                'class.', 0);

        if(strtolower($name) == 'prototype') {

            $out = is_object($value);

            if($out && $this->_objectType != '')
                $out  = $value instanceof $this->_objectType;

            if($out && $this->_classType != '')
                $out &=    strtolower(get_class($value))
                        == strtolower($this->_classType);

            if(false === (bool) $out)
                throw new Hoa_Prototype_Exception(
                    'Cannot set the prototype %s; it must be an object of ' .
                    'type %s and a class of type %s.',
                    1, array(
                        get_class($value),
                        $this->_objectType,
                        $this->_classType
                    ));

            return $this->_prototype = $value;
        }

        if(null === $this->_prototype)
            throw new Hoa_Prototype_Exception(
                'Undefined property: %s::%s.',
                2, array(get_class($this), $name));

        return $this->_prototype->$name = $value;
    }

    /**
     * Get a prototype slot value.
     *
     * @access  public
     * @param   string  $name    Slot name.
     * @return  mixed
     * @throw   Hoa_Prototype_Exception
     */
    public function __get ( $name ) {

        if(true === property_exists($this, 'prototype'))
            throw new Hoa_Prototype_Exception(
                'You must not have a prototype attribute declared in your ' .
                'class.', 3);

        if(null === $this->_prototype)
            throw new Hoa_Prototype_Exception(
                'Undefined property: %s::%s.',
                4, array(get_class($this), $name));

        if(true === property_exists($this->_prototype, $name))
            return $this->_prototype->$name;

        if($this->_prototype instanceof Hoa_Prototype)
            return $this->_prototype->__get($name);

        throw new Hoa_Prototype_Exception(
            'Undefined property: %s::%s.',
            5, array(get_class($this), $name));
    }

    /**
     * Call a prototype slot.
     * Normally, in the Prototype-based programming, there is no difference
     * between an attribute or a method, all is slot. But in PHP, there is a
     * difference. So __call and __get act in the same way.
     *
     * @access  public
     * @param   string  $name         Slot name.
     * @param   array   $arguments    Slot arguments.
     * @return  mixed
     * @throw   Hoa_Prototype_Exception
     */
    public function __call ( $name, Array $arguments ) {

        if(null === $this->_prototype)
            throw new Hoa_Prototype_Exception(
                'Call to undefined property: %s::%s().',
                6, array(get_class($this), $name));

        $callback = array($this->_prototype, $name);

        if(is_callable($callback))
            return call_user_func_array($callback, $arguments);

        if($this->_prototype instanceof Hoa_Prototype)
            return $this->_prototype->__call($name, $arguments);

        throw new Hoa_Prototype_Exception(
            'Call to uncallable method %s::%s().', 7,
            array(get_class($this), $name));
    }

    /**
     * Set the prototype class type.
     *
     * @access  protected
     * @param   string     $type    Prototype class type.
     * @return  string
     */
    protected function setPrototypeClassType ( $type ) {

        $old              = $this->_classType;
        $this->_classType = $type;

        return $old;
    }

    /**
     * Set the prototype object type.
     *
     * @access  protected
     * @param   string     $type    Prototype object type.
     * @return  string
     */
    protected function setPrototypeObjectType ( $type ) {

        $old               = $this->_objectType;
        $this->_objectType = $type;

        return $old;
    }

    /**
     * Disable the prototype class type.
     *
     * @access  protected
     * @return  string
     */
    protected function disablePrototypeClassType ( ) {

        return $this->setPrototypeClassType(null);
    }

    /**
     * Disable the prototype object type.
     *
     * @access  protected
     * @return  string
     */
    protected function disablePrototypeObjectType ( ) {

        return $this->setPrototypeObjectType(null);
    }
}
