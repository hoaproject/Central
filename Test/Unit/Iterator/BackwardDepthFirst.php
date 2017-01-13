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

namespace Hoa\Graph\Test\Unit\Iterator;

use Hoa\Graph as LUT;
use Hoa\Graph\Iterator\BackwardDepthFirst as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Graph\Test\Unit\Iterator\BackwardDepthFirst.
 *
 * Test suite of the Backward Depth-First iterator.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class BackwardDepthFirst extends Test\Unit\Suite
{
    public function case_pre_ordering()
    {
        $this
            ->given(
                $graph = new LUT\AdjacencyList(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n21   = new LUT\SimpleNode('n2_1'),
                $n22   = new LUT\SimpleNode('n2_2'),
                $n23   = new LUT\SimpleNode('n2_3'),
                $n3    = new LUT\SimpleNode('n3'),
                $n31   = new LUT\SimpleNode('n3_1'),
                $n32   = new LUT\SimpleNode('n3_2'),
                $n4    = new LUT\SimpleNode('n4'),
                $graph->addNode($n21),
                $graph->addNode($n22),
                $graph->addNode($n23),
                $graph->addNode($n2, [$n21, $n22, $n23]),
                $graph->addNode($n31),
                $graph->addNode($n32),
                $graph->addNode($n3, [$n31, $n32]),
                $graph->addNode($n4),
                $graph->addNode($n1, [$n2, $n3, $n4])
            )
            ->when($result = new SUT($graph, $n1))
            ->then
                ->array(iterator_to_array($result))
                    ->isEqualTo([
                        $n1,
                        $n2,
                        $n21,
                        $n22,
                        $n23,
                        $n3,
                        $n31,
                        $n32,
                        $n4
                    ]);
    }

    public function case_pre_ordering_with_loop()
    {
        $this
            ->given(
                $graph = new LUT\AdjacencyList(LUT\Graph::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n21   = new LUT\SimpleNode('n2_1'),
                $n22   = new LUT\SimpleNode('n2_2'),
                $n23   = new LUT\SimpleNode('n2_3'),
                $n3    = new LUT\SimpleNode('n3'),
                $n31   = new LUT\SimpleNode('n3_1'),
                $n32   = new LUT\SimpleNode('n3_2'),
                $n4    = new LUT\SimpleNode('n4'),
                $graph->addNode($n21),
                $graph->addNode($n22),
                $graph->addNode($n23),
                $graph->addNode($n2, [$n21, $n22, $n23]),
                $graph->addNode($n31),
                $graph->addNode($n32),
                $graph->addNode($n3, [$n31, $n32]),
                $graph->addNode($n4),
                $graph->addNode($n1, [$n2, $n3, $n4]),
                $graph->addNode($n21, [$n1]),
                $graph->addNode($n22, [$n1])
            )
            ->when($result = new SUT($graph, $n1))
            ->then
                ->array(iterator_to_array($result))
                    ->isEqualTo([
                        $n1,
                        $n2,
                        $n21,
                        $n22,
                        $n23,
                        $n3,
                        $n31,
                        $n32,
                        $n4
                    ]);
    }

    public function case_pre_ordering_with_reflexive_nodes()
    {
        $this
            ->given(
                $graph = new LUT\AdjacencyList(LUT\Graph::ALLOW_LOOP),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $graph->addNode($n2, [$n2]),
                $graph->addNode($n1,  [$n2, $n1])
            )
            ->when($result = new SUT($graph, $n1))
            ->then
                ->array(iterator_to_array($result))
                    ->isEqualTo([
                        $n1,
                        $n2
                    ]);
    }

    public function case_get_neighbours()
    {
        $this
            ->given(
                $graph = new LUT\AdjacencyList(),
                $n1    = new LUT\SimpleNode('n1'),
                $n2    = new LUT\SimpleNode('n2'),
                $n3    = new LUT\SimpleNode('n3'),
                $graph->addNode($n2),
                $graph->addNode($n3),
                $graph->addNode($n1, [$n2, $n3]),

                $iterator = new SUT($graph, $n1)
            )
            ->when($result = $iterator->getNeighbours($n1))
            ->then
                ->array($result)
                    ->isEqualTo([
                        'n2' => $n2,
                        'n3' => $n3
                    ]);
    }
}
