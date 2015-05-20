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
 * Class \Hoa\Socket\Server.
 *
 * Established a server connection.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Server extends Connection
{
    /**
     * Tell a stream to bind to the specified target.
     *
     * @const int
     */
    const BIND              = STREAM_SERVER_BIND;

    /**
     * Tell a stream to start listening on the socket.
     *
     * @const int
     */
    const LISTEN            = STREAM_SERVER_LISTEN;

    /**
     * Encryption: SSLv2.
     *
     * @const int
     */
    const ENCRYPTION_SSLv2  = STREAM_CRYPTO_METHOD_SSLv2_SERVER;

    /**
     * Encryption: SSLv3.
     *
     * @const int
     */
    const ENCRYPTION_SSLv3  = STREAM_CRYPTO_METHOD_SSLv3_SERVER;

    /**
     * Encryption: SSLv2.3.
     *
     * @const int
     */
    const ENCRYPTION_SSLv23 = STREAM_CRYPTO_METHOD_SSLv23_SERVER;

    /**
     * Encryption: TLS.
     *
     * @const int
     */
    const ENCRYPTION_TLS    = STREAM_CRYPTO_METHOD_TLS_SERVER;

    /**
     * Master connection.
     *
     * @var resource
     */
    protected $_master   = null;

    /**
     * All considered server.
     *
     * @var array
     */
    protected $_servers  = [];

    /**
     * Masters connection.
     *
     * @var array
     */
    protected $_masters  = [];

    /**
     * Stack of connections.
     *
     * @var array
     */
    protected $_stack    = [];



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
        $flag    = -1,
        $context = null
    ) {
        $this->setSocket($socket);
        $socket = $this->getSocket();

        if ($flag == -1) {
            switch ($socket->getTransport()) {
                case 'tcp':
                    $flag = self::BIND | self::LISTEN;

                    break;

                case 'udp':
                    $flag = self::BIND;

                    break;
            }
        } else {
            switch ($socket->getTransport()) {
                case 'tcp':
                    $flag &= self::LISTEN;

                    break;

                case 'udp':
                    if ($flag & self::LISTEN) {
                        throw new Exception(
                            'Cannot use the flag ' .
                            '\Hoa\Socket\Server::LISTEN ' .
                            'for connect-less transports (such as UDP).',
                            0
                        );
                    }

                    $flag = self::BIND;

                    break;
            }
        }

        parent::__construct(null, $timeout, $flag, $context);

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
            $this->_master = @stream_socket_server(
                $streamName,
                $errno,
                $errstr,
                $this->getFlag()
            );
        } else {
            $this->_master = @stream_socket_server(
                $streamName,
                $errno,
                $errstr,
                $this->getFlag(),
                $context->getContext()
            );
        }

        if (false === $this->_master) {
            throw new Exception(
                'Server cannot join %s and returns an error (number %d): %s.',
                1,
                [$streamName, $errno, $errstr]
            );
        }

        $i                  = count($this->_masters);
        $this->_masters[$i] = $this->_master;
        $this->_servers[$i] = $this;
        $this->_stack[]     = $this->_masters[$i];

        return $this->_master;
    }

    /**
     * Close the current stream.
     *
     * @return  bool
     */
    protected function _close()
    {
        $current = $this->getStream();

        if (false === in_array($current, $this->_masters, true)) {
            $i = array_search($current, $this->_stack);

            if (false !== $i) {
                unset($this->_stack[$i]);
            }

            // $this->_node is voluntary kept in memory until a new node will be
            // used.

            unset($this->_nodes[$this->getNodeId($current)]);

            @fclose($current);

            // Closing slave does not have the same effect that closing master.
            return false;
        }

        return (bool) (@fclose($this->_master) + @fclose($this->getStream()));
    }

    /**
     * Connect and accept the first connection.
     *
     * @return  \Hoa\Socket\Server
     * @throws  \Hoa\Socket\Exception
     */
    public function connect()
    {
        parent::connect();

        $client = @stream_socket_accept($this->_master);

        if (false === $client) {
            throw new Exception(
                'Operation timed out (nothing to accept).',
                2
            );
        }

        $this->_setStream($client);

        return $this;
    }

    /**
     * Connect but wait for select and accept new connections.
     *
     * @return  \Hoa\Socket\Server
     */
    public function connectAndWait()
    {
        return parent::connect();
    }

    /**
     * Select connections.
     *
     * @return  \Hoa\Socket\Server
     * @throws  \Hoa\Socket\Exception
     */
    public function select()
    {
        $read   = $this->_stack;
        $write  = null;
        $except = null;

        @stream_select($read, $write, $except, $this->getTimeout(), 0);

        foreach ($read as $socket) {
            if (true === in_array($socket, $this->_masters, true)) {
                $client = @stream_socket_accept($socket);

                if (false === $client) {
                    throw new Exception(
                        'Operation timed out (nothing to accept).',
                        3
                    );
                }

                $m      = array_search($socket, $this->_masters, true);
                $server = $this->_servers[$m];
                $id     = $this->getNodeId($client);
                $node   = dnew(
                    $server->getNodeName(),
                    [$id, $client, $server]
                );
                $this->_nodes[$id] = $node;
                $this->_stack[]    = $client;
            } else {
                $this->_iterator[] = $socket;
            }
        }

        return $this;
    }

    /**
     * Consider another server when selecting connection.
     *
     * @param   \Hoa\Socket\Connection  $other    Other server.
     * @return  \Hoa\Socket\Server
     */
    public function consider(Connection $other)
    {
        if ($other instanceof Client) {
            if (true === $other->isDisconnected()) {
                $other->connect();
            }

            $this->_stack[] = $other->getStream();

            return $this;
        }

        if (true === $other->isDisconnected()) {
            $other->connectAndWait();
        }

        $i                  = count($this->_masters);
        $this->_masters[$i] = $other->_master;
        $this->_servers[$i] = $other;
        $this->_stack[]     = $this->_masters[$i];

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
        return $this->_node->getConnection() === $server;
    }

    /**
     * Set and get the current selected connection.
     *
     * @return  \Hoa\Socket\Node
     */
    public function current()
    {
        $current = parent::_current();
        $id      = $this->getNodeId($current);

        if (!isset($this->_nodes[$id])) {
            return $current;
        }

        return $this->_node = $this->_nodes[$this->getNodeId($current)];
    }

    /**
     * Check if the server bind or not.
     *
     * @return  bool
     */
    public function isBinding()
    {
        return (bool) $this->getFlag() & self::BIND;
    }

    /**
     * Check if the server is listening or not.
     *
     * @return  bool
     */
    public function isListening()
    {
        return (bool) $this->getFlag() & self::LISTEN;
    }
}
