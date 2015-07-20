<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

use Hoa\Stream;

/**
 * Class \Hoa\Socket\Client.
 *
 * Established a client connection.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Client extends Connection
{
    /**
     * Open client socket asynchronously.
     *
     * @const int
     */
    const ASYNCHRONOUS      = STREAM_CLIENT_ASYNC_CONNECT;

    /**
     * Open client socket connection.
     *
     * @const int
     */
    const CONNECT           = STREAM_CLIENT_CONNECT;

    /**
     * Client socket should remain persistent between page loads.
     *
     * @const int
     */
    const PERSISTENT        = STREAM_CLIENT_PERSISTENT;

    /**
     * Encryption: SSLv2.
     *
     * @const int
     */
    const ENCRYPTION_SSLv2  = STREAM_CRYPTO_METHOD_SSLv2_CLIENT;

    /**
     * Encryption: SSLv3.
     *
     * @const int
     */
    const ENCRYPTION_SSLv3  = STREAM_CRYPTO_METHOD_SSLv3_CLIENT;

    /**
     * Encryption: SSLv2.3.
     *
     * @const int
     */
    const ENCRYPTION_SSLv23 = STREAM_CRYPTO_METHOD_SSLv23_CLIENT;

    /**
     * Encryption: TLS.
     *
     * @const int
     */
    const ENCRYPTION_TLS    = STREAM_CRYPTO_METHOD_TLS_CLIENT;

    /**
     * Stack of connections.
     *
     * @var array
     */
    protected $_stack = [];



    /**
     * Start a connection.
     *
     * @param   string  $socket     Socket URI.
     * @param   int     $timeout    Timeout.
     * @param   int     $flag       Flag, see the child::* constants.
     * @param   string  $context    Context ID (please, see the
     *                              \Hoa\Stream\Context class).
     * @return  void
     */
    public function __construct(
        $socket,
        $timeout = 30,
        $flag    = self::CONNECT,
        $context = null
    ) {
        parent::__construct($socket, $timeout, self::CONNECT & $flag, $context);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @param   string               $streamName    Socket URI.
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     * @throws  \Hoa\Socket\Exception
     */
    protected function &_open($streamName, Stream\Context $context = null)
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
            if ($errno == 0) {
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

        $this->_stack[]    = $connection;
        $id                = $this->getNodeId($connection);
        $this->_node       = dnew($this->getNodeName(), [$id, $connection, $this]);
        $this->_nodes[$id] = $this->_node;

        return $connection;
    }

    /**
     * Close the current stream.
     *
     * @return  bool
     */
    protected function _close()
    {
        if (true === $this->isPersistent()) {
            return false;
        }

        return @fclose($this->getStream());
    }

    /**
     * Select connections.
     *
     * @return  \Hoa\Socket\Client
     */
    public function select()
    {
        $read   = $this->_stack;
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
     *
     * @param   \Hoa\Socket\Connection  $other    Other client.
     * @return  \Hoa\Socket\Client
     */
    public function consider(Connection $other)
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
     *
     * @param   \Hoa\Socket\Connection  $server    Server.
     * @return  bool
     */
    public function is(Connection $server)
    {
        return $this->getStream() === $server->getStream();
    }

    /**
     * Set and get the current selected connection.
     *
     * @return  \Hoa\Socket\Node
     */
    public function current()
    {
        $current = parent::_current();

        return $this->_node = $this->_nodes[$this->getNodeId($current)];
    }

    /**
     * Check if the connection is connected or not.
     *
     * @return  bool
     */
    public function isConnected()
    {
        return (bool) ($this->getFlag() & self::CONNECT);
    }

    /**
     * Check if the connection is asynchronous or not.
     *
     * @return  bool
     */
    public function isAsynchronous()
    {
        return (bool) ($this->getFlag() & self::ASYNCHRONOUS);
    }

    /**
     * Check if the connection is persistent or not.
     *
     * @return  bool
     */
    public function isPersistent()
    {
        return (bool) ($this->getFlag() & self::PERSISTENT);
    }
}
