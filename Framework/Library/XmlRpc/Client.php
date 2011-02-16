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
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Client
 *
 */

/**
 * Hoa_Uri
 */
import('Uri.~');

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
 * Hoa_XmlRpc
 */
import('XmlRpc.~');

/**
 * Hoa_XmlRpc_Value
 */
import('XmlRpc.Value');

/**
 * Hoa_XmlRpc_Message
 */
import('XmlRpc.Message');

/**
 * Class Hoa_XmlRpc_Client.
 *
 * Prepare and send headers and payload to RPC server.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Client
 */

class Hoa_XmlRpc_Client extends Hoa_XmlRpc {

    /**
     * Uniform Resource Identier object.
     *
     * @var Hoa_Uri object
     */
    protected $uri = null;

    /**
     * Connection to RPC server.
     *
     * @var Hoa_Socket_Connection_Client object
     */
    protected $connection = null;

    /**
     * URI components.
     *
     * @var Hoa_XmlRpc string
     */
    protected $scheme   = null;
    protected $domain   = null;
    protected $abs_path = null;
    protected $port     = 80;
    protected $username = null;
    protected $password = null;

    /**
     * Headers to send.
     *
     * @var Hoa_XmlRpc string
     */
    protected $header = '';

    /**
     * Connection time out.
     *
     * @var Hoa_XmlRpc int;
     */
    protected $timeout = 30;



    /**
     * __construct
     * Assign URI variables.
     *
     * @access  public
     * @param   uri     string    Address to Xml Rpc server.
     * @return  void
     * @throw   Hoa_XmlRpc_Exception
     */
    public function __construct ( $uri = null ) {

        $this->uri = Hoa_Uri::factory($uri);

        if(!$this->uri->isValid())
            throw new Hoa_XmlRpc_Exception('URI %s is not valid.', 0, $uri);

        $this->scheme   = $this->uri->getScheme($uri);
        $this->domain   = $this->uri->getAuthority();
        $this->abs_path = $this->uri->getPath();
        $this->port     = $this->uri->getPort();
        $this->username = $this->uri->getUsername();
        $this->password = $this->uri->getPassword();

        if(empty($this->port))
            $this->port = 80;

        if($this->scheme == 'http')
            $this->scheme = '';
    }

    /**
     * send
     * This method is an alias of sendPayload,
     * sets the timeout and run sendPayload.
     *
     * @access  public
     * @param   message  object    Hoa_XmlRpc_Message.
     * @param   timeout  int       Time out.
     * @return  string
     * @throw   Hoa_XmlRpc_Exception
     */
    public function send ( Hoa_XmlRpc_Message $message, $timeout = 30 ) {

        if($timeout < 0)
            throw new Hoa_XmlRpc_Exception($this->error[0], 0);

        $this->timeout = $timeout;

        return $this->sendPayload($message);
    }

    /**
     * sendPayload
     * This method opens a socket to the RPC server,
     * sets the time out of socket to $this->timeout,
     * sends the headers, and the payload,
     * recovers the response.
     *
     * @access  protected
     * @param   message    object    Hoa_XmlRpc_Message.
     * @return  string
     * @throw   Hoa_XmlRpc_Exception
     */
    protected function sendPayload ( Hoa_XmlRpc_Message $message ) {

        $remote = '';
        if(!empty($this->scheme))
            $remote .= $this->scheme . '://';
        $remote .= $this->domain . ':' . $this->port . $this->abs_path;

        throw new Hoa_Core_Exception(
            'This package is depreciated!!', 0);

        $this->connection = new Hoa_Socket($remote, null, $this->timeout);

        $this->connection->_connect();

        $payload = $message->getPayload();

        if(empty($payload))
            $payload = $message->createPayload();

        $this->createHeader(strlen($payload));

        $response = $this->connection->send($this->header . CRLF . CRLF . $payload);

        $this->connection->_disconnect();

        return $response;
    }

    /**
     * createHeader
     * Set the headers, and encode logs (username and password) if it's necessary.
     *
     * @access  public
     * @param   payloadLn  string    Payload length.
     * @return  void
     * @throw   Hoa_XmlRpc_Exception
     */
    public function createHeader ( $payloadLn ) {

        if($payloadLn == 0)
            throw new Hoa_XmlRpc_Exception($this->error[4], 4);

        $this->header  = 'POST /' . $this->abs_path . ' HTTP/1.1' . CRLF .
                         'User-Agent: HOA XmlRpc' . CRLF .
                         'Host: ' . $this->domain . CRLF;

        if(!empty($this->username) && !empty($this->password))
            $this->header .= 'Authorization: Basic ' .
                             base64_encode($this->username . ' : ' . $this->password) .
                             CRLF;

        $this->header .= 'Content-Type: text/xml' . CRLF .
                         'Content-Length: ' . $payloadLn;
    }
}
