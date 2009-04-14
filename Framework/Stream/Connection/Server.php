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
 * @subpackage  Hoa_Stream_Connection_Server
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
 * Class Hoa_Stream_Connection_Server.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Stream
 * @subpackage  Hoa_Stream_Connection_Server
 */

class Hoa_Stream_Connection_Server extends Hoa_Stream_Connection {

    /**
     * Tell a stream to bind to the specified target.
     *
     * @const int
     */
    const BIND   = STREAM_SERVER_BIND;

    /**
     * Tell a stream to start listening on the socket.
     *
     * @const int
     */
    const LISTEN = STREAM_SERVER_LISTEN;



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
                                  $flag = -1 ) {

        if($flag == -1)
            $flag = self::BIND | self::LISTEN;

        // make test on socket transport.

        parent::__construct($socket, $timeout, self::BIND & $flag);

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

        $this->_connection = stream_socket_server(
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
     * Accept.
     *
     * @access  public
     * @return  mixed
     */
    public function accept ( ) {

        return stream_socket_accept($this->getConnection());
    }
}
