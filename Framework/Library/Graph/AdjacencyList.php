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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Graph
 * @subpackage  Hoa_Graph_AdjacencyList
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Graph
 */
import('Graph.~');

/**
 * Hoa_Graph_Exception
 */
import('Graph.Exception');

/**
 * Hoa_Graph_Node_Interface
 */
import('Graph.Node.Interface');

/**
 * Class Hoa_Graph_AdjacencyList.
 *
 * Code an adjacency list graph.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Graph
 * @subpackage  Hoa_Graph_AdjacencyList
 */

class Hoa_Graph_AdjacencyList extends Hoa_Graph {

    /**
     * Node value index.
     *
     * @const int
     */
    const NODE_VALUE = 0;
    
    /**
     * Node child index.
     *
     * @const int
     */
    const NODE_CHILD = 1;



    /**
     * Propagate the construction.
     *
     * @access  public
     * @param   bool    $loop    Allow or not loop.
     * @return  void
     */
    public function __construct ( $loop = parent::DISALLOW_LOOP ) {

        parent::__construct($loop);
    }

    /**
     * Disallow to get a new instance.
     *
     * @access  public
     * @param   string  $type    Type of graph needed.
     * @return  void
     * @throw   Hoa_Graph_Exception
     */
    public static function getInstance ( $type = parent::TYPE_ADJACENCYLIST ) {

        throw new Hoa_Graph_Exception(
            'Cannot get a new from a typped graph.', 0);
    }

    /**
     * Add a node.
     *
     * @access  public
     * @param   Hoa_Graph_Node_Interface  $node      Node to add.
     * @param   mixed                     $parent    Parent of node.
     * @return  void
     * @throw   Hoa_Graph_Exception
     */
    public function addNode ( Hoa_Graph_Node_Interface $node,
                              $parent = array() ) {

        if(!is_array($parent))
            $parent = array($parent);

        if(parent::DISALLOW_LOOP === $this->isLoopAllow()) {

            if(true === $this->nodeExists($node->getNodeId()))
                throw new Hoa_Graph_Exception(
                    'Node %s already exists.', 1, $node->getNodeId());

            if(in_array($node->getNodeId(), $parent))
                throw new Hoa_Graph_Exception(
                    'Node %s cannot define itself in parent.', 2,
                    $node->getNodeId());
        }

        $this->nodes[$node->getNodeId()][self::NODE_VALUE] = $node;

        if(!isset($this->nodes[$node->getNodeId()][self::NODE_CHILD]))
            $this->nodes[$node->getNodeId()][self::NODE_CHILD] = array();

        foreach($parent as $foo => $nodeId) {

            if($nodeId instanceof Hoa_Graph_Node_Interface)
                $nodeId = $nodeId->getNodeId();

            if(parent::DISALLOW_LOOP === $this->isLoopAllow())
                if(false === $this->nodeExists($nodeId))
                    throw new Hoa_Graph_Exception(
                        'Node %s does not exist.', 3, $nodeId);

            $this->nodes[$nodeId][self::NODE_CHILD][] = $node->getNodeId();
        }
    }

    /**
     * Check if a node does already exist or not.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     */
    public function nodeExists ( $nodeId ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        return isset($this->nodes[$nodeId]);
    }

    /**
     * Get a node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   Hoa_Graph_Exception
     */
    public function getNode ( $nodeId ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Hoa_Graph_Exception(
                'Node %s does not exist.', 4, $nodeId);

        return $this->nodes[$nodeId][self::NODE_VALUE];
    }

    /**
     * Get parent of a specific node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   Hoa_Graph_Exception
     */
    public function getParent ( $nodeId ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Hoa_Graph_Exception(
                'Node %s does not exist.', 5, $nodeId);

        $parent = new ArrayObject(
            array(), ArrayObject::ARRAY_AS_PROPS, 'ArrayIterator');

        foreach($this->getNodes() as $id => $values ) {

            if(parent::DISALLOW_LOOP === $this->isLoopAllow())
                if($nodeId == $id)
                    continue;

            if(in_array($nodeId, $values[self::NODE_CHILD]))
                $parent->offsetSet(
                    $id,
                    $this->getNode($id)
                );
        }

        return $parent;
    }

    /**
     * Get child of a specific node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   Hoa_Graph_Exception
     */
    public function getChild ( $nodeId ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Hoa_Graph_Exception(
                'Node %s does not exist.', 6, $nodeId);

        $child = new ArrayObject(
            array(), ArrayObject::ARRAY_AS_PROPS, 'ArrayIterator');

        foreach($this->nodes[$nodeId][self::NODE_CHILD] as $foo => $id)
            $child->offsetSet(
                $id,
                $this->getNode($id)
            );

        return $child;
    }

    /**
     * Delete a node.
     *
     * @access  public
     * @param   mixed   $nodeId       The node ID or the node instance.
     * @param   bool    $propagate    Propagate the erasure.
     * @return  void
     * @throw   Hoa_Graph_Exception
     */
    public function deleteNode ( $nodeId, $propagate = parent::DELETE_RESTRICT ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            return;

        if($propagate == parent::DELETE_RESTRICT)

            if(empty($this->nodes[$nodeId][self::NODE_CHILD])) {

                unset($this->nodes[$nodeId]);

                foreach($this->getNodes() as $id => $values )
                    if(false !== $key = array_search($nodeId, $values[self::NODE_CHILD]))
                        unset($this->nodes[$id][self::NODE_CHILD][$key]);
            }
            else
                throw new Hoa_Graph_Exception(
                    'Cannot delete %s node in restrict delete mode, because ' .
                    'it has one or more children.', 7, $nodeId);
        else {

            foreach($this->nodes[$nodeId][self::NODE_CHILD] as $foo => $id)
                $this->deleteNode($id, $propagate);

            $this->deleteNode($nodeId, parent::DELETE_RESTRICT);
        }
    }

    /**
     * Whether node is a leaf, i.e. does not have any child.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     * @throw   Hoa_Graph_Exception
     */
    public function isLeaf ( $nodeId ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Hoa_Graph_Exception(
                'Node %s does not exist.', 8, $nodeId);

        return empty($this->nodes[$nodeId][self::NODE_CHILD]);
    }

    /**
     * Whether node is a root, i.e. does not have any parent.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     * @throw   Hoa_Graph_Exception
     */
    public function isRoot ( $nodeId ) {

        if($nodeId instanceof Hoa_Graph_Node_Interface)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Hoa_Graph_Exception(
                'Node %s does not exist.', 9, $nodeId);

        return count($this->getParent($nodeId)) == 0;
    }

    /**
     * Print the graph in the DOT language.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = 'digraph {' . "\n";

        foreach($this->getNodes() as $nodeId => $foo)
            $out .= '    ' . $nodeId . ';' . "\n";

        foreach($this->getNodes() as $nodeId => $foo)
            foreach($this->getChild($nodeId) as $fooo => $child)
                $out .= '    ' . $nodeId . ' -> ' . $child->getNodeId() . ';' .  "\n";

        $out .= '}';

        return $out;
    }
}
