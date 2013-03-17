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

namespace Hoa\Socket\Server {

/**
 * Class \Hoa\Socket\Server\Handler.
 *
 * This class provides a server handler: a complete server skeleton.
 * We are able to run() a server, to merge() with other ones and to send
 * messages in different ways (A -> A, A -> B, A -> *\A etc.).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Handler {

    /**
     * Original server.
     *
     * @var \Hoa\Socket\Server object
     */
    protected $_originalServer = null;

    /**
     * Current server.
     *
     * @var \Hoa\Socket\Server object
     */
    protected $_server         = null;

    /**
     * All other servers that have been merged.
     *
     * @var \Hoa\Socket\Server object
     */
    protected $_servers        = array();



    /**
     * Constructor. Must be called.
     *
     * @access  public
     * @param   \Hoa\Socket\Server  $server    Server.
     * @return  void
     */
    public function __construct ( Server $server ) {

        $this->_originalServer = $server;
        $this->setServer($server);

        return;
    }

    /**
     * Set current server.
     *
     * @access  protected
     * @param   \Hoa\Socket\Server  $server    Server.
     * @return  \Hoa\Socket\Server
     */
    protected function setServer ( Server $server ) {

        $old           = $this->_server;
        $this->_server = $server;

        return $old;
    }

    /**
     * Get original server.
     *
     * @access  protected
     * @return  \Hoa\Socket\Server
     */
    protected function getOriginalServer ( ) {

        return $this->_originalServer;
    }

    /**
     * Get current server.
     *
     * @access  public
     * @return  \Hoa\Socket\Server
     */
    public function getServer ( ) {

        return $this->_server;
    }

    /**
     * The node dedicated part of the run() method.
     * A run is pretty simple, schematically:
     *
     *     while(true) foreach($server->select() as $node)
     *         // body
     *
     * The body is given by this method.
     *
     * @access  protected
     * @param   \Hoa\Socket\Node  $node    Node.
     * @return  void
     */
    abstract protected function _run ( \Hoa\Socket\Node $node );

    /**
     * Run the server.
     *
     * @access  public
     * @return  void
     */
    public function run ( ) {

        $server = $this->getServer();
        $server->connectAndWait();

        while(true) foreach($server->select() as $node) {

            foreach($this->_servers as $other)
                if(true === $server->is($other->getOriginalServer())) {

                    $other->_run($node);

                    continue 2;
                }

            $this->_run($node);
        }

        $server->disconnect();

        return;
    }

    /**
     * Merge a server into this one.
     * If we have two servers that must run at the same time, the
     * Hoa\Socket\Server::consider() and Hoa\Socket\Server::is() methods are
     * helpful but this whole class eases the merge of “high-level” servers.
     *
     * @access  public
     * @param   \Hoa\Socket\Server\Handler  $other    Server to merge.
     * @return  \Hoa\Socket\Server\Handler
     */
    public function merge ( self $other ) {

        $this->getServer()->consider($other->getServer());
        $other->setServer($this->getServer());
        $this->_servers[] = $other;

        return $this;
    }

    /**
     * The sending dedicated part of the self::send() method.
     * If the send() method is overrided with more arguments, this method could
     * return a function: it works like a currying.
     *
     * @access  protected
     * @param   string            $message    Message.
     * @param   \Hoa\Socket\Node  $node       Node (if null, current node).
     * @return  void
     */
    abstract protected function _send ( $message, \Hoa\Socket\Node $node );

    /**
     * Send a message to a specific node.
     *
     * @access  public
     * @param   string            $message    Message.
     * @param   \Hoa\Socket\Node  $node       Node (if null, current node).
     *                                        current node).
     * @return  mixed
     */
    public function send ( $message, \Hoa\Socket\Node $node = null ) {

        if(null === $node)
            return $this->_send($message, $this->getServer()->getCurrentNode());

        $old  = $this->getServer()->_setStream($node->getSocket());
        $send = $this->_send($message, $node);

        if($send instanceof \Closure)
            return function ( ) use ( &$send, &$old ) {

                $out = call_user_func_array($send, func_get_args());
                $this->getServer()->_setStream($old);

                return $out;
            };

        $this->getServer()->_setStream($old);

        return $send;
    }

    /**
     * Broadcast a message, i.e. send the message to all other nodes except the
     * current one.
     *
     * @access  public
     * @param   string  $message    Message.
     * @param   …       …           …
     * @return  void
     */
    public function broadcast ( $message ) {

        $server        = $this->getServer();
        $currentNode   = $server->getCurrentNode();
        $currentSocket = $this->getOriginalServer()->getSocket();

        if(1 === func_num_args()) {

            foreach($server->getNodes() as $node)
                if($node !== $currentNode)
                    if($node->getServer()->getSocket() === $currentSocket)
                        $this->send($message, $node);

            return;
        }

        $arguments = array_slice(func_get_args(), 1);
        array_unshift($arguments, $message, null);
        $callable  = array($this, 'send');

        foreach($server->getNodes() as $node)
            if($node !== $currentNode)
                if($node->getServer()->getSocket() === $currentSocket) {

                    $arguments[1] = $node;
                    call_user_func_array($callable, $arguments);
                }

        return;
    }
}

}
