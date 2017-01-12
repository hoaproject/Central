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

namespace Hoa\Socket\Connection;

use Hoa\Exception as HoaException;
use Hoa\Socket;

/**
 * Class \Hoa\Socket\Connection\Handler.
 *
 * This class provides a connection handler: a complete connection skeleton.  We
 * are able to run() a connection (client or server), to merge() with other ones
 * and to send messages in different ways (A -> A, A -> B, A -> *\A etc.).
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Handler
{
    /**
     * Original connection.
     *
     * @var \Hoa\Socket\Connection
     */
    protected $_originalConnection = null;

    /**
     * Current connection.
     *
     * @var \Hoa\Socket\Connection
     */
    protected $_connection         = null;

    /**
     * All other connections that have been merged.
     *
     * @var array
     */
    protected $_connections        = [];



    /**
     * Constructor. Must be called.
     *
     * @param   \Hoa\Socket\Connection  $connection    Connection.
     */
    public function __construct(Connection $connection)
    {
        $this->_originalConnection = $connection;
        $this->setConnection($connection);

        return;
    }

    /**
     * Set current connection.
     *
     * @param   \Hoa\Socket\Connection  $connection    Connection.
     * @return  \Hoa\Socket\Connection
     */
    protected function setConnection(Connection $connection)
    {
        $old               = $this->_connection;
        $this->_connection = $connection;

        return $old;
    }

    /**
     * Get original connection.
     *
     * @return  \Hoa\Socket\Connection
     */
    protected function getOriginalConnection()
    {
        return $this->_originalConnection;
    }

    /**
     * Get current connection.
     *
     * @return  \Hoa\Socket\Connection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Get all merged connections.
     *
     * @return  array
     */
    public function getMergedConnections()
    {
        return $this->_connections;
    }

    /**
     * The node dedicated part of the run() method.
     * A run is pretty simple, schematically:
     *
     *     while (true) foreach ($connection->select() as $node)
     *         // body
     *
     * The body is given by this method.
     *
     * @param   \Hoa\Socket\Node  $node    Node.
     * @return  void
     */
    abstract protected function _run(Socket\Node $node);

    /**
     * Run the connection.
     *
     * @return  void
     */
    public function run()
    {
        $connection = $this->getConnection();

        if ($connection instanceof Socket\Server) {
            $connection->connectAndWait();
        } else {
            $connection->connect();
        }

        do {
            foreach ($connection->select() as $node) {
                // Connection has failed to detect the node, maybe it is a resource
                // from a merged client in a server.
                if (false === is_object($node)) {
                    $socket = $node;

                    foreach ($this->getMergedConnections() as $other) {
                        $otherConnection = $other->getOriginalConnection();

                        if (!($otherConnection instanceof Socket\Client)) {
                            continue;
                        }

                        $node = $otherConnection->getCurrentNode();

                        if ($node->getSocket() === $socket) {
                            $other->_run($node);

                            continue 2;
                        }
                    }
                }

                foreach ($this->getMergedConnections() as $other) {
                    if (true === $connection->is($other->getOriginalConnection())) {
                        $other->_run($node);

                        continue 2;
                    }
                }

                $this->_run($node);
            }
        } while (SUCCEED);

        $connection->disconnect();

        return;
    }

    /**
     * Merge a connection into this one.
     * If we have two connections that must run at the same time, the
     * Hoa\Socket\Connection::consider() and Hoa\Socket\Connection::is() methods
     * are helpful but this whole class eases the merge of “high-level”
     * connections.
     *
     * @param   \Hoa\Socket\Connection\Handler  $other    Connection to merge.
     * @return  \Hoa\Socket\Connection\Handler
     */
    public function merge(self $other)
    {
        $thisConnection  = $this->getConnection();
        $otherConnection = $other->getConnection();

        $thisConnection->consider($otherConnection);

        if ($otherConnection instanceof Socket\Server) {
            $other->setConnection($thisConnection);
        }

        $this->_connections[] = $other;

        return $this;
    }

    /**
     * The sending dedicated part of the self::send() method.
     * If the send() method is overrided with more arguments, this method could
     * return a function: it works like a currying.
     *
     * @param   string            $message    Message.
     * @param   \Hoa\Socket\Node  $node       Node (if null, current node).
     * @return  void
     */
    abstract protected function _send($message, Socket\Node $node);

    /**
     * Send a message to a specific node.
     *
     * @param   string            $message    Message.
     * @param   \Hoa\Socket\Node  $node       Node (if null, current node).
     *                                        current node).
     * @return  mixed
     */
    public function send($message, Socket\Node $node = null)
    {
        if (null === $node) {
            $node = $this->getConnection()->getCurrentNode();
        }

        if (null === $node) {
            return null;
        }

        $old = $this->getConnection()->_setStream($node->getSocket());

        try {
            $send = $this->_send($message, $node);
        } catch (\Exception $e) {
            $this->getConnection()->_setStream($old);

            throw $e;
        }

        if ($send instanceof \Closure) {
            $self = $this;

            return function () use (&$send, &$old, &$self) {
                try {
                    $out = call_user_func_array($send, func_get_args());
                } finally {
                    $self->getConnection()->_setStream($old);
                }

                return $out;
            };
        }

        $this->getConnection()->_setStream($old);

        return $send;
    }

    /**
     * Broadcast a message, i.e. send the message to all other nodes except the
     * current one.
     *
     * @param   string  $message    Message.
     * @param   …       …           …
     * @return  void
     */
    public function broadcast($message)
    {
        $currentNode = $this->getConnection()->getCurrentNode();
        $arguments   = func_get_args();
        array_unshift(
            $arguments,
            function (Socket\Node $node) use ($currentNode) {
                return $node !== $currentNode;
            }
        );

        return call_user_func_array([$this, 'broadcastIf'], $arguments);
    }

    /**
     * Broadcast a message to a subset of nodes that fulfill a predicate.
     *
     * @param   \Closure  $predicate    Predicate. Take a node in argument.
     * @param   string    $message      Message.
     * @param   …         …             …
     * @return  void
     * @throws  \Hoa\Exception\Group
     */
    public function broadcastIf(\Closure $predicate, $message)
    {
        $connection    = $this->getConnection();
        $currentSocket = $this->getOriginalConnection()->getSocket();

        $arguments = array_slice(func_get_args(), 2);
        array_unshift($arguments, $message, null);
        $callable   = [$this, 'send'];
        $exceptions = new HoaException\Group(
            'Message cannot be sent to some nodes.'
        );

        foreach ($connection->getNodes() as $node) {
            if (true === $predicate($node) &&
                $node->getConnection()->getSocket() === $currentSocket) {
                $arguments[1] = $node;
                try {
                    call_user_func_array($callable, $arguments);
                } catch (Socket\Exception $e) {
                    $exceptions[$node->getId()] = $e;
                }
            }
        }

        if (0 < $exceptions->count()) {
            throw $exceptions;
        }

        return;
    }
}
