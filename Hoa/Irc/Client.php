<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Irc\Exception
 */
-> import('Irc.Exception')

/**
 * \Hoa\Irc\Node
 */
-> import('Irc.Node')

/**
 * \Hoa\Socket\Connection\Handler
 */
-> import('Socket.Connection.Handler');

}

namespace Hoa\Irc {

/**
 * Class \Hoa\Irc\Client.
 *
 * An IRC client.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class          Client
    extends    \Hoa\Socket\Connection\Handler
    implements \Hoa\Core\Event\Listenable {

    /**
     * Listeners.
     *
     * @var \Hoa\Core\Event\Listener object
     */
    protected $_on       = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Socket\Client  $client    Client.
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function __construct ( \Hoa\Socket\Client $client ) {

        parent::__construct($client);
        $this->getConnection()->setNodeName('\Hoa\Irc\Node');
        $this->_on = new \Hoa\Core\Event\Listener($this, array(
            'open',
            'join',
            'message',
            'other-message',
            'ping',
            'error'
        ));

        return;
    }

    /**
     * Attach a callable to this listenable object.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $callable      Callable.
     * @return  \Hoa\Irc\Client
     * @throw   \Hoa\Core\Exception
     */
    public function on ( $listenerId, $callable ) {

        $this->_on->attach($listenerId, $callable);

        return $this;
    }

    /**
     * Run a node.
     *
     * @access  protected
     * @param   \Hoa\Socket\Node  $node    Node.
     * @return  void
     */
    protected function _run ( \Hoa\Socket\Node $node ) {

        if(false === $node->hasJoined()) {

            $node->setJoined(true);
            $this->_on->fire('open', new \Hoa\Core\Event\Bucket());

            return;
        }

        try {

            $line = $node->getConnection()->readLine();

            preg_match(
                '#^(?::(?<prefix>[^\s]+)\s+)?(?<command>[^\s]+)\s+(?<middle>[^:]+)?(:\s*(?<trailing>[^$]+))?$#',
                $line,
                $matches
            );

            if(!isset($matches['command']))
                $matches['command'] = null;

            switch($matches['command']) {

                case 366: // RPL_ENDOFNAMES
                    list($nickname, $channel) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'join';
                    $bucket   = array(
                        'nickname' => $nickname,
                        'channel'  => $channel
                    );
                  break;

                case 'PRIVMSG':
                    $node->setChannel(trim($matches['middle']));
                    $listener = 'message';
                    $bucket   = array(
                        'from'    => $this->parseNick($matches['prefix']),
                        'message' => $matches['trailing']
                    );
                  break;

                case 'PING':
                    $daemons  = explode(' ', $matches['trailing']);
                    $listener = 'ping';
                    $bucket   = array(
                        'daemons' => $daemons
                    );

                    if(isset($daemons[1]))
                        $this->pong($daemons[0], $daemons[1]);
                    else
                        $this->pong($daemons[0]);
                  break;

                default:
                    $listener = 'other-message';
                    $bucket   = array('line' => $line);
            }

            $this->_on->fire($listener, new \Hoa\Core\Event\Bucket($bucket));
        }
        catch ( \Hoa\Core\Exception\Idle $e ) {

            $this->_on->fire('error', new \Hoa\Core\Event\Bucket(array(
                'exception' => $e
            )));
        }

        return;
    }

    /**
     * Send a message.
     *
     * @access  protected
     * @param   string            $message    Message.
     * @param   \Hoa\Socket\Node  $node       Node.
     * @return  \Closure
     */
    protected function _send ( $message, \Hoa\Socket\Node $node ) {

        return $node->getConnection()->writeAll($message . CRLF);
    }

    /**
     * Join a channel.
     *
     * @access  public
     * @param   string  $nickname    Nickname.
     * @param   string  $channel     Channel.
     * @param   string  $password    Password.
     * @return  int
     */
    public function join ( $nickname, $channel, $password = null ) {

        $this->_nickname = $nickname;
        $this->_channel  = $channel;

        if(null !== $password)
            $this->send('PASS ' . $password);

        $this->setNickname($nickname);
        $this->send('USER ' . $nickname . ' 0 * :' . $nickname);

        return $this->send('JOIN ' . $channel);
    }

    /**
     * Say something on a channel.
     *
     * @access  public
     * @param   string  $message    Message.
     * @param   string  $channel    Channel.
     * @return  string
     */
    public function say ( $message, $channel = null ) {

        if(null === $channel)
            $channel = $this->getConnection()->getCurrentNode()->getChannel();

        return $this->send('PRIVMSG ' . $channel . ' :' . $message);
    }

    /**
     * Quit the network.
     *
     * @access  public
     * @param   string  $message    Message.
     * @return  int
     */
    public function quit ( $message = null ) {

        if(null !== $message)
            $message = ' ' . $message;

        return $this->send('QUIT' . $message);
    }

    /**
     * Set nickname.
     *
     * @access  public
     * @param   string  $nickname    Nickname.
     * @return  int
     */
    public function setNickname ( $nickname ) {

        $this->getConnection()->getCurrentNode()->setNickname($nickname);

        return $this->send('NICK ' . $nickname);
    }

    /**
     * Set topic.
     *
     * @access  public
     * @param   string  $topic      Topic.
     * @param   string  $channel    Channel.
     * @return  int
     */
    public function setTopic ( $topic, $channel = null ) {

        if(null === $channel)
            $channel = $this->getConnection()->getCurrentNode()->getChannel();

        return $this->send('TOPIC ' . $channel . ' ' . $topic);
    }

    /**
     * Invite someone on a channel.
     *
     * @access  public
     * @param   string  $nickname    Nickname.
     * @param   string  $channel     Channel.
     * @return  int
     */
    public function invite ( $nickname, $channel = null ) {

        if(null === $channel)
            $channel = $this->getConnection()->getCurrentNode()->getChannel();

        return $this->send('INVITE ' . $nickname . ' ' . $channel);
    }

    /**
     * Reply to a ping.
     *
     * @access  public
     * @param   string  $daemon     Daemon1.
     * @param   string  $daemon2    Daemon2.
     * @return  int
     */
    public function pong ( $daemon, $daemon2 = null ) {

        $this->send('PONG ' . $daemon);

        if(null !== $daemon2)
            $this->send('PONG ' . $daemon2);

        return;
    }

    /**
     * Parse a valid nick identifier.
     *
     * @access  public
     * @param   string  $nick    Nick.
     * @return  array
     */
    public function parseNick ( $nick ) {

        preg_match(
            '#^(?<nick>[^!]+)!(?<user>[^@]+)@(?<host>[^$]+)$#',
            $nick,
            $matches
        );

        return $matches;
    }
}

}
