<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Graph
 */
-> import('Graph.~')

/**
 * \Hoa\Graph\Exception
 */
-> import('Graph.Exception')

/**
 * \Hoa\Graph\IGraph\Node
 */
-> import('Graph.I~.Node');

}

namespace Hoa\Graph {

/**
 * Class \Hoa\Graph\AdjacencyList.
 *
 * Code an adjacency list graph.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class AdjacencyList extends Graph {

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
     * @throw   \Hoa\Graph\Exception
     */
    public static function getInstance ( $type = parent::TYPE_ADJACENCYLIST ) {

        throw new Exception(
            'Cannot get a new from a typped graph.', 0);
    }

    /**
     * Add a node.
     *
     * @access  public
     * @param   \Hoa\Graph\IGraph\Node  $node      Node to add.
     * @param   mixed                   $parent    Parent of node.
     * @return  void
     * @throw   \Hoa\Graph\Exception
     */
    public function addNode ( IGraph\Node $node,
                              $parent = array() ) {

        if(!is_array($parent))
            $parent = array($parent);

        if(parent::DISALLOW_LOOP === $this->isLoopAllow()) {

            if(true === $this->nodeExists($node->getNodeId()))
                throw new Exception(
                    'Node %s already exists.', 1, $node->getNodeId());

            if(in_array($node->getNodeId(), $parent))
                throw new Exception(
                    'Node %s cannot define itself in parent.', 2,
                    $node->getNodeId());
        }

        $this->nodes[$node->getNodeId()][self::NODE_VALUE] = $node;

        if(!isset($this->nodes[$node->getNodeId()][self::NODE_CHILD]))
            $this->nodes[$node->getNodeId()][self::NODE_CHILD] = array();

        foreach($parent as $foo => $nodeId) {

            if($nodeId instanceof IGraph\Node)
                $nodeId = $nodeId->getNodeId();

            if(parent::DISALLOW_LOOP === $this->isLoopAllow())
                if(false === $this->nodeExists($nodeId))
                    throw new Exception(
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

        if($nodeId instanceof IGraph\Node)
            $nodeId = $nodeId->getNodeId();

        return isset($this->nodes[$nodeId]);
    }

    /**
     * Get a node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   \Hoa\Graph\Exception
     */
    public function getNode ( $nodeId ) {

        if($nodeId instanceof IGraph\Node)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Exception(
                'Node %s does not exist.', 4, $nodeId);

        return $this->nodes[$nodeId][self::NODE_VALUE];
    }

    /**
     * Get parent of a specific node.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  object
     * @throw   \Hoa\Graph\Exception
     */
    public function getParent ( $nodeId ) {

        if($nodeId instanceof IGraph\Node)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Exception(
                'Node %s does not exist.', 5, $nodeId);

        $parent = new \ArrayObject(
            array(), \ArrayObject::ARRAY_AS_PROPS, 'ArrayIterator');

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
     * @throw   \Hoa\Graph\Exception
     */
    public function getChild ( $nodeId ) {

        if($nodeId instanceof IGraph\Node)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Exception(
                'Node %s does not exist.', 6, $nodeId);

        $child = new \ArrayObject(
            array(), \ArrayObject::ARRAY_AS_PROPS, 'ArrayIterator');

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
     * @throw   \Hoa\Graph\Exception
     */
    public function deleteNode ( $nodeId, $propagate = parent::DELETE_RESTRICT ) {

        if($nodeId instanceof IGraph\Node)
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
                throw new Exception(
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
     * @throw   \Hoa\Graph\Exception
     */
    public function isLeaf ( $nodeId ) {

        if($nodeId instanceof IGraph\Node)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Exception(
                'Node %s does not exist.', 8, $nodeId);

        return empty($this->nodes[$nodeId][self::NODE_CHILD]);
    }

    /**
     * Whether node is a root, i.e. does not have any parent.
     *
     * @access  public
     * @param   mixed   $nodeId    The node ID or the node instance.
     * @return  bool
     * @throw   \Hoa\Graph\Exception
     */
    public function isRoot ( $nodeId ) {

        if($nodeId instanceof IGraph\Node)
            $nodeId = $nodeId->getNodeId();

        if(false === $this->nodeExists($nodeId))
            throw new Exception(
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

}
