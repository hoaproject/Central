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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Stream
 * @subpackage  Hoa_Stream_Connection_Client
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Stream_Connection_Exception
 */
import('Stream.Connection.Exception');

/**
 * Hoa_Stream_Connection
 */
import('Stream.Connection.~');

/**
 * Class Hoa_Stream_Connection_Client.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Stream
 * @subpackage  Hoa_Stream_Connection_Client
 */

class Hoa_Stream_Connection_Client extends Hoa_Stream_Connection {

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
     * @param   Hoa_Stream_Socket  $socket     Socket.
     * @param   int                $timeout    Timeout.
     * @param   int                $flag       Flag, see the self::* constants.
     * @return  void
     */
    public function __construct ( Hoa_Stream_Socket $socket, $timeout = 30,
                                  $flag = self::CONNECT ) {

        parent::__construct($socket, $timeout, self::CONNECT & $flag);

        return;
    }

    /**
     * Connect.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Stream_Connection_Exception
     */
    public function connect ( ) {

        $this->_connection = stream_socket_client(
            $this->getSocket()->__toString(),
            $errno,
            $errstr,
            $this->getTimeout(),
            $this->getFlag()
        );

        if(false === $this->_connection)
            if($errno == 0)
                throw new Hoa_Stream_Connection_Exception(
                    'Client cannot join %s.', 0,
                    $this->getSocket()->__toString());
            else
                throw new Hoa_Stream_Connection_Exception(
                    'Client returns an error (number %d): %s.',
                    1, array($errno, $errstr));

        return;
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
