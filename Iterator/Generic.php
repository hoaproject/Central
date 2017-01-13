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

namespace Hoa\Graph\Iterator;

use Hoa\Graph;

/**
 * Class \Hoa\Graph\Iterator\Generic.
 *
 * Generic parent for all graph iterators. It simply implements the constructor
 * to, given a specific graph and a starting node, iterators will be able to
 * run. The iterator interface must be implemented in the children classes.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Generic
{
    /**
     * Graph to iterate over.
     *
     * @var \Hoa\Graph
     */
    protected $_graph = null;

    /**
     * Starting node.
     *
     * @var \Hoa\Graph\Node
     */
    protected $_node  = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Graph       $graph    Graph.
     * @param   \Hoa\Graph\Node  $node     Starting node.
     * @throws  \Hoa\Graph\Exception
     */
    public function __construct(Graph $graph, Graph\Node $node)
    {
        $this->_graph = $graph;

        if (false === $this->_graph->nodeExists($node->getNodeId())) {
            throw new Graph\Exception(
                'The starting node %s does not exist in the graph, ' .
                'cannot iterate over it with %s.',
                0,
                [$node->getNodeId(), get_class($this)]
            );
        }

        $this->_node = $node;

        return;
    }

    /**
     * Get graph.
     *
     * @return  \Hoa\Graph
     */
    public function getGraph()
    {
        return $this->_graph;
    }

    /**
     * Get starting node.
     *
     * @return  \Hoa\Graph\Node
     */
    public function getStartingNode()
    {
        return $this->_node;
    }

    /**
     * Get neighbours of a specific node.
     * For instance, by default, it might be the children, but in a backward
     * iterator it might be the parents.
     *
     * @return  array
     */
    abstract public function getNeighbours(Graph\Node $node);
}
