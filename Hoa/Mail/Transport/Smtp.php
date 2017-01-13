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

namespace Hoa\Mail\Transport;

use Hoa\Mail;
use Hoa\Socket;

/**
 * Class \Hoa\Mail\Transport\Smtp.
 *
 * This class allows to send an email by using the SMTP protocol.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Smtp implements ITransport\Out
{
    /**
     * Client.
     *
     * @var \Hoa\Socket\Client
     */
    protected $_client   = null;

    /**
     * Username (if authentication is needed).
     *
     * @var string
     */
    protected $_username = null;

    /**
     * Password (if authentication is needed).
     *
     * @var string
     */
    protected $_password = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Socket\Client  $client      Client.
     * @param   string              $username    Username (if auth is needed).
     * @param   string              $password    Password (if auth is needed).
     */
    public function __construct(
        Socket\Client $client,
        $username = null,
        $password = null
    ) {
        $this->setClient($client);
        $this->setUsername($username);
        $this->setPassword($password);

        return;
    }

    /**
     * Set client.
     *
     * @param   \Hoa\Socket\Client  $client    Client.
     * @return  \Hoa\Socket\Client
     */
    protected function setClient(Socket\Client $client)
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

    /**
     * Set username (if authentication is needed).
     *
     * @param   string  $username    Username.
     * @return  string
     */
    protected function setUsername($username)
    {
        $old             = $this->_username;
        $this->_username = $username;

        return $old;
    }

    /**
     * Get username.
     *
     * @return  string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Set password (if authentication is needed).
     *
     * @param   string  $password    Password.
     * @return  string
     */
    protected function setPassword($password)
    {
        $old             = $this->_password;
        $this->_password = $password;

        return $old;
    }

    /**
     * Get password.
     *
     * @return  string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Check if the client replied correctly. If not, throw an exception
     * containing an error message.
     *
     * @param   int     $code              Expected code.
     * @param   string  $errorMessage      Error message if $code is not valid.
     * @param   string  $timeOutMessage    Time out message.
     * @return  string|array
     * @throws  \Hoa\Mail\Exception\Transport
     */
    protected function ifNot($code, $errorMessage, $timeOutMessage)
    {
        $client = $this->getClient();
        $out    = [];
        $regex  = '#^(?<code>\d{3})(?<separator>[ \-])(?<message>.+)$#';

        do {
            $line = $client->readLine();

            if (true === $client->hasTimedOut()) {
                $client->writeAll('QUIT' . CRLF);
                $client->disconnect();

                throw new Mail\Exception\Transport($timeOutMessage, 0);
            }

            if (0 === preg_match($regex, $line, $matches)) {
                throw new Mail\Exception\Transport(
                    'The server sent an invalid response:' . "\n" .
                    '    ' . $line,
                    1
                );
            }

            $_code = intval($matches['code']);

            if ($code !== $_code) {
                $_message      = trim(substr($line, 4));
                $errorMessage .= ' (code: %d, message: “%s”).';
                $client->writeAll('QUIT' . CRLF);
                $client->disconnect();

                throw new Mail\Exception\Transport(
                    $errorMessage,
                    2,
                    [$_code, $_message]
                );
            }

            $out[] = rtrim($matches['message']);
        } while ('-' === $matches['separator']);

        if (1 === count($out)) {
            return $out[0];
        }

        return $out;
    }

    /**
     * Send a message.
     * Timeouts are defined as the RFC2821 Section 4.5.3.2 suggests.
     *
     * @TODO: Implement the DIGEST-MD5 and GSSAPI auth protocol. Implement SSLv1
     * and v2 support.
     *
     * @param   \Hoa\Mail\Message  $message    Message.
     * @return  bool
     */
    public function send(Mail\Message $message)
    {
        $content = $message->getFormattedContent();
        $headers = $message->getHeaders();

        $client = $this->getClient();
        $client->connect();
        $client->setStreamBlocking(true);
        $client->setStreamTimeout(5 * 60);
        $port = $client->getSocket()->getPort();

        if (465 === $port) {
            $client->enableEncryption(true, $client::ENCRYPTION_TLS);
        }

        $this->ifNot(
            220,
            'Not able to connect to the server',
            'The server timed out while trying to connect.'
        );

        $domain = $message->getDomain($this->getUsername() ?: $headers['from']);
        $client->writeAll('EHLO ' . $domain . CRLF);

        $ehlo = $this->ifNot(
            250,
            'Not able to get supported extensions from the server.',
            'The server timed out while answering to a `EHLO` command.'
        );

        if (465 !== $port && true === in_array('STARTTLS', $ehlo)) {
            $client->writeAll('STARTTLS' . CRLF);

            $this->ifNot(
                220,
                'Cannot start a TLS connection',
                'The server timed out while starting a TLS encryption.'
            );

            if (true !== $client->enableEncryption(true, $client::ENCRYPTION_TLS)) {
                throw new Mail\Exception\Transport(
                    'Cannot enable a TLS connection.',
                    3
                );
            }

            $client->writeAll('EHLO ' . $domain . CRLF);
            $ehlo = $this->ifNot(
                250,
                'Not able to get supported extensions from the server.',
                'The server timed out while answering to a `EHLO` command ' .
                'after having established a TLS connection.'
            );
        }

        $matches = null;

        foreach ($ehlo as $entry) {
            if (0 !== preg_match('#^AUTH (.+)$#', $entry, $matches)) {
                break;
            }
        }

        if (empty($matches)) {
            throw new Mail\Exception\Transport(
                'The server does not support authentication, we cannot ' .
                'authenticate.',
                4
            );
        }

        $auth     = explode(' ', $matches[1]);
        $username = $this->getUsername();
        $password = $this->getPassword();

        if (true === in_array('PLAIN', $auth)) {
            $client->writeAll('AUTH PLAIN' . CRLF);

            $this->ifNot(
                334,
                'Authentication failed (PLAIN)',
                'The server timed out while answering to an `AUTH PLAIN` ' .
                'authentication.'
            );

            $challenge = base64_encode("\0" . $username . "\0" . $password);
            $client->writeAll($challenge . CRLF);

            $this->ifNot(
                235,
                'Wrong username or password',
                'The server timed out while asserting whether the password ' .
                'is correct for an `AUTH PLAIN` authentication.'
            );
        } elseif (true === in_array('LOGIN', $auth)) {
            $client->writeAll('AUTH LOGIN' . CRLF);

            $this->ifNot(
                334,
                'Authentication failed (LOGIN)',
                'The server timed out while answering to an `AUTH LOGIN` ' .
                'authentication.'
            );

            $challenge = base64_encode($username);
            $client->writeAll($challenge . CRLF);

            $this->ifNot(
                334,
                'Wrong username',
                'The server timed out while asserting whether the username ' .
                'is correct for an `AUTH LOGIN` authentication.'
            );

            $challenge = base64_encode($password);
            $client->writeAll($challenge . CRLF);

            $this->ifNot(
                235,
                'Wrong password',
                'The server timed out while asserting whether the password ' .
                'is correct for an `AUTH LOGIN` authentication.'
            );
        } elseif (true === in_array('CRAM-MD5', $auth)) {
            $client->writeAll('AUTH CRAM-MD5' . CRLF);

            $line = $this->ifNot(
                334,
                'Authentication failed (CRAM-MD5)',
                'The server timed out while answering to an `AUTH CRAM-MD5` ' .
                'authentication.'
            );

            $handle    = base64_decode(substr($line, 4));
            $challenge = base64_encode(
                $username . ' ' . static::hmac($password, $handle)
            );
            $client->writeAll($challenge . CRLF);

            $this->ifNot(
                235,
                'Wrong username or password',
                'The server timed out while asserting whether the username ' .
                'and password are correct for an `AUTH CRAM-MD5` authentication.'
            );
        } else {
            throw new Mail\Transport(
                '%s does not support authentication algorithms available ' .
                'on the server (%s).',
                5,
                [__CLASS__, implode(', ', $auth)]
            );
        }

        $client->setStreamTimeout(5 * 60);
        $from = $message->getAddress($headers['from']);
        $client->writeAll('MAIL FROM: <' . $from . '>' . CRLF);

        $this->ifNot(
            250,
            'Sender ' . $from . ' is wrong',
            'The server timed out while asserting whether the sender\'s  ' .
            'email is correct.'
        );

        $client->setStreamTimeout(5 * 60);

        foreach ($message->getRecipients() as $recipient) {
            $client->writeAll('RCPT TO: <' . $recipient . '>' . CRLF);

            $this->ifNot(
                250,
                'Recipient ' . $recipient . ' is wrong',
                'The server timed out while asserting whether the ' .
                'recipient\'s emails are correct.'
            );
        }

        $client->setStreamTimeout(5 * 60);
        $client->writeAll('DATA' . CRLF);

        $this->ifNot(
            354,
            'Cannot send data',
            'The server timed out while answering to a `DATA` command.'
        );

        $client->setStreamTimeout(10 * 60);
        $client->writeAll(
            $content . CRLF .
            '.' . CRLF
        );

        $this->ifNot(
            250,
            'Something went wrong with data',
            'The server timed out while asserting all the data have been ' .
            'received correctly.'
        );

        $client->writeAll('QUIT' . CRLF);

        $this->ifNot(
            221,
            'Cannot quit properly',
            'The server timed out while trying to quit properly.'
        );

        $client->disconnect();

        return true;
    }

    /**
     * H-MAC.
     * Please, see RFC2104, section 2 Definition of HMAC.
     *
     * @param   string  $key     Key.
     * @param   string  $data    Data.
     * @return  string
     */
    public static function hmac($key, $data)
    {
        if (true === function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }

        if (64 < strlen($key)) {
            $key = pack('H32', md5($key));
        }

        if (64 > strlen($key)) {
            $key = str_pad($key, 64, chr(0x0));
        }

        $key     = substr($key, 0, 64);
        $oKeyPad = $key ^ str_repeat(chr(0x5c), 64);
        $iKeyPad = $key ^ str_repeat(chr(0x36), 64);

        return md5($oKeyPad . pack('H32', md5($iKeyPad . $data)));
    }
}
