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

namespace Hoa\XmlRpc;

use Hoa\Socket;

/**
 * Class \Hoa\XmlRpc\Client.
 *
 * A XML-RPC client.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Client
{
    /**
     * Client.
     *
     * @var \Hoa\Socket\Client
     */
    protected $_client = null;

    /**
     * Script to call (e.g. xmlrpc.cgi).
     *
     * @var string
     */
    protected $_script = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Socket\Client  $client    Client.
     * @param   string              $script    Script.
     * @throws  \Hoa\Socket\Exception
     */
    public function __construct(Socket\Client $client, $script)
    {
        $this->_client = $client;
        $this->_script = $script;
        $client->connect();

        return;
    }

    /**
     * Pack message to HTTP header format.
     *
     * @param   string  $message    Message.
     * @return  string
     */
    public function getHeader($message)
    {
        return
            'POST /' . $this->getScript() . ' HTTP/1.1' . "\r\n" .
            'User-Agent: Hoa' . "\r\n" .
            'Host: ' . $this->_client->getSocket()->getAddress() . "\r\n" .
            'Content-Type: text/xml' . "\r\n" .
            'Content-Length: ' . strlen($message) . "\r\n" .
            "\r\n" .
            $message;
    }

    /**
     * Send a request and get a response.
     *
     * @param   \Hoa\XmlRpc\Message\Request  $message    Message.
     * @return  \Hoa\XmlRpc\Message\Response
     * @throws  \Hoa\XmlRpc\Exception\Fault
     */
    public function send(Message\Request $message)
    {
        $request  = $message->__toString();
        $this->_client->writeAll($this->getHeader($request));
        $response = $this->_client->readAll();

        if (false === $pos = strpos($response, "\r\n\r\n")) {
            throw new Exception(
                'Oops, an unknown error occured. Headers seem to be corrupted.',
                0
            );
        }

        $response = substr($response, $pos + 4);

        if (0 !== preg_match('#<methodResponse>(\s|\n)*<fault>#i', $response)) {
            preg_match(
                '#<(i4|int)>(?:\s|\n)*(\d+)(?:\s|\n)*</\1>#i',
                $response,
                $faultCodeMatches
            );
            preg_match(
                '#<string>(?:\s|\n)*(.*)(?:\s|\n)*</string>#i',
                $response,
                $faultStringMatches
            );

            $faultCode   = -1;
            $faultString = 'An ununderstable fault from the server occured.';

            if (isset($faultCodeMatches[2])) {
                $faultCode   = $faultCodeMatches[2];
            }

            if (isset($faultStringMatches[1])) {
                $faultString = $faultStringMatches[1];
            }

            throw new Exception\Fault($faultString, $faultCode, $request);
        }

        return new Message\Response($response);
    }

    /**
     * Get script.
     *
     * @return  string
     */
    public function getScript()
    {
        return $this->_script;
    }
}
