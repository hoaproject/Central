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
    public function case_constructor()
    {
        $this
            ->when($result = new SUT())
            ->then
                ->let($groups = $this->invoke($result)->getGroups())
                ->object($groups)
                    ->isInstanceOf('Hoa\Graph')
                ->boolean($groups->isLoopAllowed())
                    ->isFalse();
    }

    public function case_constructor_with_allowed_loop()
    {
        $this
            ->when($result = new SUT(Graph::ALLOW_LOOP))
            ->then
                ->let($groups = $this->invoke($result)->getGroups())
                ->object($groups)
                    ->isInstanceOf('Hoa\Graph')
                ->boolean($groups->isLoopAllowed())
                    ->isTrue();
    }

    public function case_add_users()
    {
        $this
            ->given(
                $users = [
                    new LUT\User('u1'),
                    new LUT\User('u2'),
                    new LUT\User('u3')
                ],
                $acl      = new SUT('foo'),
                $oldCount = count($this->invoke($acl)->getUsers())
            )
            ->when($result = $acl->addUsers($users))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->integer(count($this->invoke($result)->getUsers()))
                    ->isEqualTo($oldCount + count($users))
                ->boolean($result->userExists('u1'))
                    ->isTrue()
                ->boolean($result->userExists('u2'))
                    ->isTrue()
                ->boolean($result->userExists('u3'))
                    ->isTrue()
                ->object($this->invoke($result)->getUser('u1'))
                    ->isIdenticalTo($users[0])
                ->object($this->invoke($result)->getUser('u2'))
                    ->isIdenticalTo($users[1])
                ->object($this->invoke($result)->getUser('u3'))
                    ->isIdenticalTo($users[2]);
    }

    public function case_add_users_not_a_valid_object()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $acl->addUsers([null]);
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_delete_users()
    {
        $this
            ->given(
                $users = [
                    new LUT\User('u1'),
                    new LUT\User('u2'),
                    new LUT\User('u3')
                ],
                $acl = new SUT('foo'),
                $acl->addUsers($users),
                $oldCount = count($this->invoke($acl)->getUsers()),

                $usersToDelete = [
                    $users[0],
                    $users[2]
                ]
            )
            ->when($result = $acl->deleteUsers($usersToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->integer(count($this->invoke($result)->getUsers()))
                    ->isEqualTo($oldCount - count($usersToDelete))
                ->boolean($result->userExists('u1'))
                    ->isFalse()
                ->boolean($result->userExists('u2'))
                    ->isTrue()
                ->boolean($result->userExists('u3'))
                    ->isFalse()
                ->object($this->invoke($result)->getUser('u2'))
                    ->isIdenticalTo($users[1]);
    }

    public function case_user_exists()
    {
        $this
            ->given(
                $acl = new SUT('foo'),
                $acl->addUsers([new LUT\User('u1')])
            )
            ->when($result = $acl->userExists('u1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_user_does_not_exist()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->when($result = $acl->userExists('u1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_user()
    {
        $this
            ->given(
                $acl  = new SUT('foo'),
                $user = new LUT\User('u1'),
                $acl->addUsers([$user])
            )
            ->when($result = $this->invoke($acl)->getUser('u1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($user);
    }

    public function case_get_undefined_user()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $this->invoke($acl)->getUser('u1');
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_get_users()
    {
        $this
            ->given(
                $users = [
                    new LUT\User('u1'),
                    new LUT\User('u2'),
                    new LUT\User('u3')
                ],
                $acl = new SUT('foo'),
                $acl->addUsers($users)
            )
            ->when($result = $this->invoke($acl)->getUsers())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'u1' => $users[0],
                        'u2' => $users[1],
                        'u3' => $users[2]
                    ]);
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
                ->array($groups->getParents($group->getId()))
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
                ->array($groups->getParents($g1->getId()))
                    ->isEqualTo([
                        'g2' => $g2,
                        'g3' => $g3
                    ])
                ->array($groups->getParents($g2->getId()))
                    ->isEmpty()
                ->array($groups->getParents($g3->getId()))
                    ->isEmpty();
    }

    public function case_add_group_not_a_valid_parent_object()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $acl->addGroup(new LUT\Group('g1'), [null]);
            })
                ->isInstanceOf('Hoa\Acl\Exception');
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
                ->isInstanceOf('Hoa\Acl\Exception');
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
            ->when($result = $acl->getGroup('g1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($g1);
    }

    public function case_get_undefined_group()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $acl->getGroup('g1');
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_add_services()
    {
        $this
            ->given(
                $services = [
                    new LUT\Service('s1'),
                    new LUT\Service('s2'),
                    new LUT\Service('s3')
                ],
                $acl      = new SUT('foo'),
                $oldCount = count($this->invoke($acl)->getServices())
            )
            ->when($result = $acl->addServices($services))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->integer(count($this->invoke($result)->getServices()))
                    ->isEqualTo($oldCount + count($services))
                ->boolean($result->serviceExists('s1'))
                    ->isTrue()
                ->boolean($result->serviceExists('s2'))
                    ->isTrue()
                ->boolean($result->serviceExists('s3'))
                    ->isTrue()
                ->object($this->invoke($result)->getService('s1'))
                    ->isIdenticalTo($services[0])
                ->object($this->invoke($result)->getService('s2'))
                    ->isIdenticalTo($services[1])
                ->object($this->invoke($result)->getService('s3'))
                    ->isIdenticalTo($services[2]);
    }

    public function case_add_services_not_a_valid_object()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $acl->addServices([null]);
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_delete_services()
    {
        $this
            ->given(
                $services = [
                    new LUT\Service('s1'),
                    new LUT\Service('s2'),
                    new LUT\Service('s3')
                ],
                $acl = new SUT('foo'),
                $acl->addServices($services),
                $oldCount = count($this->invoke($acl)->getServices()),

                $servicesToDelete = [
                    $services[0],
                    $services[2]
                ]
            )
            ->when($result = $acl->deleteServices($servicesToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($acl)
                ->integer(count($this->invoke($result)->getServices()))
                    ->isEqualTo($oldCount - count($servicesToDelete))
                ->boolean($result->serviceExists('s1'))
                    ->isFalse()
                ->boolean($result->serviceExists('s2'))
                    ->isTrue()
                ->boolean($result->serviceExists('s3'))
                    ->isFalse()
                ->object($this->invoke($result)->getService('s2'))
                    ->isIdenticalTo($services[1]);
    }

    public function case_service_exists()
    {
        $this
            ->given(
                $acl = new SUT('foo'),
                $acl->addServices([new LUT\Service('s1')])
            )
            ->when($result = $acl->serviceExists('s1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_service_does_not_exist()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->when($result = $acl->serviceExists('s1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_service()
    {
        $this
            ->given(
                $acl  = new SUT('foo'),
                $service = new LUT\Service('s1'),
                $acl->addServices([$service])
            )
            ->when($result = $this->invoke($acl)->getService('s1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($service);
    }

    public function case_get_undefined_service()
    {
        $this
            ->given($acl = new SUT('foo'))
            ->exception(function () use ($acl) {
                $this->invoke($acl)->getService('s1');
            })
                ->isInstanceOf('Hoa\Acl\Exception');
    }

    public function case_get_services()
    {
        $this
            ->given(
                $services = [
                    new LUT\Service('s1'),
                    new LUT\Service('s2'),
                    new LUT\Service('s3')
                ],
                $acl = new SUT('foo'),
                $acl->addServices($services)
            )
            ->when($result = $this->invoke($acl)->getServices())
            ->then
                ->array($result)
                    ->isEqualTo([
                        's1' => $services[0],
                        's2' => $services[1],
                        's3' => $services[2]
                    ]);
    }
}
