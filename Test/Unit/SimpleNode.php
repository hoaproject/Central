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

use Hoa\Graph as LUT;
use Hoa\Graph\SimpleNode as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Graph\Test\Unit\SimpleNode.
 *
 * Test suite of a simple node.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class SimpleNode extends Test\Unit\Suite
{
    public function case_constructor()
    {
        $this
            ->given(
                $id = 'foo',
                $value = 'bar'
            )
            ->when($result = new SUT($id, $value))
            ->then
                ->string($result->getNodeId())
                    ->isEqualTo($id)
                ->string($result->getNodeValue())
                    ->isEqualTo($value);
    }

    public function case_constructor_with_value_missing()
    {
        $this
            ->given($id = 'foo')
            ->when($result = new SUT($id))
            ->then
                ->string($result->getNodeId())
                    ->isEqualTo($id)
                ->variable($result->getNodeValue())
                    ->isNull();
    }

    public function case_is_a_node()
    {
        $this
            ->when($result = new SUT('foo'))
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Node::class);
    }

    public function case_set_node_id()
    {
        $this
            ->given(
                $oldId = 'foo',
                $newId = 'bar',
                $node  = new SUT($oldId)
            )
            ->when($result = $this->invoke($node)->setNodeId($newId))
            ->then
                ->string($result)
                    ->isEqualTo($oldId)
                ->string($node->getNodeId())
                    ->isEqualTo($newId);
    }

    public function case_set_node_value()
    {
        $this
            ->given(
                $oldValue = 'bar',
                $newValue = 'baz',
                $node     = new SUT('foo', $oldValue)
            )
            ->when($result = $node->setNodeValue($newValue))
            ->then
                ->string($result)
                    ->isEqualTo($oldValue)
                ->string($node->getNodeValue())
                    ->isEqualTo($newValue);
    }
}
