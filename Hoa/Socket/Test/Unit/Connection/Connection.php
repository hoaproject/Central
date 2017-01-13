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
use Mock\Hoa\Socket\Connection as SUT;

/**
 * Class \Hoa\Socket\Test\Unit\Connection\Connection.
 *
 * Test suite of the connection class.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Connection extends Test\Unit\Suite
{
    public function case_interfaces()
    {
        $this
            ->given($this->mockGenerator->orphanize('__construct'))
            ->when($result = new SUT())
            ->then
                ->object($result)
                    ->isInstanceOf('Hoa\Stream')
                    ->isInstanceOf('Hoa\Stream\IStream\In')
                    ->isInstanceOf('Hoa\Stream\IStream\Out')
                    ->isInstanceOf('Hoa\Stream\IStream\Pathable')
                    ->isInstanceOf('Iterator');
    }

    public function case_constructor()
    {
        $this
            ->given(
                $socket  = 'tcp://hoa-project.net:80',
                $timeout = 42,
                $flag    = 153,
                $context = 'context'
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
                    ->isEqualTo($flag)
                ->string($result->getContext())
                    ->isEqualTo($context);
    }

    public function case_connect()
    {
        $this
            ->given(
                $socket     = 'tcp://hoa-project.net:80',
                $timeout    = 42,
                $flag       = 153,
                $connection = new SUT($socket, $timeout, $flag),

                $this->calling($connection)->open = function () use (&$called) {
                    $called = true;
                },
                $oldDisconnected = $connection->isDisconnected()
            )
            ->when($result = $connection->connect())
            ->then
                ->object($result)
                    ->isIdenticalTo($connection)
                ->boolean($oldDisconnected)
                    ->isTrue()
                ->boolean($connection->isDisconnected())
                    ->isFalse();
    }

    public function case__current()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $current    = 'foo',

                $this->function->current                = $current,
                $this->calling($connection)->_setStream = function ($_current) use ($self, &$called, $current) {
                    $called = true;

                    $self
                        ->string($_current)
                            ->isEqualTo($current);

                    return $current;
                }
            )
            ->when($result = $this->invoke($connection)->_current())
            ->then
                ->string($result)
                    ->isEqualTo($current)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_iterator()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new \Mock\Hoa\Socket\Test\Unit\Connection\ConnectionIterator(),

                $this->calling($connection)->_setStream[1] = function ($current) use ($self, &$called0) {
                    $called0 = true;

                    $self
                        ->string($current)
                            ->isEqualTo('foo');
                },
                $this->calling($connection)->_setStream[2] = function ($current) use ($self, &$called1) {
                    $called1 = true;

                    $self
                        ->string($current)
                            ->isEqualTo('bar');
                },
                $this->calling($connection)->_setStream[3] = function ($current) use ($self, &$called2) {
                    $called2 = true;

                    $self
                        ->string($current)
                            ->isEqualTo('baz');
                }
            )
            ->when($result = iterator_to_array($connection))
            ->then
                ->array($result)
                    ->isEqualTo(['foo', 'bar', 'baz'])
                ->boolean($called0)
                    ->isTrue()
                ->boolean($called1)
                    ->isTrue()
                ->boolean($called2)
                    ->isTrue();
    }

    public function case_quiet()
    {
        $this->_case_shutdown('quiet', STREAM_SHUT_RD, 'isQuiet');
    }

    public function case_mute()
    {
        return $this->_case_shutdown('mute', STREAM_SHUT_WR, 'isMute');
    }

    public function case_quiet_and_mute()
    {
        return $this->_case_shutdown('quietAndMute', STREAM_SHUT_RDWR, 'isDisconnected');
    }

    public function _case_shutdown($method, $how, $isMethod)
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 42,

                $this->calling($connection)->getStream = $stream,
                $this->function->stream_socket_shutdown = function ($_stream, $_how) use ($self, &$called, $stream, $how) {
                    $called = true;

                    $self
                        ->integer($_stream)
                            ->isEqualTo($stream)
                        ->integer($how)
                            ->isEqualTo($how);

                    return true;
                }
            )
            ->when($result = $connection->$method())
            ->then
            ->boolean($result)
                ->isTrue()
            ->boolean($called)
                ->isTrue()
            ->boolean($connection->$isMethod())
                ->isTrue();
    }

    public function case_disconnect()
    {
        $this
            ->given(
                $socket     = 'tcp://hoa-project.net:80',
                $timeout    = 42,
                $flag       = 153,
                $connection = new SUT($socket, $timeout, $flag),
                $connection->connect(),

                $this->calling($connection)->open   = null,
                $this->calling($connection)->_close = function () use (&$called) {
                    $called = true;
                }
            )
            ->when($result = $connection->disconnect())
            ->then
                ->variable($result)
                    ->isNull()
                ->boolean($called)
                    ->isTrue()
                ->boolean($connection->isDisconnected())
                    ->isTrue();
    }

    public function case_set_socket_not_a_protocol()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $socketUri  = 'foobar'
            )
            ->exception(function () use ($connection, $socketUri) {
                $this->invoke($connection)->setSocket($socketUri);
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_set_socket()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $socketUri  = 'tcp://hoa-project.net:80'
            )
            ->when($result = $this->invoke($connection)->setSocket($socketUri))
            ->then
                ->variable($result)
                    ->isNull()
                ->let($socket = $connection->getSocket())
                ->object($socket)
                    ->isInstanceOf('Hoa\Socket\Socket')
                ->integer($socket->getAddressType())
                    ->isEqualTo($socket::ADDRESS_DOMAIN)
                ->string($socket->getTransport())
                    ->isEqualTo('tcp')
                ->string($socket->getAddress())
                    ->isEqualTo('hoa-project.net')
                ->integer($socket->getPort())
                    ->isEqualTo(80)
                ->boolean($socket->isSecured())
                    ->isFalse();
    }

    public function case_set_timeout()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $timeout    = 42
            )
            ->when($result = $this->invoke($connection)->setTimeout($timeout))
            ->then
                ->integer($result)
                    ->isEqualTo(30)
                ->integer($connection->getTimeout())
                    ->isEqualTo($timeout);
    }

    public function case_set_flag()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $flag       = 42
            )
            ->when($result = $this->invoke($connection)->setFlag($flag))
            ->then
                ->integer($result)
                    ->isEqualTo(0)
                ->integer($connection->getFlag())
                    ->isEqualTo($flag);
    }

    public function case_set_context()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $context    = 'my_context'
            )
            ->when($result = $this->invoke($connection)->setContext($context))
            ->then
                ->variable($result)
                    ->isNull()
                ->string($connection->getContext())
                    ->isEqualTo($context);
    }

    public function case_set_node_name()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $nodeName   = 'My\Node'
            )
            ->when($result = $connection->setNodeName($nodeName))
            ->then
                ->string($result)
                    ->isEqualTo('Hoa\Socket\Node')
                ->string($connection->getNodeName())
                    ->isEqualTo($nodeName);
    }

    public function case_consider_remote_address()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $consider   = true
            )
            ->when($result = $connection->considerRemoteAddress($consider))
            ->then
                ->boolean($result)
                    ->isFalse()
                ->boolean($connection->isRemoteAddressConsidered())
                    ->isEqualTo($consider);
    }

    public function case_enable_encryption_without_current_node()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),

                $this->calling($connection)->getCurrentNode = null
            )
            ->when($result = $connection->enableEncryption(true))
            ->then
                ->boolean($result)
                    ->isFalse()
                ->boolean($connection->isEncrypted())
                    ->isFalse();
    }

    public function case_enable_encryption_without_type()
    {
        $self = $this;

        $this
            ->given(
                $enable = true,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = fopen(__FILE__, 'r'),
                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getCurrentNode  = $node,
                $this->calling($connection)->getStream       = $stream,
                $this->calling($node)->getEncryptionType     = null,
                $this->function->stream_socket_enable_crypto = function ($_stream, $_enable) use ($self, &$called, $stream, $enable) {
                    $called = true;

                    $self
                        ->resource($_stream)
                            ->isIdenticalTo($stream)
                        ->boolean($_enable)
                            ->isEqualTo($enable);

                    return true;
                }
            )
            ->when($result = $connection->enableEncryption($enable))
            ->then
                ->boolean($result)
                    ->isTrue()
                ->boolean($called)
                    ->isTrue()
                ->boolean($connection->isEncrypted())
                    ->isFalse();
    }

    public function case_enable_encryption_without_session_stream()
    {
        $self = $this;

        $this
            ->given(
                $enable = true,
                $type   = LUT\Client::ENCRYPTION_TLS,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = fopen(__FILE__, 'r'),
                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getCurrentNode = $node,
                $this->calling($connection)->getStream      = $stream,
                $this->calling($node)->setEncryptionType    = function ($_type) use ($self, &$called0, $type) {
                    $called0 = true;

                    $self
                        ->integer($_type)
                            ->isEqualTo($type);

                    return;
                },
                $this->function->stream_socket_enable_crypto = function ($_stream, $_enable, $_type) use ($self, &$called1, $stream, $enable, $type) {
                    $called1 = true;

                    $self
                        ->resource($_stream)
                            ->isIdenticalTo($stream)
                        ->boolean($_enable)
                            ->isEqualTo($enable)
                        ->integer($_type)
                            ->isEqualTo($type);

                    return true;
                }
            )
            ->when($result = $connection->enableEncryption($enable, $type))
            ->then
                ->boolean($result)
                    ->isTrue()
                ->boolean($called0)
                    ->isTrue()
                ->boolean($called1)
                    ->isTrue();
    }

    public function case_enable_encryption()
    {
        $self = $this;

        $this
            ->given(
                $enable        = true,
                $type          = LUT\Client::ENCRYPTION_TLS,
                $sessionStream = 42,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = fopen(__FILE__, 'r'),
                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getCurrentNode = $node,
                $this->calling($connection)->getStream      = $stream,
                $this->calling($node)->setEncryptionType    = function ($_type) use ($self, &$called0, $type) {
                    $called0 = true;

                    $self
                        ->integer($_type)
                            ->isEqualTo($type);

                    return;
                },
                $this->function->stream_socket_enable_crypto = function ($_stream, $_enable, $_type, $_sessionStream) use ($self, &$called1, $stream, $enable, $type, $sessionStream) {
                    $called1 = true;

                    $self
                        ->resource($_stream)
                            ->isIdenticalTo($stream)
                        ->boolean($_enable)
                            ->isEqualTo($enable)
                        ->integer($_type)
                            ->isEqualTo($type)
                        ->integer($_sessionStream)
                            ->isEqualTo($sessionStream);

                    return true;
                }
            )
            ->when($result = $connection->enableEncryption($enable, $type, $sessionStream))
            ->then
                ->boolean($result)
                    ->isTrue()
                ->boolean($called0)
                    ->isTrue()
                ->boolean($called1)
                    ->isTrue();
    }

    public function case_is_encrypted_after_a_successful_encryption()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $this->mockGenerator->orphanize('__construct'),
                $node = new \Mock\Hoa\Socket\Node(),

                $this->calling($connection)->getCurrentNode = $node,
                $this->calling($node)->getEncryptionType    = LUT\Client::ENCRYPTION_TLS
            )
            ->when($result = $connection->isEncrypted())
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_get_node_id()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $resource   = fopen(__FILE__, 'r')
            )
            ->when($result = $this->invoke($connection)->getNodeId($resource))
            ->then
                ->string($result)
                    ->isEqualTo(md5((int) $resource));
    }

    public function case_read_on_a_null_stream()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),

                $this->calling($connection)->getStream = null
            )
            ->exception(function () use ($connection) {
                $connection->read(42);
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_read_a_negative_length()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),

                $this->calling($connection)->getStream = 'foo'
            )
            ->exception(function () use ($connection) {
                $connection->read(-1);
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_read_encrypted_node()
    {
        $self = $this;

        $this
            ->given(
                $length = 42,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',

                $this->calling($connection)->getStream   = $stream,
                $this->calling($connection)->isEncrypted = true,
                $this->function->fread                   = function ($_stream, $_length) use ($self, &$called, $stream, $length) {
                    $called = true;

                    $this
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->integer($_length)
                            ->isEqualTo($length);

                    return $_length;
                }
            )
            ->when($result = $connection->read($length))
            ->then
                ->integer($result)
                    ->isEqualTo($length)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_read_without_remote_address_considered()
    {
        $self = $this;

        $this
            ->given(
                $length = 42,
                $flags  = 0,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $flags      = SUT::READ_OUT_OF_BAND,
                $connection->considerRemoteAddress(false),
                $stream     = 'foo',

                $this->calling($connection)->getStream   = $stream,
                $this->calling($connection)->isEncrypted = false,
                $this->function->stream_socket_recvfrom  = function ($_stream, $_length, $_flags) use ($self, &$called, $stream, $length, $flags) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->integer($_length)
                            ->isEqualTo($length)
                        ->integer($_flags)
                            ->isEqualTo($flags);

                    return 'hello';
                }
            )
            ->when($result = $connection->read($length, $flags))
            ->then
                ->string($result)
                    ->isEqualTo('hello')
                ->boolean($called)
                    ->isTrue();
    }

    public function case_read()
    {
        $self = $this;

        $this
            ->given(
                $length = 42,
                $flags  = 0,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $flags      = SUT::READ_OUT_OF_BAND,
                $connection->considerRemoteAddress(true),
                $stream     = 'foo',

                $this->calling($connection)->getStream   = $stream,
                $this->calling($connection)->isEncrypted = false,
                $this->function->stream_socket_recvfrom  = function ($_stream, $_length, $_flags, &$address) use ($self, &$called, $stream, $length, $flags) {
                    $called  = true;
                    $address = '1.2.3.4';

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->integer($_length)
                            ->isEqualTo($length)
                        ->integer($_flags)
                            ->isEqualTo($flags);

                    return 'hello';
                }
            )
            ->when($result = $connection->read($length, $flags))
            ->then
                ->string($result)
                    ->isEqualTo('hello')
                ->boolean($called)
                    ->isTrue()
                ->string($connection->getRemoteAddress())
                    ->isEqualTo('1.2.3.4');
    }

    public function case_read_string()
    {
        $this
            ->given(
                $length = 3,
                $output = 'foobar'
            )
            ->_case_read($output, $connection)
            ->when($result = $connection->readString($length))
            ->then
                ->string($result)
                    ->isEqualTo('foo');
    }

    public function case_read_character()
    {
        $this
            ->given($output = 'foobar')
            ->_case_read($output, $connection)
            ->when($result = $connection->readCharacter())
            ->then
                ->string($result)
                    ->isEqualTo('f');
    }

    public function case_read_boolean_true()
    {
        $this
            ->given($output = '1')
            ->_case_read($output, $connection)
            ->when($result = $connection->readBoolean())
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_read_boolean_false()
    {
        $this
            ->given($output = '0')
            ->_case_read($output, $connection)
            ->when($result = $connection->readBoolean())
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_read_integer()
    {
        $this
            ->given($output = '42foobar')
            ->_case_read($output, $connection)
            ->when($result = $connection->readInteger(2))
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    public function case_read_float()
    {
        $this
            ->given($output = '4.2foobar')
            ->_case_read($output, $connection)
            ->when($result = $connection->readFloat(3))
            ->then
                ->float($result)
                    ->isEqualTo(4.2);
    }

    public function case_read_array()
    {
        $this
            ->given($output = 'foo bar')
            ->_case_read($output, $connection)
            ->when($result = $connection->readArray('%s %s'))
            ->then
                ->array($result)
                    ->isEqualTo([
                        0 => 'foo',
                        1 => 'bar'
                    ]);
    }

    public function case_read_line_with_encryption()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream = 'foo',

                $this->calling($connection)->isEncrypted = true,
                $this->calling($connection)->getStream   = 'foo',
                $this->function->fgets                   = function ($_stream, $length) use ($self, &$called, $stream) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->integer($length)
                            ->isEqualTo(1 << 15);

                    return 'foobar' . "\n\n";
                }
            )
            ->when($result = $connection->readLine())
            ->then
                ->string($result)
                    ->isEqualTo('foobar')
                ->boolean($called)
                    ->isTrue();
    }

    public function case_read_line_without_encryption()
    {
        $self = $this;

        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream = 'foo',

                $this->calling($connection)->isEncrypted = false,
                $this->calling($connection)->getStream   = 'foo',
                $this->function->stream_get_line         = function ($_stream, $length) use ($self, &$called, $stream) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->integer($length)
                            ->isEqualTo(1 << 15);

                    return 'foobar';
                }
            )
            ->when($result = $connection->readLine())
            ->then
                ->string($result)
                    ->isEqualTo('foobar')
                ->boolean($called)
                    ->isTrue();
    }

    public function case_read_all()
    {
        $this
            ->given($output = 'foobar' . "\n")
            ->_case_read($output, $connection)
            ->when($result = $connection->readAll())
            ->then
                ->string($result)
                    ->isEqualTo($output);
    }

    public function case_scanf()
    {
        $this
            ->given($output = 'foo bar')
            ->_case_read($output, $connection)
            ->when($result = $connection->scanf('%s %s'))
            ->then
                ->array($result)
                    ->isEqualTo([
                        0 => 'foo',
                        1 => 'bar'
                    ]);
    }

    protected function _case_read($output, &$connection)
    {
        return
            $this
                ->given(
                    $this->mockGenerator->orphanize('__construct'),
                    $connection = new SUT(),
                    $stream     = 'foo',

                    $this->calling($connection)->getStream   = $stream,
                    $this->calling($connection)->isEncrypted = false,
                    $this->function->stream_socket_recvfrom  = function ($_stream, $_length) use ($output) {
                        return substr($output, 0, $_length);
                    },
                    $this->function->stream_get_contents = $output
                );
    }

    public function case_write_on_a_null_stream()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),

                $this->calling($connection)->getStream = null
            )
            ->exception(function () use ($connection) {
                $connection->write('foo', 3);
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_write_a_negative_length()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',

                $this->calling($connection)->getStream = $stream
            )
            ->exception(function () use ($connection) {
                $connection->write('foo', -1);
            })
                ->isInstanceOf('Hoa\Socket\Exception');
    }

    public function case_write_on_encrypted_node()
    {
        $self = $this;

        $this
            ->given(
                $string = 'foobar',
                $length = 3,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',

                $this->calling($connection)->getStream   = $stream,
                $this->calling($connection)->isEncrypted = true,
                $this->function->fwrite                  = function ($_stream, $_string, $_length) use ($self, &$called, $stream, $string, $length) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->string($_string)
                            ->hasLength($_length)
                            ->hasLength($length)
                            ->isEqualTo(substr($string, 0, $length));

                    return $_length;
                }
            )
            ->when($result = $connection->write($string, $length))
            ->then
                ->integer($result)
                    ->isEqualTo($length)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_write_without_remote_address_considered()
    {
        $self = $this;

        $this
            ->given(
                $string = 'foobar',
                $length = 3,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',

                $this->calling($connection)->getStream                 = $stream,
                $this->calling($connection)->isEncrypted               = false,
                $this->calling($connection)->isRemoteAddressConsidered = false,
                $this->function->stream_socket_sendto                  = function ($_stream, $_string) use ($self, &$called, $stream, $string, $length) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->string($_string)
                            ->hasLength($length)
                            ->isEqualTo(substr($string, 0, $length));

                    return $length;
                }
            )
            ->when($result = $connection->write($string, $length))
            ->then
                ->integer($result)
                    ->isEqualTo($length)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_write_without_remote_address_considered_broken_pipe()
    {
        $self = $this;

        $this
            ->given(
                $string = 'foobar',
                $length = 3,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',

                $this->calling($connection)->getStream                 = $stream,
                $this->calling($connection)->isEncrypted               = false,
                $this->calling($connection)->isRemoteAddressConsidered = false,
                $this->function->stream_socket_sendto                  = -1
            )
            ->exception(function () use ($connection, $string, $length) {
                $connection->write($string, $length);
            })
                ->isInstanceOf('Hoa\Socket\Exception\BrokenPipe');
    }

    public function case_write()
    {
        $self = $this;

        $this
            ->given(
                $string = 'foobar',
                $length = 3,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',
                $remote     = '1.2.3.4',

                $this->calling($connection)->getStream                 = $stream,
                $this->calling($connection)->isEncrypted               = false,
                $this->calling($connection)->isRemoteAddressConsidered = true,
                $this->calling($connection)->getRemoteAddress          = $remote,
                $this->function->stream_socket_sendto                  = function ($_stream, $_string, $_flags, $_remote) use ($self, &$called, $stream, $string, $length, $remote) {
                    $called = true;

                    $self
                        ->string($_stream)
                            ->isEqualTo($stream)
                        ->string($_string)
                            ->hasLength($length)
                            ->isEqualTo(substr($string, 0, $length))
                        ->integer($_flags)
                            ->isEqualTo(0)
                        ->string($_remote)
                            ->isEqualTo($remote);

                    return $length;
                }
            )
            ->when($result = $connection->write($string, $length))
            ->then
                ->integer($result)
                    ->isEqualTo($length)
                ->boolean($called)
                    ->isTrue();
    }

    public function case_write_broken_pipe()
    {
        $self = $this;

        $this
            ->given(
                $string = 'foobar',
                $length = 3,

                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $stream     = 'foo',
                $remote     = '1.2.3.4',

                $this->calling($connection)->getStream                 = $stream,
                $this->calling($connection)->isEncrypted               = false,
                $this->calling($connection)->isRemoteAddressConsidered = true,
                $this->calling($connection)->getRemoteAddress          = $remote,
                $this->function->stream_socket_sendto                  = -1
            )
            ->exception(function () use ($connection, $string, $length) {
                $connection->write($string, $length);
            })
                ->isInstanceOf('Hoa\Socket\Exception\BrokenPipe');
    }

    public function case_base_name()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $socketUri  = 'tcp://hoa-project.net:80/foo/bar',
                $this->invoke($connection)->setSocket($socketUri)
            )
            ->when($result = $connection->getBasename())
            ->then
                ->string($result)
                    ->isEqualTo('bar');
    }

    public function case_base_name_of_a_domain()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $socketUri  = 'tcp://hoa-project.net:80',
                $this->invoke($connection)->setSocket($socketUri)
            )
            ->when($result = $connection->getBasename())
            ->then
                ->string($result)
                    ->isEqualTo('hoa-project.net:80');
    }

    public function case_directory_name()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $socketUri  = 'tcp://hoa-project.net:80/foo/bar',
                $this->invoke($connection)->setSocket($socketUri)
            )
            ->when($result = $connection->getDirname())
            ->then
                ->string($result)
                    ->isEqualTo('tcp://hoa-project.net:80/foo');
    }

    public function case_directory_name_of_a_domain()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $connection = new SUT(),
                $socketUri  = 'tcp://hoa-project.net:80',
                $this->invoke($connection)->setSocket($socketUri)
            )
            ->when($result = $connection->getBasename())
            ->then
                ->string($result)
                    ->isEqualTo('hoa-project.net:80');
    }
}

abstract class ConnectionIterator extends \Hoa\Socket\Connection\Connection
{
    protected $_iterator = ['foo', 'bar', 'baz'];

    public function current()
    {
        return $this->_current();
    }
}
