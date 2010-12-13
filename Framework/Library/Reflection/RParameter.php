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
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_RParameter
 *
 */

/**
 * Hoa_Reflection_Wrapper
 */
import('Reflection.Wrapper') and load();

/**
 * Hoa_Reflection_RClass
 */
import('Reflection.RClass');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element') and load();

/**
 * Class Hoa_Reflection_RParameter.
 *
 * Extending ReflectionParameter capacities.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_RParameter
 */

class          Hoa_Reflection_RParameter
    extends    Hoa_Reflection_Wrapper
    implements Hoa_Visitor_Element {

    /**
     * Parameter type (a string or an object).
     *
     * @var Hoa_Reflection_RParameter mixed
     */
    protected $_type         = null;

    /**
     * Whether the parameter is passed by reference.
     *
     * @var Hoa_Reflection_RParameter bool
     */
    protected $_byReference  = false;

    /**
     * Parameter name.
     *
     * @var Hoa_Reflection_RParameter string
     */
    protected $_name         = null;

    /**
     * Parameter default value.
     *
     * @var Hoa_Reflection_RParameter mixed
     */
    protected $_defaultValue = null;

    /**
     * Whether the parameter is optional or not.
     *
     * @var Hoa_Reflection_RParameter bool
     */
    protected $_optional     = false;

    /**
     * Parameter position.
     *
     * @var Hoa_Reflection_RParameter int
     */
    protected $_position     = 0;



    /**
     * Reflect a parameter.
     *
     * @access  public
     * @param   mixed   $function     Function owning parameter or
     *                                ReflectionParameter instance.
     * @param   string  $parameter    Parameter's name.
     * @return  void
     */
    public function __construct ( $function, $parameter = null ) {

        if($function instanceof ReflectionParameter)
            $p = $function;
        else
            $p = new ReflectionParameter($function, $parameter);

        $this->setWrapped($p);
        $this->setType($p->isArray() ? 'Array' : $p->getClass());
        $this->setReference($p->isPassedByReference());
        $this->setName($p->getName());

        if(true === $p->isDefaultValueAvailable()) {

            $this->setDefaultValue($p->getDefaultValue());
            $this->setOptional(true);
        }

        $this->_position = $p->getPosition();

        return;
    }

    /**
     * Set parameter type.
     *
     * @access  public
     * @param   mixed   $type    Type (a string or an object).
     * @return  mixed
     */
    public function setType ( $type ) {

        if($type instanceof ReflectionClass)
            $type = new Hoa_Reflection_RClass($type);

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Get parameter type.
     *
     * @access  public
     * @return  mixed
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Get parameter type as a string.
     *
     * @access  public
     * @return  string
     */
    public function getTypeAsString ( ) {

        $type = $this->getType();

        if(!is_object($type))
            return $type;

        if(   ($type instanceof ReflectionClass)
           || ($type instanceof Hoa_Reflection_RClass))
            return $type->getName();

        return get_class($type);
    }

    /**
     * Check if the parameter has a type.
     *
     * @access  public
     * @return  bool
     */
    public function hasType ( ) {

        return null !== $this->_type;
    }

    /**
     * Set whether the parameter is passed by reference.
     *
     * @access  public
     * @param   bool    $reference    Whether parameter is passed by reference
     *                                or not.
     * @return  bool
     */
    public function setReference ( $reference ) {

        $old                = $this->_byReference;
        $this->_byReference = $reference;

        return $old;
    }

    /**
     * Get whether the parameter is passed by reference.
     *
     * @access  public
     * @return  bool
     */
    public function getReference ( ) {

        return $this->_byReference;
    }

    /**
     * Override the ReflectionParameter method.
     *
     * @access  public
     * @return  bool
     */
    public function isPassedByReference ( ) {

        return $this->getReference();
    }

    /**
     * Set the parameter name.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  string
     */
    public function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the parameter name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Set the default value. Do not forget to call self::setOptional() if
     * necessary.
     *
     * @access  public
     * @param   mixed   $value    Default value.
     * @return  mixed
     */
    public function setDefaultValue ( $value ) {

        $old                 = $this->_defaultValue;
        $this->_defaultValue = $value;

        return $old;
    }

    /**
     * Get the default value.
     *
     * @access  public
     * @return  mixed
     */
    public function getDefaultValue ( ) {

        return $this->_defaultValue;
    }

    /**
     * Set whether the parameter is optinal or not.
     *
     * @access  public
     * @param   bool    $optional    Whether the parameter is optional.
     * @return  bool
     */
    public function setOptional ( $optional ) {

        $old             = $this->_optional;
        $this->_optional = $optional;

        return $old;
    }

    /**
     * Get whether the parameter is optional or not.
     *
     * @access  public
     * @return  bool
     */
    public function getOptional ( ) {

        return $this->_optional;
    }

    /**
     * Override the ReflectionParameter method.
     *
     * @access  public
     * @return  bool
     */
    public function isOptional ( ) {

        return $this->getOptional();
    }

    /**
     * Override the ReflectionParameter method.
     *
     * @access  public
     * @return  bool
     */
    public function isDefaultValueAvailable ( ) {

        return $this->getOptional();
    }

    /**
     * Override the ReflectionParameter method.
     *
     * @access  public
     * @return  bool
     */
    public function allowsNull ( ) {

        return true;
    }

    /**
     * Get the parameter position.
     *
     * @access  public
     * @return  int
     */
    public function getPosition ( ) {

        return $this->_position;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
