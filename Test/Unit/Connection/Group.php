<?php

declare(strict_types=1);

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

namespace Hoa\Socket\Test\Unit\Connection;

use Hoa\Socket as LUT;
use Hoa\Socket\Connection\Group as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Socket\Test\Unit\Connection\Group.
 *
 * Test suite of the connection group.
 *
 * @license    New BSD License
 */
class Group extends Test\Unit\Suite
{
    public function case_interfaces(): void
    {
        $this
            ->when($result = new SUT())
            ->then
                ->object($result)
                    ->isInstanceOf(\ArrayAccess::class)
                    ->isInstanceOf(\IteratorAggregate::class)
                    ->isInstanceOf(\Countable::class);
    }

    public function case_offset_exists(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),

                $group        = new SUT(),
                $group['foo'] = new \Mock\Hoa\Socket\Connection\Handler()
            )
            ->when($result = $group->offsetExists('foo'))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_offset_does_not_exist(): void
    {
        $this
            ->given($group = new SUT())
            ->when($result = $group->offsetExists('foo'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_offset_get(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection\Handler(),

                $group        = new SUT(),
                $group['foo'] = $connection
            )
            ->when($result = $group->offsetGet('foo'))
            ->then
                ->object($result)
                    ->isIdenticalTo($connection);
    }

    public function case_offset_get_an_undefined_offset(): void
    {
        $this
            ->given($group = new SUT())
            ->when($result = $group->offsetGet('foo'))
            ->then
                ->variable($result)
                    ->isNull();
    }

    public function case_offset_set_not_a_connection(): void
    {
        $this
            ->given($group = new SUT())
            ->exception(function () use ($group): void {
                $group->offsetSet(null, 42);
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_offset_set_with_a_null_offset(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection\Handler(),
                $group      = new SUT(),
                $oldCount   = count($group)
            )
            ->when($result = $group->offsetSet(null, $connection))
            ->then
                ->variable($result)
                    ->isNull()
                ->integer(count($group))
                    ->isEqualTo($oldCount + 1)
                ->boolean(isset($group[0]))
                    ->isTrue()
                ->object($group[0])
                    ->isIdenticalTo($connection);
    }

    public function case_offset_set(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection\Handler(),
                $group      = new SUT(),
                $oldCount   = count($group)
            )
            ->when($result = $group->offsetSet('foo', $connection))
            ->then
                ->variable($result)
                    ->isNull()
                ->integer(count($group))
                    ->isEqualTo($oldCount + 1)
                ->boolean(isset($group['foo']))
                    ->isTrue()
                ->object($group['foo'])
                    ->isIdenticalTo($connection);
    }

    public function case_offset_set_with_the_same_key(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connectionA = new \Mock\Hoa\Socket\Connection\Handler(),
                $connectionB = new \Mock\Hoa\Socket\Connection\Handler(),
                $group       = new SUT(),
                $group->offsetSet('foo', $connectionA),
                $oldCount = count($group)
            )
            ->when($result = $group->offsetSet('foo', $connectionB))
            ->then
                ->variable($result)
                    ->isNull()
                ->integer(count($group))
                    ->isEqualTo($oldCount)
                ->boolean(isset($group['foo']))
                    ->isTrue()
                ->object($group['foo'])
                    ->isIdenticalTo($connectionB);
    }

    public function case_offset_set_another_connection(): void
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connectionA = new \Mock\Hoa\Socket\Connection\Handler(),
                $connectionB = new \Mock\Hoa\Socket\Connection\Handler(),
                $this->calling($connectionA)->merge = function (LUT\Connection\Handler $connection) use ($self, &$called, $connectionA, $connectionB) {
                    $called = true;

                    $self
                        ->object($connection)
                            ->isIdenticalTo($connectionB);

                    return $connectionA;
                },

                $group = new SUT(),
                $group->offsetSet('foo', $connectionA),
                $oldCount = count($group)
            )
            ->when($result = $group->offsetSet('bar', $connectionB))
            ->then
                ->variable($result)
                    ->isNull()
                ->integer(count($group))
                    ->isEqualTo($oldCount + 1)
                ->boolean(isset($group['foo']))
                    ->isTrue()
                ->boolean(isset($group['bar']))
                    ->isTrue()
                ->object($group['foo'])
                    ->isIdenticalTo($connectionA)
                ->object($group['bar'])
                    ->isIdenticalTo($connectionB)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_offset_unset(): void
    {
        $this
            ->given($group = new SUT())
            ->exception(function () use ($group): void {
                $group->offsetUnset('foo');
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_get_empty_iterator(): void
    {
        $this
            ->given($group = new SUT())
            ->when($result = $group->getIterator())
            ->then
                ->iterator($result)
                    ->isInstanceOf(\ArrayIterator::class)
                    ->hasSize(0);
    }

    public function case_get_iterator(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $group   = new SUT(),
                $group[] = new \Mock\Hoa\Socket\Connection\Handler()
            )
            ->when($result = $group->getIterator())
            ->then
                ->iterator($result)
                    ->isInstanceOf(\ArrayIterator::class)
                    ->hasSize(1);
    }

    public function case_count_zero(): void
    {
        $this
            ->given($group = new SUT())
            ->when($result = $group->count())
            ->then
                ->integer($result)
                    ->isEqualTo(0);
    }

    public function case_count(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('merge'),
                $group   = new SUT(),
                $group[] = new \Mock\Hoa\Socket\Connection\Handler(),
                $group[] = new \Mock\Hoa\Socket\Connection\Handler()
            )
            ->when($result = $group->count())
            ->then
                ->integer($result)
                    ->isEqualTo(2);
    }

    public function case_merge(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection\Handler(),
                $group      = new SUT(),
                $oldCount   = count($group)
            )
            ->when($result = $group->merge($connection))
            ->then
                ->object($result)
                    ->isIdenticalTo($result)
                ->integer(count($group))
                    ->isEqualTo($oldCount + 1)
                ->boolean(isset($group[0]))
                    ->isTrue()
                ->object($group[0])
                    ->isIdenticalTo($connection);
    }

    public function case_run_no_connection(): void
    {
        $this
            ->given($group = new SUT())
            ->exception(function () use ($group): void {
                $group->run();
            })
                ->isInstanceOf(LUT\Exception::class);
    }

    public function case_run(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('merge'),
                $group   = new SUT(),
                $group[] = new \Mock\Hoa\Socket\Connection\Handler(),
                $group[] = new \Mock\Hoa\Socket\Connection\Handler(),
                $this->calling($group[0])->run = function () use (&$called): void {
                    $called = true;

                    return;
                }
            )
            ->when($result = $group->run())
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($called)
                    ->isTrue();
    }

    public function case_get_first_connection(): void
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('merge'),
                $connectionA = new \Mock\Hoa\Socket\Connection\Handler(),
                $connectionB = new \Mock\Hoa\Socket\Connection\Handler(),
                $group       = new SUT(),
                $group[]     = $connectionA,
                $group[]     = $connectionB
            )
            ->when($result = $group->getFirstConnection())
            ->then
                ->object($result)
                    ->isIdenticalTo($connectionA);
    }
}
