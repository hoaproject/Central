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

namespace {

from('Hoa')

/**
 * \Hoa\Notification\Exception
 */
-> import('Notification.Exception')

/**
 * \Hoa\Notification
 */
-> import('Notification.~')

/**
 * \Hoa\Socket\Client
 */
-> import('Socket.Client');

}

namespace Hoa\Notification {

/**
 * Class \Hoa\Notification\Growl.
 *
 * Growl notification support.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Growl implements Notification
{
    /**
     * Client.
     *
     * @var \Hoa\Socket\Client
     */
    protected $_client          = null;

    /**
     * Application name.
     *
     * @var string
     */
    protected $_applicationName = null;

    /**
     * Application's password.
     *
     * @var string
     */
    protected $_password        = null;

    /**
     * Notification's channel.
     *
     * @var string
     */
    protected $_channel         = '_default';

    /**
     * All notification's channels: name => boolean; whether handshake has
     * already been done.
     *
     * @var array
     */
    protected $_channels        = ['_default' => false];



    /**
     * Construct a new notifier.
     *
     * @param   string              $applicationName    Application's name.
     * @param   \Hoa\Socket\Client  $client             Client.
     * @param   string              $password           Application's
     *                                                  password.
     * @throws  \Hoa\Socket\Exception
     */
    public function __construct($applicationName           = 'Hoa',
                                  \Hoa\Socket\Client $client = null,
                                  $password                  = '')
    {
        if (null === $client) {
            $client = new \Hoa\Socket\Client('udp://localhost:9887');
        }

        $this->_client = $client;
        $this->_client->connect();
        $this->setApplicationName($applicationName);
        $this->setPassword($password);

        return;
    }

    /**
     * Select a specific channel.
     *
     * @param   string  $channel    Channel's name.
     * @return  \Hoa\Notification\Growl
     */
    public function __get($channel)
    {
        if (false === $this->channelExists($channel)) {
            $this->_channels[$channel] = false;
        }

        $this->_channel = $channel;

        if (false === $this->_channels[$channel]) {
            $this->handshake();
        }

        return $this;
    }

    /**
     * Do handshake.
     *
     * @return  bool
     */
    protected function handshake()
    {
        $channel = $this->getChannel();
        $data    = pack(
                       'c2nc2',
                       1,
                       0,
                       strlen($this->getApplicationName()),
                       1,
                       1
                   ) .
                   $this->getApplicationName() .
                   pack('n', strlen($channel)) . $channel .
                   pack('c', 0);

        $this->_client->writeAll(
            $data .
            pack('H32', md5($data . $this->getPassword()))
        );

        $this->_channels[$channel] = true;

        return true;
    }

    /**
     * Send a notification.
     *
     * @param   string  $title      Title.
     * @param   string  $message    Message.
     * @return  \Hoa\Notification\Growl
     */
    public function notify($title, $message)
    {
        $channel = $this->getChannel();

        if (false === $this->_channels[$channel]) { // _default
            $this->handshake();
        }

        $flags   = 0;
        $data    = pack(
                       'c2n5',
                       1,
                       1,
                       $flags,
                       strlen($channel),
                       strlen($title),
                       strlen($message),
                       strlen($this->getApplicationName())
                   ) .
                   $channel .
                   $title .
                   $message .
                   $this->getApplicationName();

        $this->_client->writeAll(
            $data .
            pack('H32', md5($data . $this->getPassword()))
        );

        return $this;
    }

    /**
     * Set application's name.
     *
     * @param   string  $applicationName    Application's name.
     * @return  string
     */
    protected function setApplicationName($applicationName)
    {
        $old                    = $this->_applicationName;
        $this->_applicationName = $applicationName;

        return $old;
    }

    /**
     * Get application's name.
     *
     * @return  string
     */
    public function getApplicationName()
    {
        return $this->_applicationName;
    }

    /**
     * Set application's password.
     *
     * @param   string  $password    Application's password.
     * @return  string
     */
    protected function setPassword($password)
    {
        $old             = $this->_password;
        $this->_password = $password;

        return $old;
    }

    /**
     * Get application's password.
     *
     * @return  string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Get current channel.
     *
     * @return  string
     */
    public function getChannel()
    {
        return $this->_channel;
    }

    /**
     * Get all channels.
     *
     * @return  array
     */
    public function getChannels()
    {
        return $this->_channels;
    }

    /**
     * Check if a channel exists.
     *
     * @return  string
     */
    public function channelExists($channel)
    {
        return array_key_exists($channel, $this->getChannels());
    }
}

}
