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
 * @subpackage  Hoa_Stream_Connection
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
 * Class Hoa_Stream_Connection.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Stream
 * @subpackage  Hoa_Stream_Connection
 */

abstract class Hoa_Stream_Connection {

    /**
     * Client.
     *
     * @var Hoa_Stream_Connection resource
     */
    protected $_connection = null;

    /**
     * Socket.
     *
     * @var Hoa_Stream_Socket object
     */
    protected $_socket     = null;

    /**
     * Timeout.
     *
     * @var Hoa_Stream_Connection int
     */
    protected $_timeout    = 30;

    /**
     * Flag.
     *
     * @var Hoa_Stream_Connection int
     */
    protected $_flag       = 0;



    /**
     * Constructor.
     * Configure a socket.
     *
     * @access  public
     * @param   Hoa_Stream_Socket  $socket     Socket.
     * @param   int                $timeout    Timeout.
     * @param   int                $flag       Flag, see the child::* constants.
     * @return  void
     */
    public function __construct ( Hoa_Stream_Socket $socket, $timeout, $flag) {

        $this->setSocket($socket);
        $this->setTimeout($timeout);
        $this->setFlag($flag);

        return;
    }

    /**
     * Connect.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Stream_Connection_Exception
     */
    abstract public function connect ( );

    /**
     * Disable further receptions.
     *
     * @access  public
     * @return  void
     */
    public function quiet ( ) {

        stream_socket_shutdown($this->getConnection(), STREAM_SHUT_RD);

        return;
    }

    /**
     * Disable further transmissions.
     *
     * @access  public
     * @return  void
     */
    public function mute ( ) {

        stream_socket_shutdown($this->getConnection(), STREAM_SHUT_WR);

        return;
    }

    /**
     * Disable further receptions and transmissions, i.e. disconnect.
     *
     * @access  public
     * @return  void
     */
    public function disconnect ( ) {

        stream_socket_shutdown($this->getConnection(), STREAM_SHUT_RDWR);

        return;
    }

    /**
     * Set socket.
     *
     * @access  protected
     * @param   Hoa_Stream_Socket  $socket     Socket.
     * @return  Hoa_Stream_Socket
     */
    protected function setSocket ( Hoa_Stream_Socket $socket ) {

        $old           = $this->_socket;
        $this->_socket = $socket;

        return $old;
    }

    /**
     * Set timeout.
     *
     * @access  protected
     * @param   int        $timeout    Timeout.
     * @return  int
     */
    protected function setTimeout ( $timeout ) {

        $old            = $this->_timeout;
        $this->_timeout = $timeout;

        return $old;
    }

    /**
     * Set flag.
     *
     * @access  protected
     * @param   int        $flag    Flag.
     * @return  int
     */
    protected function setFlag ( $flag ) {

        $old         = $this->_flag;
        $this->_flag = $flag;

        return $old;
    }

    /**
     * Get connection (client or server).
     *
     * @access  protected
     * @return  resource
     */
    protected function getConnection ( ) {

        return $this->_connection;
    }

    /**
     * Get socket.
     *
     * @access  public
     * @return  Hoa_Stream_Socket
     */
    public function getSocket ( ) {

        return $this->_socket;
    }

    /**
     * Get timeout.
     *
     * @access  public
     * @return  int
     */
    public function getTimeout ( ) {

        return $this->_timeout;
    }

    /**
     * Get flag.
     *
     * @access  public
     * @return  int
     */
    public function getFlag ( ) {

        return $this->_flag;
    }
}
