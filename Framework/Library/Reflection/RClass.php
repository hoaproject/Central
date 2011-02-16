<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * \Hoa\Reflection\Exception
 */
-> import('Reflection.Exception')

/**
 * \Hoa\Reflection\Wrapper
 */
-> import('Reflection.Wrapper')

/**
 * \Hoa\Reflection\RProperty
 */
-> import('Reflection.RProperty')

/**
 * \Hoa\Reflection\RFunction\RMethod
 */
-> import('Reflection.RFunction.RMethod')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Reflection {

/**
 * Class \Hoa\Reflection\RClass.
 *
 * Extending ReflectionClass capacities.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class RClass extends Wrapper implements \Hoa\Visitor\Element {

    /**
     * Class file.
     *
     * @var \Hoa\Reflection\RClass string
     */
    protected $_file       = null;

    /**
     * Class name.
     *
     * @var \Hoa\Reflection\RClass string
     */
    protected $_name       = null;

    /**
     * Whether methods were already transformed or not.
     *
     * @var \Hoa\Reflection\RClass bool
     */
    protected $_firstM     = true;

    /**
     * Whether properties were already transformed or not.
     *
     * @var \Hoa\Reflection\RProperty bool
     */
    protected $_firstP     = true;

    /**
     * All methods.
     *
     * @var \Hoa\Reflection\RClass array
     */
    protected $_methods    = array();

    /**
     * All properties.
     *
     * @var \Hoa\Reflection\RProperty array
     */
    protected $_properties = array();



    /**
     * Reflect a class.
     *
     * @access  public
     * @param   mixed   $class    Class name or ReflectionClass instance.
     * @return  void
     */
    public function __construct ( $class ) {

        if($class instanceof \ReflectionClass)
            $this->setWrapped($class);
        else
            $this->setWrapped(new \ReflectionClass($class));

        $this->setName($this->getWrapped()->getName());

        return;
    }

    /**
     * Set class name.
     *
     * @access  public
     * @return  string
     */
    public function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get class name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get all properties.
     *
     * @access  public
     * @return  array
     */
    public function getProperties ( ) {

        if(false === $this->_firstP)
            return $this->_properties;

        $handle = null;

        foreach($this->getWrapped()->getProperties() as $i => $property) {

            $handle = new RProperty($property);
            $handle->_setDefaultValue($this->getFileName());
            $this->_properties[] = $handle;
        }

        $this->_firstP = false;

        return $this->_properties;
    }

    /**
     * Get all methods.
     *
     * @access  public
     * @return  array
     */
    public function getMethods ( ) {

        if(false === $this->_firstM)
            return $this->_methods;

        $handle = null;

        foreach($this->getWrapped()->getMethods() as $i => $method) {

            $handle = new RFunction\RMethod($method);
            $handle->_setFile($this->_file);

            $this->_methods[] = $handle;
        }

        $this->_firstM = false;

        return $this->_methods;
    }

    /**
     * Import a fragment.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Reflection\Exception
     */
    public function importFragment ( $fragment ) {

        if(   ($fragment instanceof RMethod)
           || ($fragment instanceof Fragment\RMethod))
            $this->_methods[] = $fragment;
        else
            throw new Exception(
                'Unknown fragment %s; cannot import it.',
                0, get_class($fragment));

        return;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
