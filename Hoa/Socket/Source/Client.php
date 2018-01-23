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

namespace Hoa\Socket;

use Hoa\Consistency;
use Hoa\Stream;

/**
 * Class \Hoa\Socket\Client.
 *
 * Established a client connection.
 */
class Client extends Connection
{
    /**
     * Open client socket asynchronously.
     */
    public const ASYNCHRONOUS       = STREAM_CLIENT_ASYNC_CONNECT;

    /**
     * Open client socket connection.
     */
    public const CONNECT            = STREAM_CLIENT_CONNECT;

    /**
     * Client socket should remain persistent between page loads.
     */
    public const PERSISTENT         = STREAM_CLIENT_PERSISTENT;

    /**
     * Encryption: SSLv2.
     */
    public const ENCRYPTION_SSLv2   = STREAM_CRYPTO_METHOD_SSLv2_CLIENT;

    /**
     * Encryption: SSLv3.
     */
    public const ENCRYPTION_SSLv3   = STREAM_CRYPTO_METHOD_SSLv3_CLIENT;

    /**
     * Encryption: SSLv2.3.
     */
    public const ENCRYPTION_SSLv23  = STREAM_CRYPTO_METHOD_SSLv23_CLIENT;

    /**
     * Encryption: TLS.
     */
    public const ENCRYPTION_TLS     = STREAM_CRYPTO_METHOD_TLS_CLIENT;

    /**
     * Encryption: TLSv1.0.
     */
    public const ENCRYPTION_TLSv1_0 = STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT;

    /**
     * Encryption: TLSv1.1.
     */
    public const ENCRYPTION_TLSv1_1 = STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;

    /**
     * Encryption: TLSv1.2.
     */
    public const ENCRYPTION_TLSv1_2 = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;

    /**
     * Encryption: ANY
     */
    public const ENCRYPTION_ANY     = STREAM_CRYPTO_METHOD_ANY_CLIENT;

    /**
     * Stack of connections.
     */
    protected $_stack = [];



    /**
     * Start a connection.
     */
    public function __construct(
        string $socket,
        int $timeout    = 30,
        int $flag       = self::CONNECT,
        string $context = null
    ) {
        parent::__construct($socket, $timeout, self::CONNECT | $flag, $context);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     */
    protected function &_open(string $streamName, Stream\Context $context = null)
    {
        if (null === $context) {
            $connection = @stream_socket_client(
                $streamName,
                $errno,
                $errstr,
                $this->getTimeout(),
                $this->getFlag()
            );
        } else {
            $connection = @stream_socket_client(
                $streamName,
                $errno,
                $errstr,
                $this->getTimeout(),
                $this->getFlag(),
                $context->getContext()
            );
        }

        if (false === $connection) {
            if ($errno === 0) {
                throw new Exception('Client cannot join %s.', 0, $streamName);
            } else {
                throw new Exception(
                    'Client returns an error (number %d): %s while trying ' .
                    'to join %s.',
                    1,
                    [$errno, $errstr, $streamName]
                );
            }
        }

        $this->_stack[] = $connection;
        $id             = $this->getNodeId($connection);
        $this->_node    = Consistency\Autoloader::dnew(
            $this->getNodeName(),
            [$id, $connection, $this]
        );
        $this->_nodes[$id] = $this->_node;

        return $connection;
    }

    /**
     * Close the current stream.
     */
    protected function _close(): bool
    {
        if (true === $this->isPersistent()) {
            return false;
        }

        return @fclose($this->getStream());
    }

    /**
     * Select connections.
     */
    public function select(): iterable
    {
        $read   = $this->getStack();
        $write  = null;
        $except = null;

        @stream_select($read, $write, $except, $this->getTimeout(), 0);

        foreach ($read as $socket) {
            $this->_iterator[] = $socket;
        }

        return $this;
    }

    /**
     * Consider another client when selecting connection.
     */
    public function consider(Connection $other): Connection
    {
        if (!($other instanceof self)) {
            throw new Exception(
                'Other client must be of type %s.',
                2,
                __CLASS__
            );
        }

        if (true === $other->isDisconnected()) {
            $other->connect();
        }

        $otherNode                         = $other->getCurrentNode();
        $this->_stack[]                    = $otherNode->getSocket();
        $this->_nodes[$otherNode->getId()] = $otherNode;

        return $this;
    }

    /**
     * Check if the current node belongs to a specific server.
     */
    public function is(Connection $server): bool
    {
        return $this->getStream() === $server->getStream();
    }

    /**
     * Set and get the current selected connection.
     */
    public function current(): Node
    {
        $current = parent::_current();

        return $this->_node = $this->_nodes[$this->getNodeId($current)];
    }

    /**
     * Check if the connection is connected or not.
     */
    public function isConnected(): bool
    {
        return (bool) ($this->getFlag() & self::CONNECT);
    }

    /**
     * Check if the connection is asynchronous or not.
     */
    public function isAsynchronous(): bool
    {
        return (bool) ($this->getFlag() & self::ASYNCHRONOUS);
    }

    /**
     * Check if the connection is persistent or not.
     */
    public function isPersistent(): bool
    {
        return (bool) ($this->getFlag() & self::PERSISTENT);
    }

    /**
     * Return internal node stack.
     */
    protected function getStack(): array
    {
        return $this->_stack;
    }
}
