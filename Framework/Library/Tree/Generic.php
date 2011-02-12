<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace {

from('Hoa')

/**
 * \Hoa\Tree\Exception
 */
-> import('Tree.Exception')

/**
 * \Hoa\Tree\ITree\Node
 */
-> import('Tree.I~.Node')

/**
 * \Hoa\Tree\SimpleNode
 */
-> import('Tree.SimpleNode')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Tree {

/**
 * Class \Hoa\Tree\Generic.
 *
 * Here is an abstract tree.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Generic
    implements \Hoa\Visitor\Element,
               \Iterator,
               \SeekableIterator,
               \Countable {

    /**
     * Node value.
     *
     * @var \Hoa\Tree\ITree\Node object
     */
    protected $_value  = null;

    /**
     * List of childs.
     *
     * @var \Hoa\Tree\Generic array
     */
    protected $_childs = array();


    /**
     * Build a node. It can be a root, a node or a leaf of course.
     *
     * @access  public
     * @param   mixed   $value    Node value.
     * @return  void
     */
    public function __construct ( $value = null ) {

        $this->setValue($value);

        return;
    }

    /**
     * Set the node value.
     *
     * @access  public
     * @param   mixed   $value    Node value.
     * @return  mixed
     */
    public function setValue ( $value ) {

        if(!($value instanceof \Hoa\Tree\ITree\Node))
            $value    = new \Hoa\Tree\SimpleNode(md5($value), $value);

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get the node value.
     *
     * @access  public
     * @return  \Hoa\Tree\ITree\Node
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get the current child for the iterator.
     *
     * @access  public
     * @return  \Hoa\Tree\Generic
     */
    public function current ( ) {

        return current($this->_childs);
    }

    /**
     * Get the current child id for the iterator.
     *
     * @access  public
     * @return  int
     */
    public function key ( ) {

        return key($this->_childs);
    }

    /**
     * Advance the internal child pointer, and return the current child.
     *
     * @access  public
     * @return  \Hoa\Tree\Generic
     */
    public function next ( ) {

        return next($this->_childs);
    }

    /**
     * Rewind the internal child pointer, and return the first child.
     *
     * @access  public
     * @return  \Hoa\Tree\Generic
     */
    public function rewind ( ) {

        return reset($this->_childs);
    }

    /**
     * Check if there is a current element after calls to the rewind or the next
     * methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty($this->_collection))
            return false;

        $key    = key($this->_collection);
        $return = (bool) next($this->_childs);
        prev($this->_collection);

        if(false === $return) {

            end($this->_childs);
            if($key === key($this->_childs))
                $return = true;
        }

        return $return;
    }

    /**
     * Seek to a position.
     *
     * @access  public
     * @param   mixed   $position    Position to seek.
     * @return  void
     */
    public function seek ( $position ) {

        if(!array_key_exists($position, $this->_collection))
            return;

        $this->rewind();

        while($position != $this->key())
            $this->next();

        return;
    }

    /**
     * Count number of elements in collection.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_childs);
    }

    /**
     * Get a specific child.
     *
     * @access  public
     * @param   mixed   $nodeId    Node ID.
     * @return  \Hoa\Tree\Generic
     * @throw   \Hoa\Tree\Exception
     */
    public function getChild ( $nodeId ) {

        if(false === $this->childExists($nodeId))
            throw new Exception(
                'Child %s does not exist.', 0, $nodeId);

        return $this->_childs[$nodeId];
    }

    /**
     * Get all childs.
     *
     * @access  public
     * @return  array
     */
    public function getChilds ( ) {

        return $this->_childs;
    }

    /**
     * Check if a child exists.
     *
     * @access  public
     * @param   mixed   $nodeId    Node ID.
     * @return  bool
     */
    public function childExists ( $nodeId ) {

        return array_key_exists($nodeId, $this->getChilds());
    }

    /**
     * Insert a child.
     * Fill the child list from left to right.
     *
     * @access  public
     * @param   \Hoa\Tree\Generic  $child    Child to insert.
     * @return  \Hoa\Tree\Generic
     * @throw   \Hoa\Tree\Exception
     */
    abstract public function insert ( \Hoa\Tree\Generic $child );

    /**
     * Delete a child.
     *
     * @access  public
     * @param   int     $i    Child index.
     * @return  \Hoa\Tree\Generic
     * @throw   \Hoa\Tree\Exception
     */
    abstract public function delete ( $i );

    /**
     * Check if the node is a leaf.
     *
     * @access  public
     * @return  bool
     */
    abstract public function isLeaf ( );

    /**
     * Check if the node is a node (i.e. not a leaf).
     *
     * @access  public
     * @return  bool
     */
    abstract public function isNode ( );

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
