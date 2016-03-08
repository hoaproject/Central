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
use Hoa\Acl\Service as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Acl\Test\Unit\Service.
 *
 * Test suite of the service class.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Service extends Test\Unit\Suite
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

    public function case_add_users()
    {
        $this
            ->given(
                $users = [
                    new LUT\User('u1'),
                    new LUT\User('u2'),
                    new LUT\User('u3')
                ],
                $service  = new SUT('foo'),
                $oldCount = count($service->getUsers())
            )
            ->when($result = $service->addUsers($users))
            ->then
                ->object($result)
                    ->isIdenticalTo($service)
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
            ->given($service = new SUT('foo'))
            ->exception(function () use ($service) {
                $service->addUsers([null]);
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
                $service = new SUT('foo'),
                $service->addUsers($users),
                $oldCount = count($service->getUsers()),

                $usersToDelete = [
                    $users[0],
                    $users[2]
                ]
            )
            ->when($result = $service->deleteUsers($usersToDelete))
            ->then
                ->object($result)
                    ->isIdenticalTo($service)
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
                $service = new SUT('foo'),
                $service->addUsers([new LUT\User('u1')])
            )
            ->when($result = $service->userExists('u1'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_user_does_not_exist()
    {
        $this
            ->given($service = new SUT('foo'))
            ->when($result = $service->userExists('p1'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_get_user()
    {
        $this
            ->given(
                $service = new SUT('foo'),
                $user    = new LUT\User('u1'),
                $service->addUsers([$user])
            )
            ->when($result = $service->getUser('u1'))
            ->then
                ->object($result)
                    ->isIdenticalTo($user);
    }

    public function case_get_undefined_user()
    {
        $this
            ->given($service = new SUT('foo'))
            ->exception(function () use ($service) {
                $service->getUser('u1');
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
                $service = new SUT('foo'),
                $service->addUsers($users)
            )
            ->when($result = $service->getUsers())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'u1' => $users[0],
                        'u2' => $users[1],
                        'u3' => $users[2]
                    ]);
    }

    public function case_set_id()
    {
        $this
            ->given(
                $oldId   = 'foo',
                $service = new SUT($oldId),
                $id      = 'bar'
            )
            ->when($result = $this->invoke($service)->setId($id))
            ->then
                ->string($result)
                    ->isEqualTo($oldId)
                ->string($service->getId())
                    ->isEqualTo($id);
    }

    public function case_set_label()
    {
        $this
            ->given(
                $id       = 'foo',
                $oldLabel = 'bar',
                $service  = new SUT($id, $oldLabel),
                $label    = 'baz'
            )
            ->when($result = $service->setLabel($label))
            ->then
                ->string($result)
                    ->isEqualTo($oldLabel)
                ->string($service->getLabel())
                    ->isEqualTo($label);
    }
}
