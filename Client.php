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

namespace Hoa\Irc;

use Hoa\Event;
use Hoa\Socket as HoaSocket;

/**
 * Class \Hoa\Irc\Client.
 *
 * An IRC client.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Client extends HoaSocket\Connection\Handler implements Event\Listenable
{
    use Event\Listens;



    /**
     * Constructor.
     *
     * @param   \Hoa\Socket\Client  $client    Client.
     * @throws  \Hoa\Socket\Exception
     */
    public function __construct(HoaSocket\Client $client)
    {
        parent::__construct($client);
        $this->getConnection()->setNodeName('\Hoa\Irc\Node');
        $this->setListener(
            new Event\Listener(
                $this,
                [
                    'open',
                    'join',
                    'message',
                    'private-message',
                    'mention',
                    'other-message',
                    'ping',
                    'kick',
                    'invite',
                    'error'
                ]
            )
        );

        return;
    }

    /**
     * Run a node.
     *
     * @param   \Hoa\Socket\Node  $node    Node.
     * @return  void
     * @throws  \Hoa\Irc\Exception
     */
    protected function _run(HoaSocket\Node $node)
    {
        if (false === $node->hasJoined()) {
            $node->setJoined(true);
            $this->getListener()->fire('open', new Event\Bucket());

            return;
        }

        try {
            $line = $node->getConnection()->readLine();

            preg_match(
                '#^(?::(?<prefix>[^\s]+)\s+)?(?<command>[^\s]+)\s+(?<middle>[^:]+)?(:\s*(?<trailing>.+))?$#',
                $line,
                $matches
            );

            if (!isset($matches['command'])) {
                $matches['command'] = null;
            }

            switch ($matches['command']) {
                case 366: // RPL_ENDOFNAMES
                    list($nickname, $channel) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'join';
                    $bucket   = [
                        'nickname' => $nickname,
                        'channel'  => trim($channel)
                    ];

                    break;

                case 'PRIVMSG':
                    $middle   = trim($matches['middle']);
                    $message  = $matches['trailing'];
                    $username = $node->getUsername();

                    if ($username === $middle) {
                        $listener = 'private-message';
                    } elseif (false !== strpos($message, $username)) {
                        $node->setChannel($middle);
                        $listener = 'mention';
                    } else {
                        $node->setChannel($middle);
                        $listener = 'message';
                    }

                    $bucket   = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'message' => $message
                    ];

                    break;

                case 'PING':
                    $daemons  = explode(' ', $matches['trailing']);
                    $listener = 'ping';
                    $bucket   = [
                        'daemons' => $daemons
                    ];

                    if (isset($daemons[1])) {
                        $this->pong($daemons[0], $daemons[1]);
                    } else {
                        $this->pong($daemons[0]);
                    }

                    break;

                case 'KICK':
                    list($channel, ) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'kick';
                    $bucket   = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'channel' => trim($channel)
                    ];

                    break;

                case 'INVITE':
                    list($channel, ) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'invite';
                    $bucket   = [
                        'from'               => $this->parseNick($matches['prefix']),
                        'channel'            => trim($channel),
                        'invitation_channel' => trim($matches['trailing'])
                    ];

                    break;

                default:
                    if ($matches['command'] >= 400 && $matches['command'] < 600) {
                        $code = intval($matches['command']);

                        throw new Exception\ErrorReply(
                            'Error reply code %d.',
                            $code,
                            $code
                        );
                    }

                    $listener = 'other-message';
                    $bucket   = [
                        'line'        => $line,
                        'parsed_line' => $matches
                    ];
            }

            $this->getListener()->fire($listener, new Event\Bucket($bucket));
        } catch (\Exception $e) {
            $this->getListener()->fire(
                'error',
                new Event\Bucket([
                    'exception' => $e
                ])
            );
        }

        return;
    }

    /**
     * Send a message.
     *
     * @param   string            $message    Message.
     * @param   \Hoa\Socket\Node  $node       Node.
     * @return  \Closure
     */
    protected function _send($message, HoaSocket\Node $node)
    {
        return $node->getConnection()->writeAll($message . CRLF);
    }

    /**
     * Join a channel.
     *
     * @param   string  $username    Username.
     * @param   string  $channel     Channel.
     * @param   string  $password    Password.
     * @return  int
     */
    public function join($username, $channel, $password = null)
    {
        if (null !== $password) {
            $this->send('PASS ' . $password);
        }

        $this->send('USER ' . $username . ' 0 * :' . $username);

        $node = $this->getConnection()->getCurrentNode();
        $node->setUsername($username);
        $node->setChannel($channel);
        $this->setNickname($username);

        return $this->send('JOIN ' . $channel);
    }

    /**
     * Say something on a channel.
     *
     * @param   string  $message    Message.
     * @param   string  $to         Channel or username.
     * @return  void
     */
    public function say($message, $to = null)
    {
        if (null === $to) {
            $to = $this->getConnection()->getCurrentNode()->getChannel();
        }

        foreach (explode("\n", $message) as $line) {
            $this->send('PRIVMSG ' . $to . ' :' . $line);
        }

        return;
    }

    /**
     * Quit the network.
     *
     * @param   string  $message    Message.
     * @return  int
     */
    public function quit($message = null)
    {
        if (null !== $message) {
            $message = ' ' . $message;
        }

        return $this->send('QUIT' . $message);
    }

    /**
     * Set nickname.
     *
     * @param   string  $nickname    Nickname.
     * @return  int
     */
    public function setNickname($nickname)
    {
        return $this->send('NICK ' . $nickname);
    }

    /**
     * Set topic.
     *
     * @param   string  $topic      Topic.
     * @param   string  $channel    Channel.
     * @return  int
     */
    public function setTopic($topic, $channel = null)
    {
        if (null === $channel) {
            $channel = $this->getConnection()->getCurrentNode()->getChannel();
        }

        return $this->send('TOPIC ' . $channel . ' ' . $topic);
    }

    /**
     * Invite someone on a channel.
     *
     * @param   string  $nickname    Nickname.
     * @param   string  $channel     Channel.
     * @return  int
     */
    public function invite($nickname, $channel = null)
    {
        if (null === $channel) {
            $channel = $this->getConnection()->getCurrentNode()->getChannel();
        }

        return $this->send('INVITE ' . $nickname . ' ' . $channel);
    }

    /**
     * Reply to a ping.
     *
     * @param   string  $daemon     Daemon1.
     * @param   string  $daemon2    Daemon2.
     * @return  int
     */
    public function pong($daemon, $daemon2 = null)
    {
        $this->send('PONG ' . $daemon);

        if (null !== $daemon2) {
            $this->send('PONG ' . $daemon2);
        }

        return;
    }

    /**
     * Parse a valid nick identifier.
     *
     * @param   string  $nick    Nick.
     * @return  array
     */
    public function parseNick($nick)
    {
        preg_match(
            '#^(?<nick>[^!]+)!(?<user>[^@]+)@(?<host>.+)$#',
            $nick,
            $matches
        );

        return $matches;
    }
}
