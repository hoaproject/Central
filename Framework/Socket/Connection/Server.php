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
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Connection_Server
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Socket_Connection_Exception
 */
import('Socket.Connection.Exception');

/**
 * Hoa_Socket_Connection
 */
import('Socket.Connection');

/**
 * Hoa_Socket_Interface
 */
import('Socket.Interface');

/**
 * Class Hoa_Socket_Connection_Server.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Connection_Server
 */

class Hoa_Socket_Connection_Server extends Hoa_Socket_Connection {

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
     * @param   Hoa_Socket_Interface  $socket     Socket.
     * @param   int                   $timeout    Timeout.
     * @param   int                   $flag       Flag, see the self::* constants.
     * @return  void
     * @throw   Hoa_Socket_Connection_Exception
     */
    public function __construct ( Hoa_Socket_Interface $socket, $timeout = 30,
                                  $flag = -1 ) {

        if($flag == -1)
            $flag = self::BIND | self::LISTEN;

        switch($socket->getTransport()) {

            case 'tcp':
                $flag &= self::LISTEN;
              break;

            case 'udp':
                if($flag & self::LISTEN)
                    throw new Hoa_Socket_Connection_Exception(
                        'Cannot use the flag Hoa_Socket_Connection_Server::LISTEN ' .
                        'for connect-less transports (such as UDP).', 0);

                $flag &= self::BIND;
              break;
        }

        parent::__construct($socket, $timeout, self::BIND & $flag);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string     $streamName    Stream name (e.g. path or URL).
     * @return  resource
     * @throw   Hoa_Socket_Connection_Exception
     */
    protected function &open ( $streamName ) {

        $connection = @stream_socket_server(
            $streamName,
            $errno,
            $errstr,
            $this->getTimeout(),
            $this->getFlag()
        );

        if(false === $connection)
            if($errno == 0)
                throw new Hoa_Socket_Connection_Exception(
                    'Server cannot join %s.', 0, $streamName);
            else
                throw new Hoa_Socket_Connection_Exception(
                    'Server returns an error (number %d): %s.',
                    1, array($errno, $errstr));

        return $connection;
    }

    /**
     * Close the current stream.
     *
     * @access  protected
     * @return  bool
     */
    public function close ( ) {

        return fclose($this->getStream());
    }

    /**
     * Accept.
     *
     * @access  public
     * @return  mixed
     */
    public function accept ( ) {

        return stream_socket_accept($this->getStream());
    }
}
