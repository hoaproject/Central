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
 * \Hoa\Praspel\Model\Clause
 */
-> import('Praspel.Model.Clause')

/**
 * \Hoa\Iterator\Aggregate
 */
-> import('Iterator.Aggregate')

/**
 * \Hoa\Iterator\Map
 */
-> import('Iterator.Map');

}

namespace Hoa\Praspel\Model {

/**
 * Class \Hoa\Praspel\Model\Throwable.
 *
 * Represent the @throwable clause.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class          Throwable
    extends    Clause
    implements \Hoa\Iterator\Aggregate,
               \ArrayAccess,
               \Countable {

    /**
     * Name.
     *
     * @const string
     */
    const NAME = 'throwable';

    /**
     * List of exception names.
     *
     * @var \Hoa\Praspel\Model\Throwable array
     */
    protected $_exceptions = array();



    /**
     * Check if an exception identifier exists.
     *
     * @access  public
     * @param   string  $offset    Exception identifier.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return isset($this->_exceptions[$offset]);
    }

    /**
     * Get an exception.
     *
     * @access  public
     * @param   string  $offset    Exception identifier.
     * @return  \Hoa\Prasel\Model\Variable
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset))
            return null;

        return $this->_exceptions[$offset];
    }

    /**
     * Add an exception.
     *
     * @access  public
     * @param   string  $offset    Exception identifier.
     * @param   mixed   $value     Exception classname.
     * @return  mixed
     */
    public function offsetSet ( $offset, $value ) {

        $old                        = $this->offsetGet($offset);
        $this->_exceptions[$offset] = $value;

        return $old;
    }

    /**
     * Delete an exception.
     *
     * @access  public
     * @param   string  $offset    Exception identifier.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        unset($this->_exceptions[$offset]);

        return;
    }

    /**
     * Get exceptions list.
     *
     * @access  public
     * @return  array
     */
    public function getExceptions ( ) {

        return $this->_exceptions;
    }

    /**
     * Iterator over exceptions.
     *
     * @access  public
     * @return  \Hoa\Iterator\Map
     */
    public function getIterator ( ) {

        return new \Hoa\Iterator\Map($this->getExceptions());
    }

    /**
     * Count number of exceptions.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->getExceptions());
    }
}

}
