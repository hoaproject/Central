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
use Hoa\Praspel;
use Hoa\Visitor;

/**
 * Class \Hoa\Praspel\Model\Collection.
 *
 * Represent a collection of clauses.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          Collection
    implements Visitor\Element,
               Iterator\Aggregate,
               \ArrayAccess,
               \Countable
{
    /**
     * Clauses.
     *
     * @var array
     */
    protected $_clauses   = [];

    /**
     * Mapping from position to clauses (instead of identifier).
     *
     * @var array
     */
    protected $_mapping   = [];

    /**
     * Reference clause.
     *
     * @var \Hoa\Praspel\Model\Clause
     */
    protected $_clause    = null;

    /**
     * Post-clone function.
     *
     * @var closure
     */
    protected $_postClone = null;



    /**
     * Build a collection of clauses.
     *
     * @param   \Hoa\Praspel\Model\Clause  $clause       Clause.
     * @param   \Closure                   $postClone    Post-clone function.
     */
    public function __construct(Clause $clause, \Closure $postClone = null)
    {
        $this->_clause    = $clause;
        $this->_postClone = $postClone;

        return;
    }

    /**
     * Check whether an offset exists.
     *
     * @param   string  $offset    Offset.
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return false !== array_key_exists($offset, $this->_clauses);
    }

    /**
     * Get a clause.
     *
     * @param   string  $offset    Offset.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function offsetGet($offset)
    {
        if (false === $this->offsetExists($offset)) {
            $clause                  = $this->getClause();
            $this->_clauses[$offset] = clone $clause;
            $this->_mapping[]        = &$this->_clauses[$offset];
            $postClone               = $this->getPostClone();

            if (null !== $postClone) {
                $postClone($this->_clauses[$offset], $offset);
            }
        }

        return $this->_clauses[$offset];
    }

    /**
     * Alias of $this->offsetGet($offset).
     *
     * @param   string  $identifier    Identifier.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Get a clause from its position.
     *
     * @param   string  $position    Position.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function getNth($position)
    {
        if (!isset($this->_mapping[$position])) {
            return null;
        }

        return $this->_mapping[$position];
    }

    /**
     * Disabled.
     *
     * @return  void
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function offsetSet($offset, $value)
    {
        throw new Praspel\Exception\Model('Operation denied.', 0);
    }

    /**
     * Disabled.
     *
     * @return  void
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function offsetUnset($offset)
    {
        throw new Praspel\Exception\Model('Operation denied.', 1);
    }

    /**
     * Get reference clause.
     *
     * @return  \Hoa\Praspel\Model\Clause
     */
    protected function getClause()
    {
        return $this->_clause;
    }

    /**
     * Get post-clone function.
     *
     * @return  \Closure
     */
    protected function getPostClone()
    {
        return $this->_postClone;
    }

    /**
     * Iterate over all clauses.
     *
     * @return  \Hoa\Iterator\Map
     */
    public function getIterator()
    {
        return new Iterator\Map($this->_clauses);
    }

    /**
     * Count number of clauses.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->_clauses);
    }

    /**
     * Accept a visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept(
        Visitor\Visit $visitor,
        &$handle = null,
        $eldnah  = null
    ) {
        return $visitor->visit($this, $handle, $eldnah);
    }
}
