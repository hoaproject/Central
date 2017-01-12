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

namespace Hoa\Tree;

use Hoa\Visitor;

/**
 * Class \Hoa\Tree\Generic.
 *
 * Here is an abstract tree.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Generic
    implements Visitor\Element,
               \Iterator,
               \SeekableIterator,
               \Countable
{
    /**
     * Node value.
     *
     * @var \Hoa\Tree\ITree\Node
     */
    protected $_value  = null;

    /**
     * List of childs.
     *
     * @var array
     */
    protected $_childs = [];


    /**
     * Build a node. It can be a root, a node or a leaf of course.
     *
     * @param   mixed   $value    Node value.
     */
    public function __construct($value = null)
    {
        $this->setValue($value);

        return;
    }

    /**
     * Set the node value.
     *
     * @param   mixed   $value    Node value.
     * @return  mixed
     */
    public function setValue($value)
    {
        if (!($value instanceof ITree\Node)) {
            $value = new SimpleNode(md5($value), $value);
        }

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get the node value.
     *
     * @return  \Hoa\Tree\ITree\Node
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Get the current child for the iterator.
     *
     * @return  \Hoa\Tree\Generic
     */
    public function current()
    {
        return current($this->_childs);
    }

    /**
     * Get the current child id for the iterator.
     *
     * @return  int
     */
    public function key()
    {
        return key($this->_childs);
    }

    /**
     * Advance the internal child pointer, and return the current child.
     *
     * @return  \Hoa\Tree\Generic
     */
    public function next()
    {
        return next($this->_childs);
    }

    /**
     * Rewind the internal child pointer, and return the first child.
     *
     * @return  \Hoa\Tree\Generic
     */
    public function rewind()
    {
        return reset($this->_childs);
    }

    /**
     * Check if there is a current element after calls to the rewind or the next
     * methods.
     *
     * @return  bool
     */
    public function valid()
    {
        if (empty($this->_collection)) {
            return false;
        }

        $key    = key($this->_collection);
        $return = (bool) next($this->_childs);
        prev($this->_collection);

        if (false === $return) {
            end($this->_childs);
            if ($key === key($this->_childs)) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Seek to a position.
     *
     * @param   mixed   $position    Position to seek.
     * @return  void
     */
    public function seek($position)
    {
        if (!array_key_exists($position, $this->_collection)) {
            return;
        }

        $this->rewind();

        while ($position != $this->key()) {
            $this->next();
        }

        return;
    }

    /**
     * Count number of elements in collection.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->_childs);
    }

    /**
     * Get a specific child.
     *
     * @param   mixed   $nodeId    Node ID.
     * @return  \Hoa\Tree\Generic
     * @throws  \Hoa\Tree\Exception
     */
    public function getChild($nodeId)
    {
        if (false === $this->childExists($nodeId)) {
            throw new Exception('Child %s does not exist.', 0, $nodeId);
        }

        return $this->_childs[$nodeId];
    }

    /**
     * Get all childs.
     *
     * @return  array
     */
    public function getChilds()
    {
        return $this->_childs;
    }

    /**
     * Check if a child exists.
     *
     * @param   mixed   $nodeId    Node ID.
     * @return  bool
     */
    public function childExists($nodeId)
    {
        return array_key_exists($nodeId, $this->getChilds());
    }

    /**
     * Insert a child.
     * Fill the child list from left to right.
     *
     * @param   \Hoa\Tree\Generic  $child    Child to insert.
     * @return  \Hoa\Tree\Generic
     * @throws  \Hoa\Tree\Exception
     */
    abstract public function insert(Generic $child);

    /**
     * Delete a child.
     *
     * @param   int     $i    Child index.
     * @return  \Hoa\Tree\Generic
     * @throws  \Hoa\Tree\Exception
     */
    abstract public function delete($i);

    /**
     * Check if the node is a leaf.
     *
     * @return  bool
     */
    abstract public function isLeaf();

    /**
     * Check if the node is a node (i.e. not a leaf).
     *
     * @return  bool
     */
    abstract public function isNode();

    /**
     * Accept a visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle     Handle (reference).
     * @param   mixed               $eldna      Handle (not reference).
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
