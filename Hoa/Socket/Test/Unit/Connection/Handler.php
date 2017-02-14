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

namespace Hoa\Socket\Test\Unit\Connection;

use Hoa\Socket as LUT;
use Hoa\Test;
use Mock\Hoa\Socket\Connection\Handler as SUT;

/**
 * Class \Hoa\Socket\Test\Unit\Connection\Handler.
 *
 * Test suite of the connection handler.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Handler extends Test\Unit\Suite
{
    public function case_constructor()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection()
            )
            ->when($result = new SUT($connection))
            ->then
                ->object($result->getConnection())
                    ->isIdenticalTo($connection);
    }

    public function case_get_original_connection()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection)
            )
            ->when($result = $this->invoke($handler)->getOriginalConnection())
            ->then
                ->object($result)
                    ->isIdenticalTo($connection);
    }

    public function case_get_merged_connections()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('consider'),
                $connectionA = new \Mock\Hoa\Socket\Connection(),
                $handlerA    = new SUT($connectionA),
                $connectionB = new \Mock\Hoa\Socket\Connection(),
                $handlerB    = new SUT($connectionB),
                $handlerA->merge($handlerB)
            )
            ->when($result = $this->invoke($handlerA)->getMergedConnections())
            ->then
                ->array($result)
                    ->isEqualTo([$handlerB]);
    }

    public function case_run_connect_and_wait()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Server(),
                $handler    = new SUT($connection),

                $this->calling($connection)->connectAndWait = function () use (&$connectCalled) {
                    $connectCalled = true;
                },
                $this->calling($connection)->disconnect = function () use (&$disconnectCalled) {
                    $disconnectCalled = true;
                },
                $this->calling($connection)->select = [],

                $this->constant->SUCCEED = false
            )
            ->when($result = $handler->run())
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($connectCalled)
                    ->isTrue()
                ->boolean($disconnectCalled)
                    ->isTrue();
    }

    public function case_run_connect()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->calling($connection)->connect = function () use (&$connectCalled) {
                    $connectCalled = true;
                },
                $this->calling($connection)->disconnect = function () use (&$disconnectCalled) {
                    $disconnectCalled = true;
                },
                $this->calling($connection)->select = [],

                $this->constant->SUCCEED = false
            )
            ->when($result = $handler->run())
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($connectCalled)
                    ->isTrue()
                ->boolean($disconnectCalled)
                    ->isTrue();
    }

    public function case_run_node_on_the_current_handler()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('connect'),
                $connectionA = new \Mock\Hoa\Socket\Connection(),
                $connectionB = new \Mock\Hoa\Socket\Connection(),

                $handlerA = new SUT($connectionA),
                $handlerB = new SUT($connectionB),
                $handlerA->merge($handlerB),

                $this->mockGenerator->orphanize('__construct'),
                $nodeX = new \Mock\Hoa\Socket\Node(),

                $this->calling($connectionA)->select = [$nodeX],
                $this->calling($connectionA)->is     = function (LUT\Connection $connection) use ($self, &$isCalled, $connectionB) {
                    $isCalled = true;

                    $self
                        ->object($connection)
                            ->isIdenticalTo($connectionB);

                    return false;
                },

                $this->calling($handlerA)->_run = function (LUT\Node $node) use ($self, &$runCalled, $nodeX) {
                    $runCalled = true;

                    $self
                        ->object($node)
                            ->isIdenticalTo($nodeX);
                },

                $this->constant->SUCCEED = false
            )
            ->when($result = $handlerA->run())
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($isCalled)
                    ->isTrue()
                ->boolean($runCalled)
                    ->isTrue();
    }

    public function case_run_node_on_another_handler()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('connect'),
                $connectionA = new \Mock\Hoa\Socket\Connection(),
                $connectionB = new \Mock\Hoa\Socket\Connection(),

                $handlerA = new SUT($connectionA),
                $handlerB = new SUT($connectionB),
                $handlerA->merge($handlerB),

                $this->mockGenerator->orphanize('__construct'),
                $nodeX = new \Mock\Hoa\Socket\Node(),

                $this->calling($connectionA)->select = [$nodeX],
                $this->calling($connectionA)->is     = function (LUT\Connection $connection) use ($self, &$isCalled, $connectionB) {
                    $isCalled = true;

                    $self
                        ->object($connection)
                            ->isIdenticalTo($connectionB);

                    return true;
                },

                $this->calling($handlerA)->_run = function (LUT\Node $node) use (&$runCalledA) {
                    $runCalledA = true;
                },
                $this->calling($handlerB)->_run = function (LUT\Node $node) use ($self, &$runCalledB, $nodeX) {
                    $runCalledB = true;

                    $self
                        ->object($node)
                            ->isIdenticalTo($nodeX);
                },

                $this->constant->SUCCEED = false
            )
            ->when($result = $handlerA->run())
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($isCalled)
                    ->isTrue()
                ->variable($runCalledA)
                    ->isNull()
                ->boolean($runCalledB)
                    ->isTrue();
    }

    public function case_run_when_connection_failed_to_detect_the_node()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $this->mockGenerator->orphanize('connect'),
                $connectionA = new \Mock\Hoa\Socket\Connection(),
                $this->mockGenerator->orphanize('__construct'),
                $connectionB = new \Mock\Hoa\Socket\Client(),

                $handlerA = new SUT($connectionA),
                $handlerB = new SUT($connectionB),
                $handlerA->merge($handlerB),

                $this->mockGenerator->orphanize('__construct'),
                $nodeX = new \Mock\Hoa\Socket\Node(),

                $resourceY = 42,

                $this->calling($connectionA)->select         = [$resourceY],
                $this->calling($connectionB)->getCurrentNode = $nodeX,
                $this->calling($nodeX)->getSocket            = $resourceY,

                $this->calling($handlerA)->_run = function (LUT\Node $node) use (&$runCalledA) {
                    $runCalledA = true;
                },
                $this->calling($handlerB)->_run = function (LUT\Node $node) use ($self, &$runCalledB, $nodeX) {
                    $runCalledB = true;

                    $self
                        ->object($node)
                            ->isIdenticalTo($nodeX);
                },

                $this->constant->SUCCEED = false
            )
            ->when($result = $handlerA->run())
            ->then
                ->variable($result)
                    ->isNull()
                ->variable($runCalledA)
                    ->isNull()
                ->boolean($runCalledB)
                    ->isTrue();
    }

    public function case_merge()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connectionA = new \Mock\Hoa\Socket\Connection(),
                $handlerA    = new SUT($connectionA),
                $connectionB = new \Mock\Hoa\Socket\Connection(),
                $handlerB    = new SUT($connectionB),

                $this->calling($connectionA)->consider = function (LUT\Connection $connection) use ($self, &$called, $connectionB) {
                    $called = true;

                    $self
                        ->object($connection)
                            ->isIdenticalTo($connectionB);

                    return;
                }
            )
            ->when($result = $handlerA->merge($handlerB))
            ->then
                ->object($result)
                    ->isIdenticalTo($handlerA)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_merge_a_server()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->makeVisible('setConnection')->generate('Hoa\Socket\Connection\Handler', 'Mock', 'Handlerz'),
                $this->mockGenerator->orphanize('__construct'),
                $connectionA = new \Mock\Hoa\Socket\Connection(),
                $handlerA    = new \Mock\Handlerz($connectionA),
                $this->mockGenerator->orphanize('__construct'),
                $connectionB = new \Mock\Hoa\Socket\Server(),
                $handlerB    = new \Mock\Handlerz($connectionB),

                $this->calling($handlerB)->setConnection = function (LUT\Connection $connection) use ($self, &$called, $connectionA) {
                    $called = true;

                    $self
                        ->object($connection)
                            ->isIdenticalTo($connectionA);

                    return;
                }
            )
            ->when($result = $handlerA->merge($handlerB))
            ->then
                ->object($result)
                    ->isIdenticalTo($handlerA)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_send_on_nonexistent_node()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->calling($connection)->getCurrentNode = null
            )
            ->when($result = $handler->send('foo'))
            ->then
                ->variable($result)
                    ->isNull();
    }

    public function case_send_on_unspecified_node()
    {
        $self = $this;

        $this
            ->given(
                $message = 'foo',

                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $oldResource = 7,
                $resource    = 42,

                $this->calling($connection)->getCurrentNode = $node,
                $this->calling($connection)->_setStream[1] = function ($socket) use ($self, &$streamCalled0, $oldResource, $resource) {
                    $streamCalled0 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($resource);

                    return $oldResource;
                },
                $this->calling($connection)->_setStream[2] = function ($socket) use ($self, &$streamCalled1, $oldResource, $resource) {
                    $streamCalled1 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($oldResource);

                    return $resource;
                },
                $this->calling($node)->getSocket = $resource,
                $this->calling($handler)->_send  = function ($_message, $_node) use ($self, &$sendCalled, $message, $node) {
                    $sendCalled = true;

                    $self
                        ->string($_message)
                            ->isEqualTo($message)
                        ->object($_node)
                            ->isIdenticalTo($node);

                    return strlen($message);
                }
            )
            ->when($result = $handler->send($message))
            ->then
                ->integer($result)
                    ->isEqualTo(strlen($message))
                ->boolean($streamCalled0)
                    ->isTrue()
                ->boolean($streamCalled1)
                    ->isTrue()
                ->boolean($sendCalled)
                    ->isTrue();
    }

    public function case_send_broken_pipe()
    {
        $self = $this;

        $this
            ->given(
                $message   = 'foo',
                $exception = new LUT\Exception\BrokenPipe('Foo', 0),

                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $oldResource = 7,
                $resource    = 42,

                $this->calling($connection)->_setStream[1] = function ($socket) use ($self, &$streamCalled0, $oldResource, $resource) {
                    $streamCalled0 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($resource);

                    return $oldResource;
                },
                $this->calling($connection)->_setStream[2] = function ($socket) use ($self, &$streamCalled1, $oldResource, $resource) {
                    $streamCalled1 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($oldResource);

                    return $resource;
                },
                $this->calling($node)->getSocket       = $resource,
                $this->calling($handler)->_send->throw = $exception
            )
            ->exception(function () use ($handler, $message, $node) {
                $handler->send($message, $node);
            })
                ->isIdenticalTo($exception)
                ->boolean($streamCalled0)
                    ->isTrue()
                ->boolean($streamCalled1)
                    ->isTrue();
    }

    public function case_send()
    {
        $self = $this;

        $this
            ->given(
                $message = 'foo',

                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $oldResource = 7,
                $resource    = 42,

                $this->calling($connection)->_setStream[1] = function ($socket) use ($self, &$streamCalled0, $oldResource, $resource) {
                    $streamCalled0 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($resource);

                    return $oldResource;
                },
                $this->calling($connection)->_setStream[2] = function ($socket) use ($self, &$streamCalled1, $oldResource, $resource) {
                    $streamCalled1 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($oldResource);

                    return $resource;
                },
                $this->calling($node)->getSocket = $resource,
                $this->calling($handler)->_send  = function ($_message, $_node) use ($self, &$sendCalled, $message, $node) {
                    $sendCalled = true;

                    $self
                        ->string($_message)
                            ->isEqualTo($message)
                        ->object($_node)
                            ->isIdenticalTo($node);

                    return strlen($message);
                }
            )
            ->when($result = $handler->send($message, $node))
            ->then
                ->integer($result)
                    ->isEqualTo(strlen($message))
                ->boolean($streamCalled0)
                    ->isTrue()
                ->boolean($streamCalled1)
                    ->isTrue()
                ->boolean($sendCalled)
                    ->isTrue();
    }

    public function case_send_on_closure()
    {
        $self = $this;

        $this
            ->given(
                $message = 'foo',

                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $oldResource = 7,
                $resource    = 42,

                $this->calling($connection)->_setStream[1] = function ($socket) use ($self, &$streamCalled0, $oldResource, $resource) {
                    $streamCalled0 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($resource);

                    return $oldResource;
                },
                $this->calling($connection)->_setStream[2] = function ($socket) use ($self, &$streamCalled1, $oldResource, $resource) {
                    $streamCalled1 = true;

                    $self
                        ->variable($socket)
                            ->isIdenticalTo($oldResource);

                    return $resource;
                },
                $this->calling($node)->getSocket = $resource,
                $this->calling($handler)->_send  = function ($_message, $_node) use ($self, &$sendCalled, $message, $node) {
                    $sendCalled = true;

                    $self
                        ->string($_message)
                            ->isEqualTo($message)
                        ->object($_node)
                            ->isIdenticalTo($node);

                    return function () use ($self) {
                        return func_get_args();
                    };
                }
            )
            ->when(function () use (&$result, $handler, $message, $node) {
                $result = $handler->send($message, $node);

                return;
            })
            ->then
                ->array($result('foo', 'bar'))
                    ->isEqualTo(['foo', 'bar'])
                ->boolean($streamCalled0)
                    ->isTrue()
                ->boolean($streamCalled1)
                    ->isTrue()
                ->boolean($sendCalled)
                    ->isTrue();
    }

    public function case_broadcast()
    {
        $self = $this;

        $this
            ->given(
                $message = 'foo',

                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $nodeX = new \Mock\Hoa\Socket\Node(),
                $nodeY = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getCurrentNode = $nodeX,

                $this->calling($handler)->broadcastIf = function (\Closure $predicate, $message) use ($self, &$called, $nodeX, $nodeY) {
                    $called = true;

                    $self
                        ->object($predicate)
                        ->let($reflection = new \ReflectionFunction($predicate))
                        ->let($parameters = $reflection->getParameters())
                        ->string($parameters[0]->getType() . '')
                            ->isEqualTo('Hoa\Socket\Node')
                        ->boolean($predicate($nodeX))
                            ->isFalse()
                        ->boolean($predicate($nodeY))
                            ->isTrue()
                        ->string($message)
                            ->isEqualTo('foo');

                    return;
                }
            )
            ->when($result = $handler->broadcast($message))
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($called)
                    ->isTrue();
    }

    public function case_broadcast_if_broken_pipe()
    {
        $self = $this;

        $this
            ->given(
                $message   = 'foo',
                $predicate = function (LUT\Node $node) use (&$nodeY, &$nodeZ) {
                    return $node === $nodeY || $node === $nodeZ;
                },
                $exception = new LUT\Exception\BrokenPipe('Foo', 0),

                $this->mockGenerator->orphanize('__construct'),
                $resource   = 42,
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $nodeX = new \Mock\Hoa\Socket\Node(),
                $nodeY = new \Mock\Hoa\Socket\Node(),
                $nodeZ = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getSocket = $resource,
                $this->calling($connection)->getNodes  = [$nodeX, $nodeY, $nodeZ],

                $this->calling($nodeY)->getConnection = $connection,
                $this->calling($nodeY)->getId         = 'nodeY',

                $this->calling($nodeZ)->getConnection = $connection,
                $this->calling($nodeZ)->getId         = 'nodeZ',

                $this->calling($handler)->send->throw = $exception
            )
            ->exception(function () use ($handler, $predicate, $message) {
                $handler->broadcastIf($predicate, $message, 'bar', 'baz');
            })
                ->isInstanceOf('Hoa\Exception\Group')
                ->integer(count($this->exception))
                    ->isEqualTo(2)
                ->object($this->exception[$nodeY->getId()])
                    ->isIdenticalTo($exception)
                ->object($this->exception[$nodeZ->getId()])
                    ->isIdenticalTo($exception);
    }

    public function case_broadcast_if()
    {
        $self = $this;

        $this
            ->given(
                $message   = 'foo',
                $predicate = function (LUT\Node $node) use (&$nodeY) {
                    return $node === $nodeY;
                },

                $this->mockGenerator->orphanize('__construct'),
                $resource   = 42,
                $connection = new \Mock\Hoa\Socket\Connection(),
                $handler    = new SUT($connection),

                $this->mockGenerator->orphanize('__construct'),
                $nodeX = new \Mock\Hoa\Socket\Node(),
                $nodeY = new \Mock\Hoa\Socket\Node(),
                $nodeZ = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getSocket = $resource,
                $this->calling($connection)->getNodes  = [$nodeX, $nodeY, $nodeZ],

                $this->calling($nodeY)->getConnection = $connection,

                $this->calling($handler)->send = function ($message, LUT\Node $node, $extra1, $extra2) use ($self, &$called, $nodeY) {
                    ++$called;

                    $self
                        ->string($message)
                            ->isEqualTo('foo')
                        ->object($node)
                            ->isIdenticalTo($nodeY)
                        ->string($extra1)
                            ->isEqualTo('bar')
                        ->string($extra2)
                            ->isEqualTo('baz');
                }
            )
            ->when($result = $handler->broadcastIf($predicate, $message, 'bar', 'baz'))
            ->then
                ->variable($result)
                    ->isNull()
                ->integer($called)
                    ->isEqualTo(1);
    }
}
