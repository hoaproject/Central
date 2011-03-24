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
 * \Hoa\Reflection\RFunction\RAbstract
 */
-> import('Reflection.RFunction.RAbstract');

}

namespace Hoa\Reflection\RFunction {

/**
 * Class \Hoa\Reflection\RFunction.
 *
 * Extending ReflectionMethod capacities.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class RMethod extends RAbstract {

    /**
     * Method finality.
     *
     * @var \Hoa\Reflection\RFunction\RMethod int
     */
    protected $_finality       = _overridable;

    /**
     * Method implementation.
     *
     * @var \Hoa\Reflection\RFunction\RMethod int
     */
    protected $_implementation = _concrete;

    /**
     * Method visibility.
     *
     * @var \Hoa\Reflection\RFunction\RMethod int
     */
    protected $_visibility     = _public;

    /**
     * Memory stack where the method is declared.
     *
     * @var \Hoa\Reflection\RFunction\RMethod int
     */
    protected $_memoryStack    = _dynamic;



    /**
     * Reflect a method.
     *
     * @access  public
     * @param   mixed    $class    Class name or ReflectionMethod instance.
     * @param   string   $name     Method name.
     * @return  void
     */
    public function __construct ( $class, $name = null ) {

        if(!($class instanceof \ReflectionMethod))
            $method = new \ReflectionMethod($class, $name);
        else
            $method = $class;

        parent::__construct($method);

        if(true === $method->isFinal())
            $this->setFinality(_final);

        if(true === $method->isAbstract())
            $this->setImplementation(_abstract);

        if(true === $method->isProtected())
            $this->setVisibility(_protected);
        elseif(true === $method->isPrivate())
            $this->setVisibility(_private);

        if(true === $method->isStatic())
            $this->setMemoryStack(_static);

        return;
    }

    /**
     * Set the method finality.
     *
     * @access  public
     * @param   int     $finality    Finality (please, see the _final and
     *                               _overridable constants).
     * @return  int
     */
    public function setFinality ( $finality ) {

        $old             = $this->_finality;
        $this->_finality = $finality;

        return $old;
    }

    /**
     * Get the method finality.
     * Please, see the _final and _overridable constants.
     *
     * @access  public
     * @return  int
     */
    public function getFinality ( ) {

        return $this->_finality;
    }

    /**
     * Check if the method is final or not.
     *
     * @access  public
     * @return  bool
     */
    public function isFinal ( ) {

        return _final == $this->getFinality();
    }

    /**
     * Check if the method is overridable or not.
     *
     * @access  public
     * @return  bool
     */
    public function isOverridable ( ) {

        return _overridable == $this->getFinality();
    }

    /**
     * Set the method implementation.
     *
     * @access  public
     * @param   int     $implementation    Implementation (please, see the
     *                                     _concrete and the _abstract
     *                                     constants).
     * @return  int
     */
    public function setImplementation ( $implementation ) {

        $old                   = $this->_implementation;
        $this->_implementation = $implementation;

        return $old;
    }

    /**
     * Get the method implementation.
     * Please, see the _concrete and the _abstract constants.
     *
     * @access  public
     * @return  int
     */
    public function getImplementation ( ) {

        return $this->_implementation;
    }

    /**
     * Check if the method is concrete or not.
     *
     * @access  public
     * @return  bool
     */
    public function isConcrete ( ) {

        return _concrete == $this->getImplementation();
    }

    /**
     * Check if the method is abstract or not.
     *
     * @access  public
     * @return  bool
     */
    public function isAbstract ( ) {

        return _abstract == $this->getImplementation();
    }

    /**
     * Set the method visibility.
     *
     * @access  public
     * @param   int     $visibility    Visibility (please, see the _public,
     *                                 _protected and _private constants).
     * @return  int
     */
    public function setVisibility ( $visibility ) {

        $old               = $this->_visibility;
        $this->_visibility = $visibility;

        return $old;
    }

    /**
     * Get the method visibility.
     * Please, see the _public, _protected and _private constants.
     *
     * @access  public
     * @return  int
     */
    public function getVisibility ( ) {

        return $this->_visibility;
    }

    /**
     * Check if the method is public or not.
     *
     * @access  public
     * @return  bool
     */
    public function isPublic ( ) {

        return _public == $this->getVisibility();
    }

    /**
     * Check if the method is protected or not.
     *
     * @access  public
     * @return  bool
     */
    public function isProtected ( ) {

        return _protected == $this->getVisibility();
    }

    /**
     * Check if the method is private or not.
     *
     * @access  public
     * @return  bool
     */
    public function isPrivate ( ) {

        return _private == $this->getVisibility();
    }

    /**
     * Set the method memory stack.
     *
     * @access  public
     * @param   int     $stack    Memory stack (please, see the _dynamic and
     *                            _static constants).
     * @return  int
     */
    public function setMemoryStack ( $stack ) {

        $old                = $this->_memoryStack;
        $this->_memoryStack = $stack;

        return $old;
    }

    /**
     * Get the method memory stack.
     * Please, see the _dynamic and _static constants.
     *
     * @access  public
     * @return  int
     */
    public function getMemoryStack ( ) {

        return $this->_memoryStack;
    }

    /**
     * Check if the method is dynamic or not.
     *
     * @access  public
     * @return  bool
     */
    public function isDynamic ( ) {

        return _dynamic == $this->getMemoryStack();
    }

    /**
     * Check if the method is static or not.
     *
     * @access  public
     * @return  bool
     */
    public function isStatic ( ) {

        return _static == $this->getMemoryStack();
    }

    /**
     * Check if the method is a constructor or not.
     *
     * @access  public
     * @return  bool
     */
    public function isConstructor ( ) {

        return '__construct' == $this->getName();
    }

    /**
     * Check if the method is a destructor or not.
     *
     * @access  public
     * @return  bool
     */
    public function isDestructor ( ) {

        return '__destruct' == $this->getName();
    }
}

}
