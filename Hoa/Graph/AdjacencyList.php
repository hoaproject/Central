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

use Hoa\Iterator;

/**
 * Class \Hoa\Graph\AdjacencyList.
 *
 * Graph implementation using an adjacency list.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class AdjacencyList extends Graph
{
    /**
     * Node value index.
     *
     * @const int
     */
    const NODE_VALUE    = 0;

    /**
     * Node child index.
     *
     * @const int
     */
    const NODE_CHILDREN = 1;



    /**
     * Add a node.
     *
     * @param   \Hoa\Graph\Node  $node       Node to add.
     * @param   array            $parents    Parents.
     * @return  \Hoa\Graph\Graph
     * @throws  \Hoa\Graph\Exception
     */
    public function addNode(Node $node, array $parents = [])
    {
        $id = $node->getNodeId();

        if (parent::DISALLOW_LOOP === $this->isLoopAllowed()) {
            if (true === $this->nodeExists($id)) {
                throw new Exception(
                    'Node %s already exists, cannot re-add it ' .
                    '(loop is not allowed).',
                    0,
                    $id
                );
            }

            if (true === in_array($node, $parents)) {
                throw new Exception(
                    'Reflexive node is not allowed, tried to add %s.',
                    1,
                    $id
                );
            }
        }

        if (!isset($this->_nodes[$id])) {
            $this->_nodes[$id] = [
                self::NODE_VALUE    => null,
                self::NODE_CHILDREN => []
            ];
        }

        $this->_nodes[$id][self::NODE_VALUE] = $node;

        foreach ($parents as $parent) {
            if (!($parent instanceof Node)) {
                throw new Exception(
                    'Parent %s must be an instance of Hoa\Graph\Node.',
                    2,
                    $parent
                );
            }

            $parentId = $parent->getNodeId();

            if (false === $this->nodeExists($parentId)) {
                throw new Exception(
                    'Cannot use %s as a parent node of %s ' .
                    'because it does exists.',
                    3,
                    [$parentId, $id]
                );
            }

            $this->_nodes[$parentId][self::NODE_CHILDREN][] = $id;
        }

        return $this;
    }

    /**
     * Check if a node does already exist or not.
     *
     * @param   mixed   $nodeId    Node ID.
     * @return  bool
     */
    public function nodeExists($nodeId)
    {
        if ($nodeId instanceof Node) {
            $nodeId = $nodeId->getNodeId();
        }

        return isset($this->_nodes[$nodeId]);
    }

    /**
     * Get a node.
     *
     * @param   mixed   $nodeId    Node ID.
     * @return  \Hoa\Graph\Node
     * @throws  \Hoa\Graph\Exception
     */
    public function getNode($nodeId)
    {
        if (false === $this->nodeExists($nodeId)) {
            throw new Exception(
                'Node %s does not exist, cannot get it.',
                4,
                $nodeId
            );
        }

        return $this->_nodes[$nodeId][self::NODE_VALUE];
    }

    /**
     * Get parents of a specific node.
     *
     * @param   \Hoa\Graph\Node   $node    Node.
     * @return  array
     * @throws  \Hoa\Graph\Exception
     */
    public function getParents(Node $node)
    {
        $id = $node->getNodeId();

        if (false === $this->nodeExists($id)) {
            throw new Exception(
                'Node %s does not exist, cannot get its parents.',
                5,
                $id
            );
        }

        $parents = [];

        foreach ($this->getNodes() as $nodeId => $nodeBucket) {
            if (true === in_array($id, $nodeBucket[self::NODE_CHILDREN])) {
                $parents[$nodeId] = $this->getNode($nodeId);
            }
        }

        return $parents;
    }

    /**
     * Get children of a specific node.
     *
     * @param   \Hoa\Graph\Node   $node    Node.
     * @return  array
     * @throws  \Hoa\Graph\Exception
     */
    public function getChildren(Node $node)
    {
        $id = $node->getNodeId();

        if (false === $this->nodeExists($id)) {
            throw new Exception(
                'Node %s does not exist, cannot get its children.',
                6,
                $id
            );
        }

        $children = [];

        foreach ($this->_nodes[$id][self::NODE_CHILDREN] as $childId) {
            $children[$childId] = $this->getNode($childId);
        }

        return $children;
    }

    /**
     * Delete a node.
     *
     * @param   \Hoa\Graph\Node  $node         Node.
     * @param   bool             $propagate    Propagate the erasure.
     * @return  \Hoa\Graph\Graph
     * @throws  \Hoa\Graph\Exception
     */
    public function deleteNode(Node $node, $propagate = self::DELETE_RESTRICT)
    {
        $id = $node->getNodeId();

        if (false === $this->nodeExists($id)) {
            return $this;
        }

        if (parent::DELETE_RESTRICT === $propagate) {
            if (!empty($this->_nodes[$id][self::NODE_CHILDREN])) {
                throw new Exception(
                    'Cannot delete %s node in restrict delete mode, because ' .
                    'it has one or more children.',
                    7,
                    $id
                );
            }

            foreach ($this->getParents($node) as $parentId => $parent) {
                unset(
                    $this->_nodes
                        [$parentId]
                        [self::NODE_CHILDREN]
                        [array_search($id, $this->_nodes[$parentId][self::NODE_CHILDREN])]
                );
            }

            unset($this->_nodes[$id]);

            return $this;
        }

        foreach ($this->getChildren($node) as $child) {
            if ($node === $child) {
                unset(
                    $this->_nodes
                        [$id]
                        [self::NODE_CHILDREN]
                        [array_search($id, $this->_nodes[$id][self::NODE_CHILDREN])]
                );
            }

            $this->deleteNode($child, $propagate);
        }

        return $this->deleteNode($node, parent::DELETE_RESTRICT);
    }

    /**
     * Whether node is a leaf, i.e. if it does not have any child.
     *
     * @param   \Hoa\Graph\Node  $node    Node.
     * @return  bool
     * @throws  \Hoa\Graph\Exception
     */
    public function isLeaf(Node $node)
    {
        $id = $node->getNodeId();

        if (false === $this->nodeExists($id)) {
            throw new Exception(
                'Node %s does not exist, ' .
                'cannot check if this is a leaf or not.',
                8,
                $id
            );
        }

        return empty($this->_nodes[$id][self::NODE_CHILDREN]);
    }

    /**
     * Whether node is a root, i.e. if it does not have any parent.
     *
     * @param   \Hoa\Graph\Node  $node    Node.
     * @return  bool
     * @throws  \Hoa\Graph\Exception
     */
    public function isRoot(Node $node)
    {
        $id = $node->getNodeId();

        if (false === $this->nodeExists($id)) {
            throw new Exception(
                'Node %s does not exist, ' .
                'cannot check if this is a root or not.',
                9,
                $id
            );
        }

        return 0 === count($this->getParents($node));
    }

    /**
     * Iterator over all nodes ordered by declarations.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->getNodes() as $nodeId => $nodeBucket) {
            yield $nodeId => $nodeBucket[self::NODE_VALUE];
        }
    }

    /**
     * Print the graph in the DOT language.
     *
     * @return  string
     */
    public function __toString()
    {
        $out = 'digraph {' . "\n";

        foreach ($this->getNodes() as $nodeId => $_) {
            $out .= '    ' . $nodeId . ';' . "\n";
        }

        foreach ($this->getNodes() as $nodeId => $node) {
            foreach ($this->getChildren($node[self::NODE_VALUE]) as $childId => $child) {
                $out .= '    ' . $nodeId . ' -> ' . $childId . ';' . "\n";
            }
        }

        $out .= '}';

        return $out;
    }
}
