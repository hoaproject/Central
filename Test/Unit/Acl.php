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
 * @copyright  Copyright © 2007-2017 Hoa community
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
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_allow()
    {
        $this
            ->given(
                $group       = new LUT\Group('g1'),
                $permissions = [
                    new LUT\Permission('p1'),
                    new LUT\Permission('p2'),
                    new LUT\Permission('p3')
                ],
                $acl = new SUT('foo'),
                $acl->addGroup($group)
            )
            ->when($result = $acl->allow($group, $permissions))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->let($groups = $this->invoke($acl)->getGroups())
                ->boolean($groups->nodeExists('g1'))
                    ->isTrue()
                ->let($g1 = $this->invoke($acl)->getGroup('g1'))
                ->object($g1)
                    ->isIdenticalTo($group)
                ->array($g1->getPermissions())
                    ->isEqualTo([
                        'p1' => $permissions[0],
                        'p2' => $permissions[1],
                        'p3' => $permissions[2]
                    ]);
    }

    public function case_allow_an_undeclared_group()
    {
        $this
            ->given(
                $group = new LUT\Group('g1'),
                $acl   = new SUT('foo')
            )
            ->exception(function () use ($acl, $group) {
                $acl->allow($group, []);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_deny()
    {
        $this
            ->given(
                $group       = new LUT\Group('g1'),
                $permissions = [
                    new LUT\Permission('p1'),
                    new LUT\Permission('p2'),
                    new LUT\Permission('p3')
                ],
                $acl = new SUT('foo'),
                $acl->addGroup($group),
                $acl->allow($group, $permissions)
            )
            ->when($result = $acl->deny($group, [$permissions[1]]))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->let($groups = $this->invoke($acl)->getGroups())
                ->boolean($groups->nodeExists('g1'))
                    ->isTrue()
                ->let($g1 = $this->invoke($acl)->getGroup('g1'))
                ->object($g1)
                    ->isIdenticalTo($group)
                ->array($g1->getPermissions())
                    ->isEqualTo([
                        'p1' => $permissions[0],
                        'p3' => $permissions[2]
                    ]);
    }

    public function case_deny_an_undeclared_group()
    {
        $this
            ->given(
                $group = new LUT\Group('g1'),
                $acl   = new SUT('foo')
            )
            ->exception(function () use ($acl, $group) {
                $acl->deny($group, []);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_is_allowed_simple()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),

                $acl->allow($g1, [$p1]),

                $g1->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_allowed_with_inherented_permission()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $g3  = new LUT\Group('g3'),
                $g4  = new LUT\Group('g4'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),
                $acl->addGroup($g3, [$g2]),
                $acl->addGroup($g4, [$g2]),

                $acl->allow($g1, [$p1]),

                $g3->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_allowed_with_a_user_in_multiple_groups()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $g3  = new LUT\Group('g3'),
                $g4  = new LUT\Group('g4'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),
                $acl->addGroup($g3),
                $acl->addGroup($g4, [$g3]),

                $acl->allow($g1, [$p1]),

                $g2->addUsers([$u1]),
                $g4->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_allowed_with_a_user_in_multiple_groups_and_inherited_permission()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $g3  = new LUT\Group('g3'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),
                $acl->addGroup($g3, [$g1]),

                $acl->allow($g1, [$p1]),

                $g2->addUsers([$u1]),
                $g3->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_allowed_with_an_owned_service()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $p1  = new LUT\Permission('p1'),
                $s1  = new LUT\Service('s1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),

                $acl->allow($g1, [$p1]),

                $g2->addUsers([$u1]),

                $u1->addServices([$s1])
            )
            ->when($result = $acl->isAllowed($u1, $p1, $s1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_allowed_with_a_shared_service()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $p1  = new LUT\Permission('p1'),
                $s1  = new LUT\Service('s1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),

                $acl->allow($g1, [$p1]),

                $g2->addUsers([$u1]),

                $g2->addServices([$s1])
            )
            ->when($result = $acl->isAllowed($u1, $p1, $s1))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_is_not_allowed_with_no_user()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),

                $acl->allow($g1, [$p1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_is_not_allowed_with_no_permission()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),

                $g1->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_is_not_allowed_with_no_permission_inherited()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $g3  = new LUT\Group('g3'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),
                $acl->addGroup($g3),

                $acl->allow($g1, [$p1]),

                $g3->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_is_not_allowed_with_permission_only_in_children()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $g2  = new LUT\Group('g2'),
                $p1  = new LUT\Permission('p1'),
                $acl = new SUT(),

                $acl->addGroup($g1),
                $acl->addGroup($g2, [$g1]),

                $acl->allow($g2, [$p1]),

                $g1->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_is_not_allowed_with_no_service()
    {
        $this
            ->given(
                $u1  = new LUT\User('u1'),
                $g1  = new LUT\Group('g1'),
                $p1  = new LUT\Permission('p1'),
                $s1  = new LUT\Service('s1'),
                $acl = new SUT(),

                $acl->addGroup($g1),

                $acl->allow($g1, [$p1]),

                $g1->addUsers([$u1])
            )
            ->when($result = $acl->isAllowed($u1, $p1, $s1))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_is_allowed_asserter_is_true()
    {
        return $this->_case_is_allowed_asserter(true);
    }

    public function case_is_allowed_asserter_is_false()
    {
        return $this->_case_is_allowed_asserter(false);
    }

    protected function _case_is_allowed_asserter($return)
    {
        $self = $this;

        $this
            ->given(
                $u1       = new LUT\User('u1'),
                $g1       = new LUT\Group('g1'),
                $p1       = new LUT\Permission('p1'),
                $s1       = new LUT\Service('s1'),
                $asserter = new \Mock\Hoa\Acl\Assertable(),
                $acl      = new SUT(),

                $acl->addGroup($g1),
                $acl->allow($g1, [$p1]),
                $g1->addUsers([$u1]),
                $u1->addServices([$s1]),

                $this->calling($asserter)->assert = function ($userId, $permissionId, $serviceId) use (&$called, $self, $u1, $p1, $s1, $return) {
                    $called = true;

                    $self
                        ->string($userId)
                            ->isEqualTo($u1->getId())
                        ->string($permissionId)
                            ->isEqualTo($p1->getId())
                        ->string($serviceId)
                            ->isEqualTo($s1->getId());

                    return $return;
                }
            )
            ->when($result = $acl->isAllowed($u1, $p1, $s1, $asserter))
            ->then
                ->boolean($result)
                    ->isEqualTo($return);
    }
}
