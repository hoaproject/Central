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
 * @package     Hoa_Socket
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Socket_Exception
 */
import('Socket.Exception');

/**
 * Class Hoa_Socket.
 *
 * Send requests, get responses etc. : manage sockets.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Socket
 */

class Hoa_Socket {

    /**
     * Current open socket.
     *
     * @var Hoa_Socket resource
     */
    protected $socket = null;

    /**
     * Hostame or IP of remote server.
     *
     * @var Hoa_Socket string
     */
    protected $host = '127.0.0.1';

    /**
     * Port number.
     *
     * @var Hoa_Socket string
     */
    protected $port = null;

    /**
     * Connection time out.
     *
     * @var Hoa_Socket int
     */
    protected $timeout = 30;

    /**
     * Array of last request sent to server.
     *
     * @var Hoa_Socket array
     */
    protected $request = array();

    /**
     * Array of last responses received from server.
     *
     * @var Hoa_Socket array
     */
    protected $response = array();

    /**
     * Except template.
     *
     * @var Hoa_Socket string
     */
    protected $except = '%d%s';



    /**
     * __construct
     * Set host and port.
     *
     * @access  public
     * @param   host     string    Hostname or IP of remote server.
     * @param   port     int       The port to connection.
     * @param   timeout  int       Stream time out.
     * @return  void
     */
    public function __construct ( $host = '127.0.0.1', $port = null,
                                  $timeout = 30 ) {

        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
    }


    /**
     * __destruct
     * Close opened connection.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        return $this->_disconnect();
    }

    /**
     * Create a connection to the remote server.
     */
    //abstract protected function connect ( );

    /**
     * Destroy an opened connection.
     */
    //abstract protected function disconnect ( );

    /**
     * _connect
     * Connect to a server.
     *
     * @access  public
     * @param   remote  string    Protocol + Hostname + Port etc.
     * @return  bool
     * @throw   Hoa_Socket_Exception
     */
    public function _connect ( ) {

        $remote = $this->host . (!empty($this->port) ? ':' . $this->port : '');

        $errno  = 0;
        $errstr = 'Could not open socket';

        $this->socket = stream_socket_client($remote,
                                             $errno,
                                             $errstr,
                                             $this->timeout);

        if($this->socket === false)
            throw new Hoa_Socket_Exception($errstr, $errno);

        $out = false;
        if($this->timeout > 0)
            if(false === $out = @socket_set_timeout($this->socket, $this->timeout))
                throw new Hoa_Socket_Exception('Could not set time out limit (%d)',
                                               0, $this->timeout);

        return $out;
    }


    /**
     * _disconnect
     * Close an opened connection.
     *
     * @access  public
     * @return  bool
     */
    public function _disconnect ( ) {

        return @fclose($this->socket);
    }


    /**
     * getRequest
     * Get last sent request.
     *
     * @access  public
     * @return  string
     */
    public function getRequest ( ) {

        end($this->request);
        return current($this->request);
    }


    /**
     * getResponse
     * Get last received response.
     *
     * @access  public
     * @return  string
     */
    public function getResponse ( ) {

        end($this->response);
        return current($this->response);
    }


    /**
     * send
     * Send a request.
     *
     * @access  public
     * @param   request  string    Request to send.
     * @return  string
     * @throw   Hoa_Socket_Exception
     */
    public function send ( $request = '' ) {

        if(empty($request))
            throw new Hoa_Socket_Exception('Request could not be empty.', 2);

        if(!is_resource($this->socket))
            throw new Hoa_Socket_Exception('No connection has been established to %s',
                                           3, $this->host);

        $this->request[] = $request;

        if(!fputs($this->socket, $request . CRLF))
            throw new Hoa_Socket_Exception('Could not send request %s to %s', 4,
                                           array($request, $this->host));

        return $this->receive();
    }


    /**
     * receive
     * Get server response.
     *
     * @access  private
     * @return  string
     * @throw   Hoa_Socket_Exception
     */
    private function receive ( ) {

        $read   = array($this->socket);
        $write  = array();
        $except = array();

        $buffer = '';
        while((stream_select($read, $write, $except, 1, 1000) > 0)
              && !feof($this->socket))
            $buffer .= fgets($this->socket, 1024);

        if($this->isTimedOut($this->socket))
            throw new Hoa_Socket_Exception('%s has timed out.', 5, $this->host);

        if($buffer === false)
            throw new Hoa_Socket_Exception('Could not read from %s', 6, $this->host);

        $buffer           = trim($buffer);
        $this->response[] = $buffer;

        return $buffer;
    }


    /**
     * except
     * Parser server response for successful codes.
     *
     * @access  public
     * @param   codeA   int    Low or unique code.
     * @param   codeB   int    High code.
     * @return  bool
     * @throw   Hoa_Socket_Exception
     */
    public function except ( $codeA, $codeB = null ) {

        if(!empty($codeB))
            $code = range($codeA, $codeB);
        else
            $code = (array)$codeA;

        $response = $this->getResponse();
        $command  = '';
        $message  = '';

        sscanf($response, $this->except, $command, $message);

        if($command === null || !in_array($command, $code))
            throw new Hoa_Socket_Exception($response, 7);
        else
            return true;
    }


    /**
     * isTimedOut
     * Check if the stream timed out while waiting for data.
     *
     * @access  public
     * @return  bool
     */
    public function isTimedOut ( $socket ) {

        $meta = stream_get_meta_data($socket);

        return $meta['timed_out'];
    }
}
