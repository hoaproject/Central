<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
