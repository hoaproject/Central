<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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

namespace {

from('Hoa')

/**
 * \Hoa\Socket\Connection\Exception
 */
-> import('Socket.Connection.Exception')

/**
 * \Hoa\Socket\Connection
 */
-> import('Socket.Connection')

/**
 * \Hoa\Socket\Socketable
 */
-> import('Socket.Socketable');

}

namespace Hoa\Socket\Connection {

/**
 * Class \Hoa\Socket\Connection\Client.
 *
 * Established a client connection.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Client extends Connection {

    /**
     * Open client socket asynchronously.
     *
     * @const int
     */
    const ASYNCHRONOUS = STREAM_CLIENT_ASYNC_CONNECT;

    /**
     * Open client socket connection.
     *
     * @const int
     */
    const CONNECT      = STREAM_CLIENT_CONNECT;

    /**
     * Client socket should remain persistent between page loads.
     *
     * @const int
     */
    const PERSISTENT   = STREAM_CLIENT_PERSISTENT;



    /**
     * Constructor.
     * Configure a socket.
     *
     * @access  public
     * @param   \Hoa\Socket\Socketable  $socket     Socket.
     * @param   int                     $timeout    Timeout.
     * @param   int                     $flag       Flag, see the self::* constants.
     * @param   string                  $context    Context ID (please, see the
     *                                              \Hoa\Stream\Context class).
     * @return  void
     */
    public function __construct ( \Hoa\Socket\Socketable $socket, $timeout = 30,
                                  $flag = self::CONNECT, $context = null ) {

        parent::__construct($socket, $timeout, self::CONNECT & $flag, $context);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string               $streamName    Socket name (e.g. path or URL).
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     * @throw   \Hoa\Socket\Connection\Exception
     */
    protected function &_open ( $streamName, \Hoa\Stream\Context $context = null ) {

        if(null === $context)
            $connection = @stream_socket_client(
                $streamName,
                $errno,
                $errstr,
                $this->getTimeout(),
                $this->getFlag()
            );
        else
            $connection = @stream_socket_client(
                $streamName,
                $errno,
                $errstr,
                $this->getTimeout(),
                $this->getFlag(),
                $context->getContext()
            );

        if(false === $connection)
            if($errno == 0)
                throw new Exception(
                    'Client cannot join %s.', 0, $streamName);
            else
                throw new Exception(
                    'Client returns an error (number %d): %s.',
                    1, array($errno, $errstr));

        return $connection;
    }

    /**
     * Close the current stream.
     *
     * @access  protected
     * @return  bool
     */
    protected function _close ( ) {

        if(true === $this->isPersistent())
            return false;

        return @fclose($this->getStream());
    }

    /**
     * Check if the connection is connected or not.
     *
     * @access  public
     * @return  bool
     */
    public function isConnected ( ) {

        return (bool) $this->getFlag() & self::CONNECT;
    }

    /**
     * Check if the connection is asynchronous or not.
     *
     * @access  public
     * @return  bool
     */
    public function isAsynchronous ( ) {

        return (bool) $this->getFlag() & self::ASYNCHRONOUS;
    }

    /**
     * Check if the connection is persistent or not.
     *
     * @access  public
     * @return  bool
     */
    public function isPersistent ( ) {

        return (bool) $this->getFlag() & self::PERSISTENT;
    }
}

}
