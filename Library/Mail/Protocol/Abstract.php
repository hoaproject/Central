<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
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
