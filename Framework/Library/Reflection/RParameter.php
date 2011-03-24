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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Reflection\Wrapper
 */
-> import('Reflection.Wrapper')

/**
 * \Hoa\Reflection\RClass
 */
-> import('Reflection.RClass')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Reflection {

/**
 * Class \Hoa\Reflection\RParameter.
 *
 * Extending ReflectionParameter capacities.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class RParameter extends Wrapper implements \Hoa\Visitor\Element {

    /**
     * Parameter type (a string or an object).
     *
     * @var \Hoa\Reflection\RParameter mixed
     */
    protected $_type         = null;

    /**
     * Whether the parameter is passed by reference.
     *
     * @var \Hoa\Reflection\RParameter bool
     */
    protected $_byReference  = false;

    /**
     * Parameter name.
     *
     * @var \Hoa\Reflection\RParameter string
     */
    protected $_name         = null;

    /**
     * Parameter default value.
     *
     * @var \Hoa\Reflection\RParameter mixed
     */
    protected $_defaultValue = null;

    /**
     * Whether the parameter is optional or not.
     *
     * @var \Hoa\Reflection\RParameter bool
     */
    protected $_optional     = false;

    /**
     * Parameter position.
     *
     * @var \Hoa\Reflection\RParameter int
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

        if($function instanceof \ReflectionParameter)
            $p = $function;
        else
            $p = new \ReflectionParameter($function, $parameter);

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

        if($type instanceof \ReflectionClass)
            $type = new RClass($type);

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

        if(   ($type instanceof \ReflectionClass)
           || ($type instanceof RClass))
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
