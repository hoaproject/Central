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
-> import('FastCgi.Exception.~')

/**
 * \Hoa\FastCgi\Exception\CannotMultiplex
 */
-> import('FastCgi.Exception.CannotMultiplex')

/**
 * \Hoa\FastCgi\Exception\Overloaded
 */
-> import('FastCgi.Exception.Overloaded')

/**
 * \Hoa\FastCgi\Exception\UnknownRole
 */
-> import('FastCgi.Exception.UnknownRole')

/**
 * \Hoa\FastCgi\Connection
 */
-> import('FastCgi.Connection');

}

namespace Hoa\FastCgi {

/**
 * Class \Hoa\FastCgi\Responder.
 *
 * A FastCGI client with a responder role.
 * Inspired by PHP SAPI code: php://sapi/cgi/fastcgi.*.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Responder extends Connection {

    /**
     * Request: begin.
     *
     * @const int
     */
    const REQUEST_BEGIN           = 1;

    /**
     * Request: abord.
     *
     * @const int
     */
    const REQUEST_ABORD           = 2;

    /**
     * Request: end.
     *
     * @const int
     */
    const REQUEST_END             = 3;

    /**
     * Request: parameters.
     *
     * @const int
     */
    const REQUEST_PARAMETERS      = 4;

    /**
     * Stream: stdin.
     *
     * @const int
     */
    const STREAM_STDIN            = 5;

    /**
     * Stream: stdout.
     *
     * @const int
     */
    const STREAM_STDOUT           = 6;

    /**
     * Stream: stderr.
     *
     * @const int
     */
    const STREAM_STDERR           = 7;

    /**
     * Request status: normal en of request.
     *
     * @const int
     */
    const STATUS_COMPLETE         = 0;

    /**
     * Request status: rejecting a new request; this happens when a Web server
     * sends concurrent requests over one connection to an application that is
     * designed to process one request at a time per connection.
     *
     * @const int
     */
    const STATUS_CANNOT_MULTIPLEX = 1;

    /**
     * Request status: rejecting a new request; this happens when the
     * application runs out of some resource, e.g. database connections.
     *
     * @const int
     */
    const STATUS_OVERLOADED       = 2;

    /**
     * Request status: rejecting a new request; this happens when the Web server
     * has specificied a role that is unknown to the application.
     *
     * @const int
     */
    const STATUS_UNKNOWN_ROLE     = 3;

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

        $this->setClient($client);

        return;
    }

    /**
     * Send data on a FastCGI.
     *
     * @access  public
     * @param   array   $headers    Headers.
     * @param   string  $content    Content (e.g. key=value for POST).
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    public function send ( Array $headers, $content = null ) {

        $client = $this->getClient();
        $client->connect();

        $parameters = null;
        $response   = null;
        $request    = $this->pack(
            self::REQUEST_BEGIN,
            //   ___________________
            //  /                   \
            //  | “I'm a responder” |
            //  \_______  __________/
            //          \/
            chr(0) . chr(1) . chr((int) $client->isPersistent()) .
            chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
        );

        $parameters .= $this->packPairs($headers);

        if(null !== $parameters)
            $request .= $this->pack(self::REQUEST_PARAMETERS, $parameters);

        $request .= $this->pack(self::REQUEST_PARAMETERS, '');

        if(null !== $content)
            $request .= $this->pack(self::STREAM_STDIN, $content);

        $request .= $this->pack(self::STREAM_STDIN, '');
        $client->writeAll($request);
        $handle   = null;

        do {

            if(false === $handle = $this->readPack())
                throw new Exception('Bad request foobar.', 0);

            if(   self::STREAM_STDOUT === $handle[parent::HEADER_TYPE]
               || self::STREAM_STDERR === $handle[parent::HEADER_TYPE])
                $response .= $handle[parent::HEADER_CONTENT];

        } while(self::REQUEST_END !== $handle[parent::HEADER_TYPE]);

        $client->disconnect();

        switch(ord($handle[parent::HEADER_CONTENT][4])) {

            case self::STATUS_CANNOT_MULTIPLEX:
                throw new Exception\CannotMultiplex(
                    'Application %s that you are trying to reach does not ' .
                    'support multiplexing.',
                    0, $this->getClient()->getSocket()->__toString());
              break;

            case self::STATUS_OVERLOADED:
                throw new Exception\Overloaded(
                    'Application %s is too busy and rejects your request.',
                    1, $this->getClient()->getSocket()->__toString());
              break;

            case self::STATUS_UNKNOWN_ROLE:
                throw new Exception\UnknownRole(
                    'Server for the application %s returns an unknown role.',
                    2, $this->getClient()->getSocket()->__toString());
              break;
        }

        /**
         * default: // self::STATUS_COMPLETE
         *   break;
         */

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

        return $this->getClient()->read($length);
    }

    /**
     * Set client.
     *
     * @access  public
     * @param   \Hoa\Socket\Connection\Client  $client    Client.
     * @return  \Hoa\Socket\Connection\Client
     */
    public function setClient ( \Hoa\Socket\Connection\Client $client ) {

        $old           = $this->_client;
        $this->_client = $client;

        return $old;
    }

    /**
     * Get client.
     *
     * @access  public
     * @return  \Hoa\Socket\Connection\Client
     */
    public function getClient ( ) {

        return $this->_client;
    }
}

}
