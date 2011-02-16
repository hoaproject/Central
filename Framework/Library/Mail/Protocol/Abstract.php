<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Protocol_Abstract
 *
 */

/**
 * Hoa_Socket_Connection_Client
 */
import('Socket.Connection.Client');

/**
 * Hoa_Socket_Internet_DomainName
 */
import('Socket.Internet.DomainName');

/**
 * Hoa_Socket_Internet_Ipv4
 */
import('Socket.Internet.Ipv4');

/**
 * Hoa_Socket_Internet_Ipv6
 */
import('Socket.Internet.Ipv6');

/**
 * Class Hoa_Mail_Protocol_Abstract.
 *
 * Abstract layer for protocol : manage connection for SMTP etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Protocol_Abstract
 */

class Hoa_Mail_Protocol_Abstract {

    /**
     * Socket.
     *
     * @var Hoa_Socket_Connection_Client object
     */
    protected $_sockt = null;



    /**
     * getConnection
     * Try to open a connection to the remote server.
     *
     * @access  public
     * @param   host     Hoa_Socket_Internet    Remote server, represented by
     *                                          the Hoa_Socket_Internet_DomainName,
     *                                          Hoa_Socket_Internet_Ipv4 or
     *                                          Hoa_Socket_Internet_Ipv4 objects.
     * @return  void
     * @throw   Hoa_Socket_Exception
     */
    public function connect ( Hoa_Socket_Internet $host ) {

        $this->_socket = new Hoa_Socket_Connection_Client($host, $port, $timeout);
        $this->_socket->connect();

        return;
    }

    /**
     * disconnect
     * Destroy an opened connection.
     *
     * @access  public
     * @return  void
     */
    public function disconnect ( ) {

        $this->_socket->disconnect();

        return;
    }
}
