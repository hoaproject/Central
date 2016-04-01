<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
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

namespace Hoa\Acl\Test\Unit;

use Hoa\Acl\Acl as SUT;
use Hoa\Acl as LUT;
use Hoa\Graph;
use Hoa\Test;

/**
 * Class \Hoa\Acl\Test\Unit\Acl.
 *
 * Test suite of the ACL class.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Acl extends Test\Unit\Suite
{
    public function case_shadowed_constants()
    {
        $this
            ->then
                ->variable(SUT::DELETE_CASCADE)
                    ->isIdenticalTo(Graph::DELETE_CASCADE)
                ->variable(SUT::DELETE_RESTRICT)
                    ->isIdenticalTo(Graph::DELETE_RESTRICT);
    }

    public function case_constructor()
    {
        $this
            ->when($result = new SUT())
            ->then
                ->let($groups = $this->invoke($result)->getGroups())
                ->object($groups)
                    ->isInstanceOf(Graph::class)
                ->boolean($groups->isLoopAllowed())
                    ->isFalse();
    }

    public function case_add_group()
    {
        $this
            ->given(
                $group = new LUT\Group('g1'),
                $acl   = new SUT('foo')
            )
            ->when($result = $acl->addGroup($group))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->let($groups = $this->invoke($result)->getGroups())
                ->boolean($groups->nodeExists($group->getId()))
                    ->isTrue()
                ->object($groups->getNode($group->getId()))
                    ->isIdenticalTo($group)
                ->array($groups->getParents($group))
                    ->isEmpty();
    }

    public function case_add_group_with_parents()
    {
        $this
            ->given(
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $g3  = new LUT\Group('g3'),
                $acl = new SUT('foo'),
                $acl->addGroup($g2),
                $acl->addGroup($g3)
            )
            ->when($result = $acl->addGroup($g1, [$g2, $g3]))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->let($groups = $this->invoke($result)->getGroups())
                ->boolean($groups->nodeExists($g1->getId()))
                    ->isTrue()
                ->boolean($groups->nodeExists($g2->getId()))
                    ->isTrue()
                ->boolean($groups->nodeExists($g3->getId()))
                    ->isTrue()
                ->object($groups->getNode($g1->getId()))
                    ->isIdenticalTo($g1)
                ->object($groups->getNode($g2->getId()))
                    ->isIdenticalTo($g2)
                ->object($groups->getNode($g3->getId()))
                    ->isIdenticalTo($g3)
                ->array($groups->getParents($g1))
                    ->isEqualTo([
                        'g2' => $g2,
                        'g3' => $g3
                    ])
                ->array($groups->getParents($g2))
                    ->isEmpty()
                ->array($groups->getParents($g3))
                    ->isEmpty();
    }

    public function case_add_group_not_a_valid_parent_object()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $acl->addGroup(new LUT\Group('g1'), [null]);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_delete_group()
    {
        $this
            ->given(
                $g1  = new LUT\Group('g1'),
                $acl = new SUT('foo'),
                $acl->addGroup($g1)
            )
            ->when($result = $acl->deleteGroup($g1))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->let($groups = $this->invoke($result)->getGroups())
                ->boolean($groups->nodeExists($g1->getId()))
                    ->isFalse();
    }

    public function case_delete_group_restricted_to_self_with_children()
    {
        $this
            ->given(
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $acl = new SUT('foo'),
                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1])
            )
            ->exception(function () use ($acl, $g1) {
                $acl->deleteGroup($g1);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_delete_group_with_children()
    {
        $this
            ->given(
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $acl = new SUT('foo'),
                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1])
            )
            ->when($result = $acl->deleteGroup($g1, $acl::DELETE_CASCADE))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->let($groups = $this->invoke($result)->getGroups())
                ->boolean($groups->nodeExists($g1->getId()))
                    ->isFalse()
                ->boolean($groups->nodeExists($g2->getId()))
                    ->isFalse();
    }

    public function case_group_exists()
    {
        $this
            ->given(
                $g1  = new LUT\Group('g1'),
                $acl = new SUT('foo'),
                $acl->addGroup($g1)
            )
            ->when($result = $acl->groupExists('g1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_group_does_not_exist()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->when($result = $acl->groupExists('g1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_group()
    {
        $this
            ->given(
                $g1  = new LUT\Group('g1'),
                $acl = new SUT('foo'),
                $acl->addGroup($g1)
            )
            ->when($result = $this->invoke($acl)->getGroup('g1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($g1);
    }

    public function case_get_undefined_group()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $this->invoke($acl)->getGroup('g1');
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }
}
