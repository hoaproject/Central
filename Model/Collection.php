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
 * \Hoa\Praspel\Exception\Model
 */
-> import('Praspel.Exception.Model')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Praspel\Model {

/**
 * Class \Hoa\Praspel\Model\Collection.
 *
 * Represent a collection of clauses.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class          Collection
    implements \Hoa\Visitor\Element,
               \ArrayAccess,
               \IteratorAggregate {

    /**
     * Clauses.
     *
     * @var \Hoa\Praspel\Model\Collection array
     */
    protected $_clauses   = array();

    /**
     * Reference clause.
     *
     * @var \Hoa\Praspel\Model\Clause object
     */
    protected $_clause    = null;

    /**
     * Post-clone function.
     *
     * @var \Closure closure
     */
    protected $_postClone = null;



    /**
     * Build a collection of clauses.
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Clause  $clause       Clause.
     * @param   \Closure                   $postClone    Post-clone function.
     * @return  void
     */
    public function __construct ( Clause   $clause,
                                  \Closure $postClone = null ) {

        $this->_clause    = $clause;
        $this->_postClone = $postClone;

        return;
    }

    /**
     * Check whether an offset exists.
     *
     * @access  public
     * @param   string  $offset    Offset.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return false !== array_key_exists($offset, $this->_clauses);
    }

    /**
     * Get a clause.
     *
     * @access  public
     * @param   string  $offset    Offset.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset)) {

            $clause                  = $this->getClause();
            $this->_clauses[$offset] = clone $clause;
            $postClone               = $this->getPostClone();

            if(null !== $postClone)
                $postClone($this->_clauses[$offset], $offset);
        }

        return $this->_clauses[$offset];
    }

    /**
     * Alias of $this->offsetGet($offset).
     *
     * @access  public
     * @param   string  $offset    Offset.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function get ( $offset ) {

        return $this->offsetGet($offset);
    }

    /**
     * Disabled.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Praspel\Exception\Model
     */
    public function offsetSet ( $offset, $value ) {

        throw new \Hoa\Praspel\Exception\Model('Operation denied.', 0);
    }

    /**
     * Disabled.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Praspel\Exception\Model
     */
    public function offsetUnset ( $offset ) {

        throw new \Hoa\Praspel\Exception\Model('Operation denied.', 0);
    }

    /**
     * Get reference clause.
     *
     * @access  protected
     * @return  \Hoa\Praspel\Model\Clause
     */
    protected function getClause ( ) {

        return $this->_clause;
    }

    /**
     * Get post-clone function.
     *
     * @access  protected
     * @return  \Closure
     */
    protected function getPostClone ( ) {

        return $this->_postClone;
    }

    /**
     * Iterate over all clauses.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        return new \ArrayIterator($this->_clauses);
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
