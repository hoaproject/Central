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
 */

namespace {

from('Hoa')

/**
 * \Hoa\FastCgi\Exception
 */
-> import('FastCgi.Exception')

/**
 * \Hoa\FastCgi\Connection
 */
-> import('FastCgi.Connection');

}

namespace Hoa\FastCgi {

/**
 * Class \Hoa\FastCgi\Client.
 *
 * A FastCGI client.
 * Inspired by PHP SAPI code: php://sapi/cgi/fastcgi.*.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Client extends Connection {

    /**
     * Client socket connection.
     *
     * @var \Hoa\Socket\Connection\Client object
     */
    protected $_client  = null;

    /**
     * Response: content.
     *
     * @var \Hoa\FastCgi\Client string
     */
    protected $_content = null;

    /**
     * Response: headers.
     *
     * @var \Hoa\FastCgi\Client array
     */
    protected $_headers = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Socket\Connection\Client  $client    Client connection.
     * @return  void
     */
    public function __construct ( \Hoa\Socket\Connection\Client $client ) {

        $this->_client = $client;

        return;
    }

    /**
     * Send data on a FastCGI.
     *
     * @access  public
     * @param   array   $headers    Headers.
     * @param   string  $content    Content (e.g. key=value for POST).
     * @return  string
     */
    public function send ( Array $headers, $content = null ) {

        $this->_client->connect();

        $parameters = null;
        $response   = null;
        $request    = $this->pack(
            1,
            chr(0) . chr(1) . chr((int) $this->_client->isPersistent()) .
            chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
        );

        $parameters .= $this->packPairs($headers);

        if(null !== $parameters)
            $request .= $this->pack(4, $parameters);

        $request .= $this->pack(4, '');

        if(null !== $content)
            $request .= $this->pack(5, $content);

        $request .= $this->pack(5, '');
        $handle   = null;
        $this->_client->writeAll($request);

        do {

            if(false === $handle = $this->readPack())
                throw new Exception('Bad request foobar.', 0);

            if(   6 === $handle[self::HEADER_TYPE]
               || 7 === $handle[self::HEADER_TYPE])
                $response .= $handle[self::PACK_CONTENT];

        } while(3 !== $handle[self::HEADER_TYPE]);

        $this->_client->disconnect();

        $pos     = strpos($response, "\r\n\r\n");
        $headers = substr($response, 0, $pos);

        foreach(explode("\r\n", $headers) as $header) {

            $semicolon = strpos($header, ':');
            $this->_headers[strtolower(trim(substr($header, 0, $semicolon)))]
                = trim(substr($header, $semicolon + 1));
        }

        return $this->_content = substr($response, $pos + 4);
    }

    /**
     * Get response content.
     *
     * @access  public
     * @return  string
     */
    public function getResponseContent ( ) {

        return $this->_content;
    }

    /**
     * Get response headers.
     *
     * @access  public
     * @return  array
     */
    public function getResponseHeaders ( ) {

        return $this->_headers;
    }

    /**
     * Read data.
     *
     * @access  protected
     * @param   int     $length    Length of data to read.
     * @return  string
     */
    protected function read ( $length ) {

        return $this->_client->read($length);
    }
}

}
