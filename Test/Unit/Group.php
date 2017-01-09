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

use Hoa\Acl as LUT;
use Hoa\Acl\Group as SUT;
use Hoa\Graph;
use Hoa\Test;

/**
 * Class \Hoa\Acl\Test\Unit\Group.
 *
 * Test suite of the group class.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Group extends Test\Unit\Suite
{
    public function case_type()
    {
        $this
            ->given(
                $id    = 'foo',
                $label = 'bar'
            )
            ->when($result = new SUT($id, $label))
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Group::class)
                    ->isInstanceOf(Graph\Node::class);
    }

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

    public function case_add_users()
    {
        $this
            ->given(
                $users = [
                    new LUT\User('u1'),
                    new LUT\User('u2'),
                    new LUT\User('u3')
                ],
                $group    = new SUT('foo'),
                $oldCount = count($group->getUsers())
            )
            ->when($result = $group->addUsers($users))
            ->then
                ->object($result)
                    ->isIdenticalTo($group)
                ->integer(count($result->getUsers()))
                    ->isEqualTo($oldCount + count($users))
                ->boolean($result->userExists('u1'))
                    ->isTrue()
                ->boolean($result->userExists('u2'))
                    ->isTrue()
                ->boolean($result->userExists('u3'))
                    ->isTrue()
                ->object($result->getUser('u1'))
                    ->isIdenticalTo($users[0])
                ->object($result->getUser('u2'))
                    ->isIdenticalTo($users[1])
                ->object($result->getUser('u3'))
                    ->isIdenticalTo($users[2]);
    }

    public function case_add_users_not_a_valid_object()
    {
        $this
            ->given($group = new SUT('foo'))
            ->exception(function () use ($group) {
                $group->addUsers([null]);
            })
                ->isInstanceOf(LUT\Exception::class);
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
                $group = new SUT('foo'),
                $group->addUsers($users),
                $oldCount = count($group->getUsers()),

                $usersToDelete = [
                    $users[0],
                    $users[2]
                ]
            )
            ->when($result = $group->deleteUsers($usersToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($group)
                ->integer(count($result->getUsers()))
                    ->isEqualTo($oldCount - count($usersToDelete))
                ->boolean($result->userExists('u1'))
                    ->isFalse()
                ->boolean($result->userExists('u2'))
                    ->isTrue()
                ->boolean($result->userExists('u3'))
                    ->isFalse()
                ->object($result->getUser('u2'))
                    ->isIdenticalTo($users[1]);
    }

    public function case_user_exists()
    {
        $this
            ->given(
                $group = new SUT('foo'),
                $group->addUsers([new LUT\User('u1')])
            )
            ->when($result = $group->userExists('u1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_user_does_not_exist()
    {
        $this
            ->given($group = new SUT('foo'))
            ->when($result = $group->userExists('u1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_user()
    {
        $this
            ->given(
                $group = new SUT('foo'),
                $user  = new LUT\User('u1'),
                $group->addUsers([$user])
            )
            ->when($result = $group->getUser('u1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($user);
    }

    public function case_get_undefined_user()
    {
        $this
            ->given($group = new SUT('foo'))
            ->exception(function () use ($group) {
                $group->getUser('u1');
            })
                ->isInstanceOf(LUT\Exception::class);
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
                $group = new SUT('foo'),
                $group->addUsers($users)
            )
            ->when($result = $group->getUsers())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'u1' => $users[0],
                        'u2' => $users[1],
                        'u3' => $users[2]
                    ]);
    }

    public function case_add_permissions()
    {
        $this
            ->given(
                $permissions = [
                    new LUT\Permission('p1'),
                    new LUT\Permission('p2'),
                    new LUT\Permission('p3')
                ],
                $group    = new SUT('foo'),
                $oldCount = count($group->getPermissions())
            )
            ->when($result = $group->addPermissions($permissions))
            ->then
                ->object($result)
                    ->isIdenticalTo($group)
                ->integer(count($result->getPermissions()))
                    ->isEqualTo($oldCount + count($permissions))
                ->boolean($result->permissionExists('p1'))
                    ->isTrue()
                ->boolean($result->permissionExists('p2'))
                    ->isTrue()
                ->boolean($result->permissionExists('p3'))
                    ->isTrue()
                ->object($result->getPermission('p1'))
                    ->isIdenticalTo($permissions[0])
                ->object($result->getPermission('p2'))
                    ->isIdenticalTo($permissions[1])
                ->object($result->getPermission('p3'))
                    ->isIdenticalTo($permissions[2]);
    }

    public function case_add_permissions_not_a_valid_object()
    {
        $this
            ->given($group = new SUT('foo'))
            ->exception(function () use ($group) {
                $group->addPermissions([null]);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_delete_permissions()
    {
        $this
            ->given(
                $permissions = [
                    new LUT\Permission('p1'),
                    new LUT\Permission('p2'),
                    new LUT\Permission('p3')
                ],
                $group = new SUT('foo'),
                $group->addPermissions($permissions),
                $oldCount = count($group->getPermissions()),

                $permissionsToDelete = [
                    $permissions[0],
                    $permissions[2]
                ]
            )
            ->when($result = $group->deletePermissions($permissionsToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($group)
                ->integer(count($result->getPermissions()))
                    ->isEqualTo($oldCount - count($permissionsToDelete))
                ->boolean($result->permissionExists('p1'))
                    ->isFalse()
                ->boolean($result->permissionExists('p2'))
                    ->isTrue()
                ->boolean($result->permissionExists('p3'))
                    ->isFalse()
                ->object($result->getPermission('p2'))
                    ->isIdenticalTo($permissions[1]);
    }

    public function case_permission_exists()
    {
        $this
            ->given(
                $group = new SUT('foo'),
                $group->addPermissions([new LUT\Permission('p1')])
            )
            ->when($result = $group->permissionExists('p1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_permission_does_not_exist()
    {
        $this
            ->given($group = new SUT('foo'))
            ->when($result = $group->permissionExists('p1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_permission()
    {
        $this
            ->given(
                $group      = new SUT('foo'),
                $permission = new LUT\Permission('p1'),
                $group->addPermissions([$permission])
            )
            ->when($result = $group->getPermission('p1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($permission);
    }

    public function case_get_undefined_permission()
    {
        $this
            ->given($group = new SUT('foo'))
            ->exception(function () use ($group) {
                $group->getPermission('p1');
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_get_permissions()
    {
        $this
            ->given(
                $permissions = [
                    new LUT\Permission('p1'),
                    new LUT\Permission('p2'),
                    new LUT\Permission('p3')
                ],
                $group = new SUT('foo'),
                $group->addPermissions($permissions)
            )
            ->when($result = $group->getPermissions())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'p1' => $permissions[0],
                        'p2' => $permissions[1],
                        'p3' => $permissions[2]
                    ]);
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
                $group    = new SUT('foo'),
                $oldCount = count($this->invoke($group)->getServices())
            )
            ->when($result = $group->addServices($services))
            ->then
                ->object($result)
                    ->isIdenticalTo($group)
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
            ->given($group = new SUT('foo'))
            ->exception(function () use ($group) {
                $group->addServices([null]);
            })
                ->isInstanceOf(LUT\Exception::class);
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
                $group = new SUT('foo'),
                $group->addServices($services),
                $oldCount = count($this->invoke($group)->getServices()),

                $servicesToDelete = [
                    $services[0],
                    $services[2]
                ]
            )
            ->when($result = $group->deleteServices($servicesToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($group)
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
                $group = new SUT('foo'),
                $group->addServices([new LUT\Service('s1')])
            )
            ->when($result = $group->serviceExists('s1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_service_does_not_exist()
    {
        $this
            ->given($group = new SUT('foo'))
            ->when($result = $group->serviceExists('s1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_service()
    {
        $this
            ->given(
                $group   = new SUT('foo'),
                $service = new LUT\Service('s1'),
                $group->addServices([$service])
            )
            ->when($result = $this->invoke($group)->getService('s1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($service);
    }

    public function case_get_undefined_service()
    {
        $this
            ->given($group = new SUT('foo'))
            ->exception(function () use ($group) {
                $this->invoke($group)->getService('s1');
            })
                ->isInstanceOf(LUT\Exception::class);
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
                $group = new SUT('foo'),
                $group->addServices($services)
            )
            ->when($result = $this->invoke($group)->getServices())
            ->then
                ->array($result)
                    ->isEqualTo([
                        's1' => $services[0],
                        's2' => $services[1],
                        's3' => $services[2]
                    ]);
    }

    public function case_set_id()
    {
        $this
            ->given(
                $oldId = 'foo',
                $group = new SUT($oldId),
                $id    = 'bar'
            )
            ->when($result = $this->invoke($group)->setId($id))
            ->then
                ->string($result)
                    ->isEqualTo($oldId)
                ->string($group->getId())
                    ->isEqualTo($id);
    }

    public function case_set_label()
    {
        $this
            ->given(
                $id       = 'foo',
                $oldLabel = 'bar',
                $group    = new SUT($id, $oldLabel),
                $label    = 'baz'
            )
            ->when($result = $group->setLabel($label))
            ->then
                ->string($result)
                    ->isEqualTo($oldLabel)
                ->string($group->getLabel())
                    ->isEqualTo($label);
    }

    public function case_get_node_id()
    {
        $this
            ->given(
                $id    = 'foo',
                $group = new SUT($id)
            )
            ->when($result = $group->getNodeId())
            ->then
                ->string($result)
                    ->isEqualTo($group->getId());
    }
}
