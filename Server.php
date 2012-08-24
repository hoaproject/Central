<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Socket\Exception
 */
-> import('Socket.Exception')

/**
 * \Hoa\Socket\Connection
 */
-> import('Socket.Connection')

/**
 * \Hoa\Socket\Node
 */
-> import('Socket.Node');

}

namespace Hoa\Socket {

/**
 * Class \Hoa\Socket\Server.
 *
 * Established a server connection.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Server extends Connection implements \Iterator {

    /**
     * Tell a stream to bind to the specified target.
     *
     * @const int
     */
    const BIND   = STREAM_SERVER_BIND;

    /**
     * Tell a stream to start listening on the socket.
     *
     * @const int
     */
    const LISTEN = STREAM_SERVER_LISTEN;

    /**
     * Master connection.
     *
     * @var \Hoa\Socket\Server resource
     */
    protected $_master   = null;

    /**
     * Stack of connections.
     *
     * @var \Hoa\Socket\Server array
     */
    protected $_stack    = null;

    /**
     * Node name.
     *
     * @var \Hoa\Socket\Server string
     */
    protected $_nodeName = null;

    /**
     * Current node.
     *
     * @var \Hoa\Socket\Node object
     */
    protected $_node     = null;

    /**
     * List of nodes (connections) when selecting.
     *
     * @var \Hoa\Socket\Server array
     */
    protected $_nodes    = array();

    /**
     * Temporize selected connections when selecting.
     *
     * @var \Hoa\Socket\Server array
     */
    protected $_iterator = array();



    /**
     * Start a connection.
     *
     * @access  public
     * @param   string  $socket     Socket URI.
     * @param   int     $timeout    Timeout.
     * @param   int     $flag       Flag, see the child::* constants.
     * @param   string  $context    Context ID (please, see the
     *                              \Hoa\Stream\Context class).
     * @return  void
     */
    public function __construct ( $socket, $timeout = 30,
                                  $flag = -1, $context = null ) {

        $this->setSocket($socket);
        $socket = $this->getSocket();

        if($flag == -1)
            switch($socket->getTransport()) {

                case 'tcp':
                    $flag = self::BIND | self::LISTEN;
                  break;

                case 'udp':
                    $flag = self::BIND;
                  break;
            }
        else
            switch($socket->getTransport()) {

                case 'tcp':
                    $flag &= self::LISTEN;
                  break;

                case 'udp':
                    if($flag & self::LISTEN)
                        throw new Exception(
                            'Cannot use the flag ' .
                            '\Hoa\Socket\Server::LISTEN ' .
                            'for connect-less transports (such as UDP).', 0);

                    $flag = self::BIND;
                  break;
            }

        parent::__construct(null, $timeout, $flag, $context);
        $this->setNodeName('\Hoa\Socket\Node');

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string               $streamName    Socket URI.
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     * @throw   \Hoa\Socket\Exception
     */
    protected function &_open ( $streamName, \Hoa\Stream\Context $context = null ) {

        if(null === $context)
            $this->_master = @stream_socket_server(
                $streamName,
                $errno,
                $errstr,
                $this->getFlag()
            );
        else
            $this->_master = @stream_socket_server(
                $streamName,
                $errno,
                $errstr,
                $this->getFlag(),
                $context->getContext()
            );

        if(false === $this->_master)
            throw new Exception(
                'Server cannot join %s and returns an error (number %d): %s.',
                0, array($streamName, $errno, $errstr));

        $this->_stack[] = $this->_master;

        return $this->_master;
    }

    /**
     * Connect and accept the first connection.
     *
     * @access  public
     * @return  \Hoa\Socket\Server
     * @throw   \Hoa\Socket\Exception
     */
    public function connect ( ) {

        parent::connect();

        $client = @stream_socket_accept($this->_master);

        if(false === $client)
            throw new Exception(
                'Operation timed out (nothing to accept).', 1);

        $this->_setStream($client);

        return $this;
    }

    /**
     * Connect but wait for select and accept new connections.
     *
     * @access  public
     * @return  \Hoa\Socket\Server
     */
    public function connectAndWait ( ) {

        return parent::connect();
    }

    /**
     * Select connections.
     *
     * @access  public
     * @return  \Hoa\Socket\Server
     * @throw   \Hoa\Socket\Exception
     */
    public function select ( ) {

        $read      = $this->_stack;
        $write     = null;
        $except    = null;
        $nodeClass = $this->getNodeName();

        @stream_select($read, $write, $except, $this->getTimeout(), 0);

        foreach($read as $socket)
            if($this->_master == $socket) {

                $client = @stream_socket_accept($this->_master);

                if(false === $client)
                    throw new Exception(
                        'Operation timed out (nothing to accept).', 2);

                $id                = $this->getNodeId($client);
                $node              = dnew($nodeClass, array($id, $client));
                $this->_nodes[$id] = $node;
                $this->_stack[]    = $client;
            }
            else
                $this->_iterator[] = $socket;

        return $this;
    }

    /**
     * Set and get the current selected connection.
     *
     * @access  public
     * @return  \Hoa\Socket\Server
     */
    public function current ( ) {

        $current = current($this->_iterator);
        $this->_setStream($current);

        return $this->_node = $this->_nodes[$this->getNodeId($current)];
    }

    /**
     * Get the current selected connection index.
     *
     * @access  public
     * @return  int
     */
    public function key ( ) {

        return key($this->_iterator);
    }

    /**
     * Advance the internal pointer of the connection iterator and return the
     * current selected connection.
     *
     * @access  public
     * @return  mixed
     */
    public function next ( ) {

        return next($this->_iterator);
    }

    /**
     * Rewind the internal iterator pointer and the first connection.
     *
     * @access  public
     * @return  mixed
     */
    public function rewind ( ) {

        return reset($this->_iterator);
    }

    /**
     * Check if there is a current connection after calls to the rewind() or the
     * next() methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty($this->_iterator))
            return false;

        $key    = key($this->_iterator);
        $return = (bool) next($this->_iterator);
        prev($this->_iterator);

        if(false === $return) {

            end($this->_iterator);
            if($key === key($this->_iterator))
                $return = true;
            else
                $this->_iterator = array();
        }

        return $return;
    }

    /**
     * Close the current stream.
     *
     * @access  protected
     * @return  bool
     */
    protected function _close ( ) {

        $current = $this->getStream();

        if($current != $this->_master) {

            $i = array_search($current, $this->_stack);

            if(false !== $i)
                unset($this->_stack[$i]);

            unset($this->_nodes[$this->getNodeId($current)]);

            @fclose($current);

            // Closing slave does not have the same effect that closing master.
            return false;
        }

        return (bool) (@fclose($this->_master) + @fclose($this->getStream()));
    }

    /**
     * Check if the server bind or not.
     *
     * @access  public
     * @return  bool
     */
    public function isBinding ( ) {

        return (bool) $this->getFlag() & self::BIND;
    }

    /**
     * Check if the server is listening or not.
     *
     * @access  public
     * @return  bool
     */
    public function isListening ( ) {

        return (bool) $this->getFlag() & self::LISTEN;
    }

    /**
     * Set node name.
     *
     * @access  public
     * @param   string  $node    Node name.
     * @return  string
     */
    public function setNodeName ( $node ) {

        $old             = $this->_nodeName;
        $this->_nodeName = $node;

        return $old;
    }

    /**
     * Get node name.
     *
     * @access  public
     * @return  string
     */
    public function getNodeName ( ) {

        return $this->_nodeName;
    }

    /**
     * Get current node.
     *
     * @access  public
     * @return  \Hoa\Socket\Node
     */
    public function getCurrentNode ( ) {

        return $this->_node;
    }

    /**
     * Get nodes list.
     *
     * @access  public
     * @return  array
     */
    public function getNodes ( ) {

        return $this->_nodes;
    }

    /**
     * Get node ID.
     *
     * @access  private
     * @param   resource  $resource    Resource.
     * @return  string
     */
    private function getNodeId ( $resource ) {

        ob_start();
        var_dump($resource);
        $id = md5(ob_get_contents());
        ob_end_clean();

        return $id;
    }
}

}
