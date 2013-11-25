<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Praspel\Exception\Preambler
 */
-> import('Praspel.Exception.Preambler');

}

namespace Hoa\Praspel\Preambler {

/**
 * Class \Hoa\Praspel\Preambler\Handler.
 *
 * Handle a class and ease to run a preamble.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Handler {

    /**
     * Callable to validate and verify.
     *
     * @var \Hoa\Core\Consistency\Xcallable object
     */
    protected $__callable = null;



    /**
     * Construct.
     *
     * @access  public
     * @param   \Hoa\Core\Consistency\Xcallable   $callable    Callable.
     * @return  void
     */
    public function __construct ( \Hoa\Core\Consistency\Xcallable  $callable ) {

        $this->__setCallable($callable);

        return;
    }

    /**
     * Get reflection object.
     *
     * @access  public
     * @param   object  &$object    Object.
     * @return  \ReflectionObject
     * @throw   \Hoa\Praspel\Exception\Preambler
     */
    public function __getReflectionObject ( &$object ) {

        static $_out    = null;
        static $_object = null;

        if(null === $_out) {

            $callback = $this->__getCallable()->getValidCallback();

            if(!is_object($callback[0]))
                throw new \Hoa\Praspel\Exception\Preambler(
                    'Callable %s is not an object.', 0, $this->getCallable());

            $_object = $callback[0];
            $_out    = new \ReflectionObject($_object);
        }

        $object = $_object;

        return $_out;
    }

    /**
     * Set an attribute.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  \Hoa\Praspel\Preambler\Handler
     * @throw   \Hoa\Praspel\Exception\Preambler
     */
    public function __set ( $name, $value ) {

        $reflectionObject = $this->__getReflectionObject($object);

        if(false === $reflectionObject->hasProperty($name))
            throw new \Hoa\Praspel\Exception\Preambler(
                'Attribute %s on object %s does not exist, cannot set it.',
                1, array($name, $reflectionObject->getName()));

        $attribute = $reflectionObject->getProperty($name);
        $attribute->setAccessible(true);
        $attribute->setValue($object, $value);

        return $this;
    }

    /**
     * Get an attribute.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  mixed
     * @throw   \Hoa\Praspel\Exception\Preambler
     */
    public function __get ( $name ) {

        $reflectionObject = $this->__getReflectionObject($object);

        if(false === $reflectionObject->hasProperty($name))
            throw new \Hoa\Praspel\Exception\Preambler(
                'Attribute %s on object %s does not exist, cannot get it.',
                2, array($name, $reflectionObject->getName()));

        $attribute = $reflectionObject->getProperty($name);
        $attribute->setAccessible(true);

        return $attribute->getValue($object);
    }

    /**
     * Set callable.
     *
     * @access  protected
     * @param   \Hoa\Core\Consistency\Xcallable  $callable    Callable.
     * @return  \Hoa\Core\Consistency\Xcallable
     */
    protected function __setCallable ( \Hoa\Core\Consistency\Xcallable $callable ) {

        $old              = $this->__callable;
        $this->__callable = $callable;

        return $old;
    }

    /**
     * Get callable.
     *
     * @access  public
     * @return  \Hoa\Core\Consistency\Xcallable
     */
    public function __getCallable ( ) {

        return $this->__callable;
    }
}

}
