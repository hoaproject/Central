<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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
 * \Hoa\Prototype\Exception
 */
-> import('Prototype.Exception');

}

namespace Hoa\Prototype {

/**
 * Class \Hoa\Prototype.
 *
 * Enable to do a Prototype-based programming.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Prototype
{
    /**
     * Where user believes to set its prototype.
     */
    //public $prototype      = null;

    /**
     * Prototype instance.
     *
     * @var \Hoa\Prototype
     */
    private $_prototype   = null;

    /**
     * Prototype class type.
     *
     * @var string
     */
    protected $_classType  = '';

    /**
     * Prototype object type.
     *
     * @var string
     */
    protected $_objectType = '';



    /**
     * Set the prototype or attempt to set a prototype slot.
     *
     * @param   string  $name     'prototype' if want to set the prototype,
     *                            slot name else.
     * @param   mixed   $value    The prototype object or the slot value.
     * @return  mixed
     * @throws  \Hoa\Prototype\Exception
     */
    public function __set($name, $value)
    {
        if (true === property_exists($this, 'prototype')) {
            throw new Exception(
                'You must not have a prototype attribute declared in your ' .
                'class.', 0);
        }

        if (strtolower($name) == 'prototype') {
            $out = is_object($value);

            if ($out && $this->_objectType != '') {
                $out  = $value instanceof $this->_objectType;
            }

            if ($out && $this->_classType != '') {
                $out &=    strtolower(get_class($value))
                        == strtolower($this->_classType);
            }

            if (false === (bool) $out) {
                throw new Exception(
                    'Cannot set the prototype %s; it must be an object of ' .
                    'type %s and a class of type %s.',
                    1, [
                        get_class($value),
                        $this->_objectType,
                        $this->_classType
                    ]);
            }

            return $this->_prototype = $value;
        }

        if (null === $this->_prototype) {
            throw new Exception(
                'Undefined property: %s::%s.',
                2, [get_class($this), $name]);
        }

        return $this->_prototype->$name = $value;
    }

    /**
     * Get a prototype slot value.
     *
     * @param   string  $name    Slot name.
     * @return  mixed
     * @throws  \Hoa\Prototype\Exception
     */
    public function __get($name)
    {
        if (true === property_exists($this, 'prototype')) {
            throw new Exception(
                'You must not have a prototype attribute declared in your ' .
                'class.', 3);
        }

        if (null === $this->_prototype) {
            throw new Exception(
                'Undefined property: %s::%s.',
                4, [get_class($this), $name]);
        }

        if (true === property_exists($this->_prototype, $name)) {
            return $this->_prototype->$name;
        }

        if ($this->_prototype instanceof self) {
            return $this->_prototype->__get($name);
        }

        throw new Exception(
            'Undefined property: %s::%s.',
            5, [get_class($this), $name]);
    }

    /**
     * Call a prototype slot.
     * Normally, in the Prototype-based programming, there is no difference
     * between an attribute or a method, all is slot. But in PHP, there is a
     * difference. So __call and __get act in the same way.
     *
     * @param   string  $name         Slot name.
     * @param   array   $arguments    Slot arguments.
     * @return  mixed
     * @throws  \Hoa\Prototype\Exception
     */
    public function __call($name, array $arguments)
    {
        if (null === $this->_prototype) {
            throw new Exception(
                'Call to undefined property: %s::%s().',
                6, [get_class($this), $name]);
        }

        $callback = [$this->_prototype, $name];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $arguments);
        }

        if ($this->_prototype instanceof self) {
            return $this->_prototype->__call($name, $arguments);
        }

        throw new Exception(
            'Call to uncallable method %s::%s().', 7,
            [get_class($this), $name]);
    }

    /**
     * Set the prototype class type.
     *
     * @param   string     $type    Prototype class type.
     * @return  string
     */
    protected function setPrototypeClassType($type)
    {
        $old              = $this->_classType;
        $this->_classType = $type;

        return $old;
    }

    /**
     * Set the prototype object type.
     *
     * @param   string     $type    Prototype object type.
     * @return  string
     */
    protected function setPrototypeObjectType($type)
    {
        $old               = $this->_objectType;
        $this->_objectType = $type;

        return $old;
    }

    /**
     * Disable the prototype class type.
     *
     * @return  string
     */
    protected function disablePrototypeClassType()
    {
        return $this->setPrototypeClassType(null);
    }

    /**
     * Disable the prototype object type.
     *
     * @return  string
     */
    protected function disablePrototypeObjectType()
    {
        return $this->setPrototypeObjectType(null);
    }
}

}

namespace {

/**
 * Flex entity.
 */
Hoa\Consistency::flexEntity('Hoa\Prototype\Prototype');

}
