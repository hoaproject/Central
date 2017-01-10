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

namespace Hoa\Praspel\Model;

use Hoa\Iterator;

/**
 * Class \Hoa\Praspel\Model\Throwable.
 *
 * Represent the @throwable clause.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          Throwable
    extends    Clause
    implements Iterator\Aggregate,
               \ArrayAccess,
               \Countable
{
    /**
     * Name.
     *
     * @const string
     */
    const NAME        = 'throwable';

    /**
     * Identifier index.
     *
     * @const int
     */
    const IDENTIFIER  = 0;

    /**
     * Instance of index.
     *
     * @const int
     */
    const INSTANCE_OF = 1;

    /**
     * With index.
     *
     * @const int
     */
    const WITH        = 2;

    /**
     * Disjunction index.
     *
     * @const int
     */
    const DISJUNCTION = 3;

    /**
     * List of exception names.
     *
     * @var array
     */
    protected $_exceptions       = [];

    /**
     * Current exception.
     *
     * @var array
     */
    protected $_currentException = null;



    /**
     * Check if an exception identifier exists.
     *
     * @param   string  $identifier    Exception identifier.
     * @return  bool
     */
    public function offsetExists($identifier)
    {
        return isset($this->_exceptions[$identifier]);
    }

    /**
     * Select an exception.
     *
     * @param   string  $identifier    Exception identifier.
     * @return  \Hoa\Praspel\Model\Throwable
     */
    public function offsetGet($identifier)
    {
        if (false === $this->offsetExists($identifier)) {
            return null;
        }

        unset($this->_currentException);
        $this->_currentException = &$this->_exceptions[$identifier];

        return $this;
    }

    /**
     * Add an exception.
     *
     * @param   string  $identifier       Exception identifier.
     * @param   mixed   $instanceName     Exception instance name.
     * @return  mixed
     */
    public function offsetSet($identifier, $instanceName)
    {
        $old                            = $this->offsetGet($identifier);
        $this->_exceptions[$identifier] = [
            static::IDENTIFIER  => $identifier,
            static::INSTANCE_OF => $instanceName,
            static::WITH        => null,
            static::DISJUNCTION => null
        ];

        return $old;
    }

    /**
     * Delete an exception.
     *
     * @param   string  $identifier    Exception identifier.
     * @return  void
     */
    public function offsetUnset($identifier)
    {
        unset($this->_exceptions[$identifier]);

        return;
    }

    /**
     * Get instance name.
     *
     * @return  string
     */
    public function getInstanceName()
    {
        if (null === $this->_currentException) {
            return null;
        }

        return $this->_currentException[static::INSTANCE_OF];
    }

    /**
     * Create a new with instance (an Hoa\Praspel\Model\Ensures instance with
     * this instance as parent).
     *
     * @return  \Hoa\Praspel\Model\Ensures
     */
    public function newWith()
    {
        return new Ensures($this);
    }

    /**
     * Set with declaration.
     *
     * @param   \Hoa\Praspel\Model\Ensures  $with    With.
     * @return  \Hoa\Praspel\Model\Throwable
     */
    public function setWith(Ensures $with)
    {
        if (null === $this->_currentException) {
            return $this;
        }

        $this->_currentException[static::WITH] = $with;

        return $this;
    }

    /**
     * Get with declaration.
     *
     * @return  \Hoa\Praspel\Model\Ensures
     */
    public function getWith()
    {
        if (null === $this->_currentException) {
            return null;
        }

        return $this->_currentException[static::WITH];
    }

    /**
     * Declare that this exception is disjointed with another one.
     *
     * @param   string  $identifier    Identifier.
     * @return  \Hoa\Praspel\Model\Throwable
     */
    public function disjunctionWith($identifier)
    {
        if (null === $this->_currentException) {
            return $this;
        }

        if (false === isset($this[$identifier])) {
            return $this;
        }

        $_identifier                           = &$this->_exceptions[$identifier];
        $this->_currentException[static::WITH] = &$_identifier[static::WITH];

        if (true === is_array($_identifier[static::DISJUNCTION])) {
            $_identifier[static::DISJUNCTION][] =
                $this->_currentException[static::IDENTIFIER];
        } else {
            $_identifier[static::DISJUNCTION] = [
                $this->_currentException[static::IDENTIFIER]
            ];
        }

        $this->_currentException[static::DISJUNCTION] = $identifier;

        return $this;
    }

    /**
     * Check if an exception is disjointed with another one.
     *
     * @return  bool
     */
    public function isDisjointed()
    {
        return is_string($this->getDisjunction());
    }

    /**
     * Get disjointed exceptions.
     * Example:
     *     T1 t1 or T2 t2 or T3 t3
     * For t1, this method will return an array containing t2 and t3.
     * For t2, this method will return a string equals to t1.
     * Same for t3.
     * If this method returns null, it means that the exception is not in a
     * disjunction.
     *
     * @return  mixed
     */
    public function getDisjunction()
    {
        if (null === $this->_currentException) {
            return null;
        }

        return $this->_currentException[static::DISJUNCTION];
    }

    /**
     * Get exceptions list.
     *
     * @return  array
     */
    public function getExceptions()
    {
        return $this->_exceptions;
    }

    /**
     * Iterator over exceptions.
     *
     * @return  \Hoa\Iterator\Map
     */
    public function getIterator()
    {
        return new Iterator\Map(array_keys($this->getExceptions()));
    }

    /**
     * Count number of exceptions.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->getExceptions());
    }
}
