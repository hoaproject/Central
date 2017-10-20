<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Fastcgi;

use Hoa\Socket;

/**
 * Class \Hoa\Fastcgi\Responder.
 *
 * A FastCGI client with a responder role.
 * Inspired by PHP SAPI code: php://sapi/cgi/fastcgi.*.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Responder extends Connection
{
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
     * @var \Hoa\Socket\Client
     */
    protected $_client          = null;

    /**
     * Response's output.
     *
     * @var string
     */
    protected $_responseOutput  = null;

    /**
     * Response's error.
     *
     * @var string
     */
    protected $_responseError   = null;

    /**
     * Response's headers.
     *
     * @var array
     */
    protected $_responseHeaders = [];



    /**
     * Constructor.
     *
     * @param   \Hoa\Socket\Client  $client    Client connection.
     */
    public function __construct(Socket\Client $client)
    {
        $this->setClient($client);

        return;
    }

    /**
     * Send data on a FastCGI.
     *
     * @param   array   $headers    Headers.
     * @param   string  $content    Content (e.g. key=value for POST).
     * @return  string
     * @throws  \Hoa\Socket\Exception
     * @throws  \Hoa\Fastcgi\Exception
     * @throws  \Hoa\Fastcgi\Exception\CannotMultiplex
     * @throws  \Hoa\Fastcgi\Exception\Overloaded
     * @throws  \Hoa\Fastcgi\Exception\UnknownRole
     * @throws  \Hoa\Fastcgi\Exception\UnknownStatus
     */
    public function send(array $headers, $content = null)
    {
        $this->_responseOutput  = null;
        $this->_responseError   = null;
        $this->_responseHeaders = [];

        $client = $this->getClient();
        $client->connect();
        $client->setStreamBlocking(true);

        $parameters     = null;
        $responseOutput = null;

        $request = $this->pack(
            self::REQUEST_BEGIN,
            // ┌───────────────────┐
            // │ “I'm a responder” │
            // └─────────⌵─────────┘
            chr(0) . chr(1) . chr((int) $client->isPersistent()) .
            chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
        );

        $parameters .= $this->packPairs($headers);

        if (null !== $parameters) {
            $request .= $this->pack(self::REQUEST_PARAMETERS, $parameters);
        }

        $request .= $this->pack(self::REQUEST_PARAMETERS, '');

        if (null !== $content) {
            // The maximum length of each record is 65535 bytes.
            // Pack multiple records if the length is larger than the
            // 65535 bytes.
            foreach (str_split($content, 65535) as $chunk) {
                $request .= $this->pack(self::STREAM_STDIN, $chunk);
            }
        }

        $request .= $this->pack(self::STREAM_STDIN, '');
        $client->writeAll($request);
        $handle = null;

        do {
            if (false === $handle = $this->readPack()) {
                throw new Exception(
                    'Bad request (not a well-formed FastCGI request).',
                    0
                );
            }

            if (self::STREAM_STDOUT === $handle[parent::HEADER_TYPE]) {
                $responseOutput .= $handle[parent::HEADER_CONTENT];
            } elseif (self::STREAM_STDERR === $handle[parent::HEADER_TYPE]) {
                $this->_responseError .= $handle[parent::HEADER_CONTENT];
            }
        } while (self::REQUEST_END !== $handle[parent::HEADER_TYPE]);

        $client->disconnect();

        $status = ord($handle[parent::HEADER_CONTENT][4]);

        switch ($status) {
            case self::STATUS_CANNOT_MULTIPLEX:
                throw new Exception\CannotMultiplex(
                    'Application %s that you are trying to reach does not ' .
                    'support multiplexing.',
                    1,
                    $this->getClient()->getSocket()->__toString()
                );

                break;

            case self::STATUS_OVERLOADED:
                throw new Exception\Overloaded(
                    'Application %s is too busy and rejects your request.',
                    2,
                    $this->getClient()->getSocket()->__toString()
                );

                break;

            case self::STATUS_UNKNOWN_ROLE:
                throw new Exception\UnknownRole(
                    'Server for the application %s returns an unknown role.',
                    3,
                    $this->getClient()->getSocket()->__toString()
                );

                break;

            case self::STATUS_COMPLETE:
                break;

            default:
                throw new Exception\UnknownStatus(
                    'Server for the application %s returns an unknown status: %d.',
                    4,
                    [
                        $this->getClient()->getSocket()->__toString(),
                        $status
                    ]
                );
        }

        $pos     = strpos($responseOutput, "\r\n\r\n");
        $headers = substr($responseOutput, 0, $pos);

        foreach (explode("\r\n", $headers) as $header) {
            $semicolon = strpos($header, ':');
            $this->_responseHeaders[strtolower(trim(substr($header, 0, $semicolon)))]
                = trim(substr($header, $semicolon + 1));
        }

        return $this->_responseOutput = substr($responseOutput, $pos + 4);
    }

    /**
     * Get response content.
     *
     * @return  ?string
     */
    public function getResponseContent()
    {
        return $this->_responseOutput;
    }

    /**
     * Get response error if any.
     *
     * @return  ?string
     */
    public function getResponseError()
    {
        return $this->_responseError;
    }

    /**
     * Get response headers.
     *
     * @return  array
     */
    public function getResponseHeaders()
    {
        return $this->_responseHeaders;
    }

    /**
     * Read data.
     *
     * @param   int     $length    Length of data to read.
     * @return  string
     */
    protected function read($length)
    {
        return $this->getClient()->read($length);
    }

    /**
     * Set client.
     *
     * @param   \Hoa\Socket\Client  $client    Client.
     * @return  \Hoa\Socket\Client
     */
    public function setClient(Socket\Client $client)
    {
        $old           = $this->_client;
        $this->_client = $client;

        return $old;
    }

    /**
     * Get client.
     *
     * @return  \Hoa\Socket\Client
     */
    public function getClient()
    {
        return $this->_client;
    }
}
