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

use Hoa\Acl as LUT;
use Hoa\Acl\User as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Acl\Test\Unit\User.
 *
 * Test suite of the user class.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class User extends Test\Unit\Suite
{
    public function case_constructor()
    {
        $this
            ->given(
                $id    = 'foo',
                $label = 'bar'
            )
            ->when($result = new SUT($id, $label))
            ->then
                ->string($result->getId())
                    ->isEqualTo($id)
                ->string($result->getLabel())
                    ->isEqualTo($label);
    }

    public function case_constructor_with_default_label()
    {
        $this
            ->given($id = 'foo')
            ->when($result = new SUT($id))
            ->then
                ->string($result->getId())
                    ->isEqualTo($id)
                ->variable($result->getLabel())
                    ->isNull();
    }

    public function case_add_groups()
    {
        $this
            ->given(
                $groups = [
                    new LUT\Group('g1'),
                    new LUT\Group('g2'),
                    new LUT\Group('g3')
                ],
                $user     = new SUT('foo'),
                $oldCount = count($user->getGroups())
            )
            ->when($result = $user->addGroups($groups))
            ->then
                ->object($result)
                    ->isIdenticalTo($user)
                ->integer(count($result->getGroups()))
                    ->isEqualTo($oldCount + count($groups))
                ->boolean($result->groupExists('g1'))
                    ->isTrue()
                ->boolean($result->groupExists('g2'))
                    ->isTrue()
                ->boolean($result->groupExists('g3'))
                    ->isTrue()
                ->object($result->getGroup('g1'))
                    ->isIdenticalTo($groups[0])
                ->object($result->getGroup('g2'))
                    ->isIdenticalTo($groups[1])
                ->object($result->getGroup('g3'))
                    ->isIdenticalTo($groups[2]);
    }

    public function case_add_groups_not_a_valid_object()
    {
        $this
            ->given($user = new SUT('foo'))
            ->exception(function () use ($user) {
                $user->addGroups([null]);
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_delete_groups()
    {
        $this
            ->given(
                $groups = [
                    new LUT\Group('g1'),
                    new LUT\Group('g2'),
                    new LUT\Group('g3')
                ],
                $user = new SUT('foo'),
                $user->addGroups($groups),
                $oldCount = count($user->getGroups()),

                $groupsToDelete = [
                    $groups[0],
                    $groups[2]
                ]
            )
            ->when($result = $user->deleteGroups($groupsToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($user)
                ->integer(count($result->getGroups()))
                    ->isEqualTo($oldCount - count($groupsToDelete))
                ->boolean($result->groupExists('g1'))
                    ->isFalse()
                ->boolean($result->groupExists('g2'))
                    ->isTrue()
                ->boolean($result->groupExists('g3'))
                    ->isFalse()
                ->object($result->getGroup('g2'))
                    ->isIdenticalTo($groups[1]);
    }

    public function case_group_exists()
    {
        $this
            ->given(
                $user = new SUT('foo'),
                $user->addGroups([new LUT\Group('g1')])
            )
            ->when($result = $user->groupExists('g1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_group_does_not_exist()
    {
        $this
            ->given($user = new SUT('foo'))
            ->when($result = $user->groupExists('p1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_group()
    {
        $this
            ->given(
                $user  = new SUT('foo'),
                $group = new LUT\Group('g1'),
                $user->addGroups([$group])
            )
            ->when($result = $user->getGroup('g1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($group);
    }

    public function case_get_undefined_group()
    {
        $this
            ->given($user = new SUT('foo'))
            ->exception(function () use ($user) {
                $user->getGroup('g1');
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_get_groups()
    {
        $this
            ->given(
                $groups = [
                    new LUT\Group('g1'),
                    new LUT\Group('g2'),
                    new LUT\Group('g3')
                ],
                $user = new SUT('foo'),
                $user->addGroups($groups)
            )
            ->when($result = $user->getGroups())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'g1' => $groups[0],
                        'g2' => $groups[1],
                        'g3' => $groups[2]
                    ]);
    }

    public function case_set_id()
    {
        $this
            ->given(
                $oldId = 'foo',
                $user  = new SUT($oldId),
                $id    = 'bar'
            )
            ->when($result = $this->invoke($user)->setId($id))
            ->then
                ->string($result)
                    ->isEqualTo($oldId)
                ->string($user->getId())
                    ->isEqualTo($id);
    }

    public function case_set_label()
    {
        $this
            ->given(
                $id       = 'foo',
                $oldLabel = 'bar',
                $user     = new SUT($id, $oldLabel),
                $label    = 'baz'
            )
            ->when($result = $user->setLabel($label))
            ->then
                ->string($result)
                    ->isEqualTo($oldLabel)
                ->string($user->getLabel())
                    ->isEqualTo($label);
    }
}
