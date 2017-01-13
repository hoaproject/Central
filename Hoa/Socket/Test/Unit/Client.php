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

namespace Hoa\Socket\Test\Unit;

use Hoa\Socket\Client as SUT;
use Hoa\Stream;
use Hoa\Test;

/**
 * Class \Hoa\Socket\Test\Unit\Client.
 *
 * Test suite for the client object.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Client extends Test\Unit\Suite
{
    public function case_is_a_connection()
    {
        $this
            ->given($this->mockGenerator->orphanize('__construct'))
            ->when($result = new \Mock\Hoa\Socket\Client())
            ->then
                ->object($result)
                    ->isInstanceOf('Hoa\Socket\Connection');
    }

    public function case_constructor()
    {
        $this
            ->given(
                $socket  = 'tcp://hoa-project.net:80',
                $timeout = 42,
                $flag    = SUT::ASYNCHRONOUS,
                $context = 'foo'
            )
            ->when($result = new SUT($socket, $timeout, $flag, $context))
            ->then
                ->let($_socket = $result->getSocket())
                ->object($_socket)
                    ->isInstanceOf('Hoa\Socket\Socket')
                ->integer($_socket->getAddressType())
                    ->isEqualTo($_socket::ADDRESS_DOMAIN)
                ->string($_socket->getTransport())
                    ->isEqualTo('tcp')
                ->string($_socket->getAddress())
                    ->isEqualTo('hoa-project.net')
                ->integer($_socket->getPort())
                    ->isEqualTo(80)
                ->boolean($_socket->isSecured())
                    ->isFalse()
                ->integer($result->getTimeout())
                    ->isEqualTo($timeout)
                ->integer($result->getFlag())
                    ->isEqualTo($flag | SUT::CONNECT)
                ->string($result->getContext())
                    ->isEqualTo($context);
    }

    public function case_open_cannot_join()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client     = new \Mock\Hoa\Socket\Client(),
                $streamName = 'foobar',
                $timeout    = 42,
                $flag       = SUT::CONNECT,

                $this->calling($client)->getTimeout   = $timeout,
                $this->calling($client)->getFlag      = $flag,
                $this->function->stream_socket_client = function ($_streamName, &$_errno, &$_errstr, $_timeout, $_flag) use ($self, &$called, $streamName, $timeout, $flag) {
                    $called = true;
                    $_errno = 0;

                    $self
                        ->string($_streamName)
                            ->isEqualTo($streamName)
                        ->integer($_timeout)
                            ->isEqualTo($timeout)
                        ->integer($_flag)
                            ->isEqualTo($flag);

                    return false;
                }
            )
            ->exception(function () use ($self, $client, $streamName) {
                $self->invoke($client)->_open($streamName);
            })
                ->isInstanceOf('Hoa\Socket\Exception')
                ->hasCode(0)
            ->boolean($called)
                ->isTrue();
    }

    public function case_open_returns_an_error()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client     = new \Mock\Hoa\Socket\Client(),
                $streamName = 'foobar',
                $timeout    = 42,
                $flag       = SUT::CONNECT,

                $this->calling($client)->getTimeout   = $timeout,
                $this->calling($client)->getFlag      = $flag,
                $this->function->stream_socket_client = function ($_streamName, &$_errno, &$_errstr, $_timeout, $_flag) use ($self, &$called, $streamName, $timeout, $flag) {
                    $called = true;
                    $_errno = 1;

                    $self
                        ->string($_streamName)
                            ->isEqualTo($streamName)
                        ->integer($_timeout)
                            ->isEqualTo($timeout)
                        ->integer($_flag)
                            ->isEqualTo($flag);

                    return false;
                }
            )
            ->exception(function () use ($self, $client, $streamName) {
                $self->invoke($client)->_open($streamName);
            })
                ->isInstanceOf('Hoa\Socket\Exception')
                ->hasCode(1)
            ->boolean($called)
                ->isTrue();
    }

    public function case_open()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client     = new \Mock\Hoa\Socket\Client(),
                $streamName = 'foobar',
                $timeout    = 42,
                $flag       = SUT::CONNECT,

                $oldStack   = $this->invoke($client)->getStack(),
                $oldNodes   = $client->getNodes(),

                $this->calling($client)->getTimeout   = $timeout,
                $this->calling($client)->getFlag      = $flag,
                $this->function->stream_socket_client = function ($_streamName, &$_errno, &$_errstr, $_timeout, $_flag) use ($self, &$called, $streamName, $timeout, $flag) {
                    $called = true;

                    $self
                        ->string($_streamName)
                            ->isEqualTo($streamName)
                        ->integer($_timeout)
                            ->isEqualTo($timeout)
                        ->integer($_flag)
                            ->isEqualTo($flag);

                    return fopen(__FILE__, 'r');
                }
            )
            ->when($result = $this->invoke($client)->_open($streamName))
            ->then
                ->resource($result)
                ->let($stack = $this->invoke($client)->getStack())
                ->integer(count($stack))
                    ->isEqualTo(count($oldStack) + 1)
                    ->isEqualTo(1)
                ->array($stack)
                ->resource($stack[0])
                    ->isIdenticalTo($result)

                ->let($node = $client->getCurrentNode())
                ->object($node)
                    ->isInstanceOf('Hoa\Socket\Node')
                ->string($node->getId())
                    ->isEqualTo($this->invoke($client)->getNodeId($result))
                ->resource($node->getSocket())
                    ->isIdenticalTo($result)
                ->object($node->getConnection())
                    ->isIdenticalTo($client)

                ->let($nodes = $client->getNodes())
                ->integer(count($nodes))
                    ->isEqualTo(count($oldNodes) + 1)
                ->array($nodes)
                ->object($nodes[$node->getId()])
                    ->isIdenticalTo($node);
    }

    public function case_open_with_context()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client     = new \Mock\Hoa\Socket\Client(),
                $streamName = 'foobar',
                $context    = Stream\Context::getInstance('foo'),
                $timeout    = 42,
                $flag       = SUT::CONNECT,

                $oldStack   = $this->invoke($client)->getStack(),
                $oldNodes   = $client->getNodes(),

                $this->calling($client)->getTimeout   = $timeout,
                $this->calling($client)->getFlag      = $flag,
                $this->function->stream_socket_client = function ($_streamName, &$_errno, &$_errstr, $_timeout, $_flag, $_context) use ($self, &$called, $streamName, $timeout, $flag, $context) {
                    $called = true;

                    $self
                        ->string($_streamName)
                            ->isEqualTo($streamName)
                        ->integer($_timeout)
                            ->isEqualTo($timeout)
                        ->integer($_flag)
                            ->isEqualTo($flag)
                        ->resource($_context)
                            ->isStreamContext()
                            ->isIdenticalTo($context->getContext());

                    return fopen(__FILE__, 'r');
                }
            )
            ->when($result = $this->invoke($client)->_open($streamName, $context))
            ->then
                ->resource($result)
                ->let($stack = $this->invoke($client)->getStack())
                ->integer(count($stack))
                    ->isEqualTo(count($oldStack) + 1)
                    ->isEqualTo(1)
                ->array($stack)
                ->resource($stack[0])
                    ->isIdenticalTo($result)

                ->let($node = $client->getCurrentNode())
                ->object($node)
                    ->isInstanceOf('Hoa\Socket\Node')
                ->string($node->getId())
                    ->isEqualTo($this->invoke($client)->getNodeId($result))
                ->resource($node->getSocket())
                    ->isIdenticalTo($result)
                ->object($node->getConnection())
                    ->isIdenticalTo($client)

                ->let($nodes = $client->getNodes())
                ->integer(count($nodes))
                    ->isEqualTo(count($oldNodes) + 1)
                ->array($nodes)
                ->object($nodes[$node->getId()])
                    ->isIdenticalTo($node);
    }

    public function case_close_persistent_connection()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client = new \Mock\Hoa\Socket\Client(),

                $this->calling($client)->isPersistent = true
            )
            ->when($result = $this->invoke($client)->_close())
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_close()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client = new \Mock\Hoa\Socket\Client(),
                $stream = 'foo',

                $this->calling($client)->isPersistent = false,
                $this->calling($client)->getStream    = $stream,
                $this->function->fclose               = function ($_stream) use ($self, &$called, $stream) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream);

                    return true;
                }
            )
            ->when($result = $this->invoke($client)->_close())
            ->then
                ->boolean($result)
                    ->isTrue()
                ->boolean($called)
                    ->isTrue();
    }

    public function case_select()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client  = new \Mock\Hoa\Socket\Client(),
                $timeout = 42,

                $this->calling($client)->getTimeout = $timeout,
                $this->function->stream_select      = function (&$_read, &$_write, &$_except, $_timeout, $_ttimeout) use ($self, &$called, $timeout) {
                    $called = true;

                    $self
                        ->array($_read)
                            ->isEmpty()
                        ->variable($_write)
                            ->isNull()
                        ->variable($_except)
                            ->isNull()
                        ->integer($_timeout)
                            ->isEqualTo($timeout)
                        ->integer($_ttimeout)
                            ->isEqualTo(0);

                    $_read = ['a', 'b', 'c'];
                }
            )
            ->when($result = $client->select())
            ->then
                ->object($result)
                    ->isIdenticalTo($client)
                ->array($this->invoke($client)->getIteratorValues())
                    ->isEqualTo(['a', 'b', 'c'])
                ->boolean($called)
                    ->isTrue();
    }

    public function case_consider_not_a_client()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $client = new \Mock\Hoa\Socket\Client(),
                $this->mockGenerator->orphanize('__construct'),
                $other = new \Mock\Hoa\Socket\Server()
            )
            ->exception(function () use ($client, $other) {
                $client->consider($other);
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_consider_disconnected_client()
    {
        $this->_case_consider(true);
    }

    public function case_consider()
    {
        $this->_case_consider(false);
    }

    protected function _case_consider($disconnected)
    {
        return
            $this
                ->given(
                    $this->mockGenerator->orphanize('__construct'),
                    $client = new \Mock\Hoa\Socket\Client(),
                    $other  = new \Mock\Hoa\Socket\Client(),
                    $this->mockGenerator->orphanize('__construct'),
                    $node   = new \Mock\Hoa\Socket\Node(),

                    $this->calling($node)->getSocket       = 42,
                    $this->calling($node)->getId           = 'foo',
                    $this->calling($other)->isDisconnected = $disconnected,
                    $this->calling($other)->getCurrentNode = $node,
                    $this->calling($other)->connect        = function () use (&$called) {
                        $called = true;
                    },

                    $oldStack = $this->invoke($client)->getStack(),
                    $oldNodes = $client->getNodes()
                )
                ->when($result = $client->consider($other))
                ->then
                    ->object($result)
                        ->isIdenticalTo($client)
                    ->variable($called)
                        ->isEqualTo($disconnected ?: null)

                    ->let($stack = $this->invoke($client)->getStack())
                    ->array($stack)
                        ->hasSize(count($oldStack) + 1)
                    ->variable($stack[0])
                        ->isEqualTo($node->getSocket())

                    ->let($nodes = $client->getNodes())
                    ->array($nodes)
                        ->hasSize(count($oldNodes) + 1)
                    ->object($nodes[$node->getId()])
                        ->isEqualTo($node);
    }

    public function case_is()
    {
        $this
            ->_case_is('foo', 'foo')
                ->isTrue();
    }

    public function case_is_not()
    {
        $this
            ->_case_is('foo', 'bar')
                ->isFalse();
    }

    protected function _case_is($streamA, $streamB)
    {
        return
            $this
                ->given(
                    $this->mockGenerator->orphanize('__construct'),
                    $client = new \Mock\Hoa\Socket\Client(),

                    $this->mockGenerator->orphanize('__construct'),
                    $server = new \Mock\Hoa\Socket\Server(),

                    $this->calling($client)->getStream = $streamA,
                    $this->calling($server)->getStream = $streamB
                )
                ->when($result = $client->is($server))
                ->then
                    ->boolean($result);
    }

    public function case_is_connected()
    {
        $this
            ->_case_flag_is(SUT::CONNECT, 'isConnected');
    }

    public function case_is_asynchronous()
    {
        $this
            ->_case_flag_is(SUT::ASYNCHRONOUS, 'isAsynchronous');
    }

    public function case_is_persistent()
    {
        $this
            ->_case_flag_is(SUT::PERSISTENT, 'isPersistent');
    }

    protected function _case_flag_is($flag, $method)
    {
        return
            $this
                ->given(
                    $socket  = 'tcp://hoa-project.net:80',
                    $timeout = 42
                )
                ->when($result = new SUT($socket, $timeout, $flag))
                ->then
                    ->boolean($result->$method())
                        ->isTrue();
    }
}
