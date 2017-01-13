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

namespace Hoa\Graph\Test\Unit;

use Hoa\Graph\AdjacencyList as SUT;
use Hoa\Graph as LUT;
use Hoa\Test;

/**
 * Class \Hoa\Graph\Test\Unit\AdjacencyList.
 *
 * Test suite of the adjancency list graph implementation.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class AdjacencyList extends Graph
{
    public function case_add_node()
    {
        $this
            ->given(
                $graph = new SUT(),
                $node  = new LUT\SimpleNode('n1')
            )
            ->when($result = $graph->addNode($node))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEqualTo([
                        $node->getNodeId() => [
                            $graph::NODE_VALUE    => $node,
                            $graph::NODE_CHILDREN => []
                        ]
                    ]);
    }

    public function case_add_node_with_parents()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $graph->addNode($n1),
                $graph->addNode($n2)
            )
            ->when($result = $graph->addNode($n3, [$n1, $n2]))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEqualTo([
                        $n1->getNodeId() => [
                            $graph::NODE_VALUE    => $n1,
                            $graph::NODE_CHILDREN => ['n3']
                        ],
                        $n2->getNodeId() => [
                            $graph::NODE_VALUE    => $n2,
                            $graph::NODE_CHILDREN => ['n3']
                        ],
                        $n3->getNodeId() => [
                            $graph::NODE_VALUE    => $n3,
                            $graph::NODE_CHILDREN => []
                        ]
                    ]);
    }

    public function case_add_node_with_an_undefined_parent()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2')
            )
            ->exception(function () use ($graph, $n1, $n2) {
                $graph->addNode($n1, [$n2]);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_add_node_with_an_invalid_parent()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1')
            )
            ->exception(function () use ($graph, $n1) {
                $graph->addNode($n1, ['n2']);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_add_node_with_loop_and_loop_disallowed()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1])
            )
            ->exception(function () use ($graph, $n1, $n2) {
                $graph->addNode($n1, [$n2]);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_add_reflexive_node_with_loop_disallowed()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1')
            )
            ->exception(function () use ($graph, $n1) {
                $graph->addNode($n1, [$n1]);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_add_node_with_loop()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1])
            )
            ->when($result = $graph->addNode($n1, [$n2]))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEqualTo([
                        $n1->getNodeId() => [
                            $graph::NODE_VALUE    => $n1,
                            $graph::NODE_CHILDREN => ['n2']
                        ],
                        $n2->getNodeId() => [
                            $graph::NODE_VALUE    => $n2,
                            $graph::NODE_CHILDREN => ['n1']
                        ]
                    ]);
    }

    public function case_add_reflexive_node()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1')
            )
            ->when($result = $graph->addNode($n1, [$n1]))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEqualTo([
                        $n1->getNodeId() => [
                            $graph::NODE_VALUE    => $n1,
                            $graph::NODE_CHILDREN => ['n1']
                        ]
                    ]);
    }

    public function case_node_exists()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1)
            )
            ->when($result = $graph->nodeExists('n1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_node_does_not_exist()
    {
        $this
            ->given($graph = new SUT())
            ->when($result = $graph->nodeExists('n1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_node()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1)
            )
            ->when($result = $graph->getNode('n1'))
            ->then
                ->object($result)
                    ->isEqualTo($n1);
    }

    public function case_get_undefined_node()
    {
        $this
            ->given($graph = new SUT())
            ->exception(function () use ($graph) {
                $graph->getNode('n1');
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_get_parents()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $n4    = new LUT\SimpleNode('n4'),
                $n5    = new LUT\SimpleNode('n5'),
                $graph->addNode($n1),
                $graph->addNode($n2),
                $graph->addNode($n3, [$n2]),
                $graph->addNode($n4, [$n1, $n3]),
                $graph->addNode($n5, [$n1, $n2])
            )
            ->when($result = $graph->getParents($n4))
            ->then
                ->array($result)
                    ->isEqualTo([
                        'n1' => $n1,
                        'n3' => $n3
                    ]);
    }

    public function case_get_parents_of_an_undefined_node()
    {
        $this
            ->given(
                $graph = new SUT(),
                $node  = new LUT\SimpleNode('n1')
            )
            ->exception(function () use ($graph, $node) {
                $graph->getParents($node);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_get_parents_with_loop()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n1, [$n2])
            )
            ->when($result = $graph->getParents($n2))
            ->then
                ->array($result)
                    ->isEqualTo(['n1' => $n1]);
    }

    public function case_get_parents_with_reflexive_node()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1, [$n1])
            )
            ->when($result = $graph->getParents($n1))
            ->then
                ->array($result)
                    ->isEqualTo(['n1' => $n1]);
    }

    public function case_get_children()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $n4    = new LUT\SimpleNode('n4'),
                $n5    = new LUT\SimpleNode('n5'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n3, [$n2]),
                $graph->addNode($n4, [$n1, $n3]),
                $graph->addNode($n5)
            )
            ->when($result = $graph->getChildren($n1))
            ->then
                ->array($result)
                    ->isEqualTo([
                        'n2' => $n2,
                        'n4' => $n4
                    ]);
    }

    public function case_get_children_of_an_undefined_node()
    {
        $this
            ->given(
                $graph = new SUT(),
                $node  = new LUT\SimpleNode('n1')
            )
            ->exception(function () use ($graph, $node) {
                $graph->getChildren($node);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_get_children_with_loop()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n1, [$n2])
            )
            ->when($result = $graph->getChildren($n1))
            ->then
                ->array($result)
                    ->isEqualTo(['n2' => $n2]);
    }

    public function case_get_children_with_reflexive_node()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1, [$n1])
            )
            ->when($result = $graph->getChildren($n1))
            ->then
                ->array($result)
                    ->isEqualTo(['n1' => $n1]);
    }

    public function case_delete_node()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2)
            )
            ->when($result = $graph->deleteNode($n1))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEqualTo([
                        $n2->getNodeId() => [
                            $graph::NODE_VALUE    => $n2,
                            $graph::NODE_CHILDREN => []
                        ],
                    ]);
    }

    public function case_delete_node_with_parents()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n3, [$n1])
            )
            ->when($result = $graph->deleteNode($n3))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEqualTo([
                        $n1->getNodeId() => [
                            $graph::NODE_VALUE    => $n1,
                            $graph::NODE_CHILDREN => ['n2']
                        ],
                        $n2->getNodeId() => [
                            $graph::NODE_VALUE    => $n2,
                            $graph::NODE_CHILDREN => []
                        ],
                    ]);
    }

    public function case_delete_an_undefined_node()
    {
        $this
            ->given(
                $graph = new SUT(),
                $node  = new LUT\SimpleNode('n1')
            )
            ->when($result = $graph->deleteNode($node))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph);
    }

    public function case_delete_node_with_children_with_restricted_propagation()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n3, [$n1])
            )
            ->exception(function () use ($graph, $n1) {
                $graph->deleteNode($n1);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_delete_node_with_children()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $n4    = new LUT\SimpleNode('n4'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n3, [$n1]),
                $graph->addNode($n4, [$n3])
            )
            ->when($result = $graph->deleteNode($n1, $graph::DELETE_CASCADE))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEmpty();
    }

    public function case_delete_reflexive_node()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1, [$n1])
            )
            ->when($result = $graph->deleteNode($n1, $graph::DELETE_CASCADE))
            ->then
                ->object($result)
                    ->isIdenticalTo($graph)
                ->array($this->invoke($graph)->getNodes())
                    ->isEmpty();
    }

    public function case_is_leaf()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1)
            )
            ->when($result = $graph->isLeaf($n1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_not_leaf()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1])
            )
            ->when($result = $graph->isLeaf($n1))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_is_undefined_node_a_leaf()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1')
            )
            ->exception(function () use ($graph, $n1) {
                $graph->isLeaf($n1);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_is_root()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $graph->addNode($n1)
            )
            ->when($result = $graph->isRoot($n1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_not_root()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1])
            )
            ->when($result = $graph->isLeaf($n2))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_undefined_node_a_root()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1')
            )
            ->exception(function () use ($graph, $n1) {
                $graph->isRoot($n1);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_to_string()
    {
        $this
            ->given(
                $graph = new SUT(SUT::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $n4    = new LUT\SimpleNode('n4'),
                $n5    = new LUT\SimpleNode('n5'),
                $graph->addNode($n1),
                $graph->addNode($n2, [$n1]),
                $graph->addNode($n3, [$n1, $n2, $n3]),
                $graph->addNode($n4, [$n3]),
                $graph->addNode($n2, [$n4]),
                $graph->addNode($n5)
            )
            ->when($result = $graph->__toString())
            ->then
                ->string($result)
                    ->isEqualTo(
                        'digraph {' . "\n" .
                        '    n1;' . "\n" .
                        '    n2;' . "\n" .
                        '    n3;' . "\n" .
                        '    n4;' . "\n" .
                        '    n5;' . "\n" .
                        '    n1 -> n2;' . "\n" .
                        '    n1 -> n3;' . "\n" .
                        '    n2 -> n3;' . "\n" .
                        '    n3 -> n3;' . "\n" .
                        '    n3 -> n4;' . "\n" .
                        '    n4 -> n2;' . "\n" .
                        '}'
                    );
    }

    public function case_get_iterator()
    {
        $this
            ->given(
                $graph = new SUT(),
                $n1 = new LUT\SimpleNode('n1'),
                $n2 = new LUT\SimpleNode('n2'),
                $n3 = new LUT\SimpleNode('n3'),
                $graph->addNode($n1),
                $graph->addNode($n2),
                $graph->addNode($n3)
            )
            ->when($result = $graph->getIterator())
            ->then
                ->array(iterator_to_array($result))
                    ->isEqualTo([
                        'n1' => $n1,
                        'n2' => $n2,
                        'n3' => $n3
                    ]);
    }
}
