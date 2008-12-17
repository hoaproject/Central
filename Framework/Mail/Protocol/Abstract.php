<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Protocol_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Socket
 */
import('Socket.~');

/**
 * Class Hoa_Mail_Protocol_Abstract.
 *
 * Abstract layer for protocol : manage connection for SMTP etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Protocol_Abstract
 */

class Hoa_Mail_Protocol_Abstract {

    /**
     * Socket resource.
     *
     * @var Hoa_Socket resource
     */
    protected $stream = null;



    /**
     * getConnection
     * Try to open a connection to the remote server.
     *
     * @access  public
     * @param   host     string    Hostname or IP of remote server.
     * @param   port     int       The port to connection.
     * @param   timeout  int       Stream time out.
     * @return  void
     * @throw   Hoa_Socket_Exception
     */
    public function connect ( $host = '127.0.0.1', $port = null, $timeout = 30 ) {

        $this->stream = new Hoa_Socket($host, $port, $timeout);
        $this->stream->_connect();
    }

    /**
     * disconnect
     * Destroy an opened connection.
     *
     * @access  public
     * @return  bool
     */
    public function disconnect ( ) {

        $this->stream->_disconnect();
    }
}
