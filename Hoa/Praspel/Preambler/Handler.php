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

namespace Hoa\Praspel\Preambler;

use Hoa\Consistency;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Preambler\Handler.
 *
 * Handle a class and ease to run a preamble.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Handler
{
    /**
     * Callable to validate and verify.
     *
     * @var \Hoa\Consistency\Xcallable
     */
    protected $__callable        = null;

    /**
     * Generated reflection object.
     *
     * This is a cache.
     */
    private $__reflectionObject = null;

    /**
     * Generated object.
     *
     * This is a cache.
     */
    private $__object            = null;



    /**
     * Construct.
     *
     * @param   \Hoa\Consistency\Xcallable   $callable    Callable.
     */
    public function __construct(Consistency\Xcallable  $callable)
    {
        $this->__setCallable($callable);

        return;
    }

    /**
     * Get reflection object.
     *
     * @param   object  &$object    Object.
     * @return  \ReflectionObject
     * @throws  \Hoa\Praspel\Exception\Preambler
     */
    public function __getReflectionObject(&$object)
    {
        if (null === $this->__reflectionObject) {
            $callback = $this->__getCallable()->getValidCallback();

            if (!is_object($callback[0])) {
                throw new Praspel\Exception\Preambler(
                    'Callable %s is not an object.',
                    0,
                    $this->__getCallable()
                );
            }

            $this->__object           = $callback[0];
            $this->__reflectionObject = new \ReflectionObject($this->__object);
        }

        $object = $this->__object;

        return $this->__reflectionObject;
    }

    /**
     * Set an attribute.
     *
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  \Hoa\Praspel\Preambler\Handler
     * @throws  \Hoa\Praspel\Exception\Preambler
     */
    public function __set($name, $value)
    {
        $reflectionObject = $this->__getReflectionObject($object);

        if (false === $reflectionObject->hasProperty($name)) {
            throw new Praspel\Exception\Preambler(
                'Attribute %s on object %s does not exist, cannot set it.',
                1,
                [$name, $reflectionObject->getName()]
            );
        }

        $attribute = $reflectionObject->getProperty($name);
        $attribute->setAccessible(true);
        $attribute->setValue($object, $value);

        return $this;
    }

    /**
     * Get an attribute.
     *
     * @param   string  $name    Name.
     * @return  mixed
     * @throws  \Hoa\Praspel\Exception\Preambler
     */
    public function __get($name)
    {
        $reflectionObject = $this->__getReflectionObject($object);

        if (false === $reflectionObject->hasProperty($name)) {
            throw new Praspel\Exception\Preambler(
                'Attribute %s on object %s does not exist, cannot get it.',
                2,
                [$name, $reflectionObject->getName()]
            );
        }

        $attribute = $reflectionObject->getProperty($name);
        $attribute->setAccessible(true);

        return $attribute->getValue($object);
    }

    /**
     * Set callable.
     *
     * @param   \Hoa\Consistency\Xcallable  $callable    Callable.
     * @return  \Hoa\Consistency\Xcallable
     */
    public function __setCallable(Consistency\Xcallable $callable)
    {
        $old              = $this->__callable;
        $this->__callable = $callable;

        return $old;
    }

    /**
     * Get callable.
     *
     * @return  \Hoa\Consistency\Xcallable
     */
    public function __getCallable()
    {
        return $this->__callable;
    }
}
