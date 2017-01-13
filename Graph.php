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

namespace Hoa\Graph;

use Hoa\Consistency;
use Hoa\Iterator;

/**
 * Class \Hoa\Graph.
 *
 * Abstract graph. One or more implementation of a graph may exists but the
 * public API remains the same.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Graph implements Iterator\Aggregate
{
    /**
     * Allow loop inside the graph.
     *
     * @const bool
     */
    const ALLOW_LOOP      = true;

    /**
     * Disallow loop inside the graph.
     *
     * @const bool
     */
    const DISALLOW_LOOP   = false;

    /**
     * Propagate node delete to its children.
     *
     * @const bool
     */
    const DELETE_CASCADE  = true;

    /**
     * Restrict node delete to the current node.
     *
     * @const bool
     */
    const DELETE_RESTRICT = false;

    /**
     * All nodes.
     *
     * @var array
     */
    protected $_nodes = [];

    /**
     * Whether loops are allowed in the graph.
     *
     * @var bool
     */
    protected $_loop  = self::DISALLOW_LOOP;



    /**
     * Get an empty graph.
     *
     * @param   bool  $loop    Whether loops are allowed.
     */
    public function __construct($loop = self::DISALLOW_LOOP)
    {
        $this->allowLoop($loop);

        return;
    }

    /**
     * Add a node.
     *
     * @param   \Hoa\Graph\Node  $node       Node to add.
     * @param   array            $parents    Parents.
     * @return  \Hoa\Graph\Graph
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function addNode(Node $node, array $parents = []);

    /**
     * Check if a node does already exist or not.
     *
     * @param   mixed   $nodeId    Node ID.
     * @return  bool
     */
    abstract public function nodeExists($nodeId);

    /**
     * Get a node.
     *
     * @param   mixed   $nodeId    Node ID.
     * @return  \Hoa\Graph\Node
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function getNode($nodeId);

    /**
     * Get all nodes.
     *
     * @return  array
     */
    protected function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * Get parents of a specific node.
     *
     * @param   \Hoa\Graph\Node   $node    Node.
     * @return  array
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function getParents(Node $node);

    /**
     * Get children of a specific node.
     *
     * @param   \Hoa\Graph\Node   $node    Node.
     * @return  array
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function getChildren(Node $node);

    /**
     * Delete a node.
     *
     * @param   \Hoa\Graph\Node  $node         Node.
     * @param   bool             $propagate    Propagate the erasure.
     * @return  \Hoa\Graph\Graph
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function deleteNode(Node $node, $propagate = self::DELETE_RESTRICT);

    /**
     * Whether node is a leaf, i.e. if it does not have any child.
     *
     * @param   \Hoa\Graph\Node  $node    Node.
     * @return  bool
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function isLeaf(Node $node);

    /**
     * Whether node is a root, i.e. if it does not have any parent.
     *
     * @param   \Hoa\Graph\Node  $node    Node.
     * @return  bool
     * @throws  \Hoa\Graph\Exception
     */
    abstract public function isRoot(Node $node);

    /**
     * Set the loop mode (see `self::ALLOW_LOOP` and `self::DISALLOW_LOOP`).
     *
     * @param   bool  $loop    Whether loops are allowed.
     * @return  bool
     */
    public function allowLoop($loop = self::ALLOW_LOOP)
    {
        $old         = $this->_loop;
        $this->_loop = $loop;

        return $old;
    }

    /**
     * Whether loops are allowed or not.
     *
     * @return  bool
     */
    public function isLoopAllowed()
    {
        return $this->_loop;
    }

    /**
     * Print the graph in the DOT language.
     *
     * @return  string
     */
    abstract public function __toString();
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Graph\Graph');
