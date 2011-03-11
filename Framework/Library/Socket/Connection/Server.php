<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace {

from('Hoa')

/**
 * \Hoa\Socket\Connection\Exception
 */
-> import('Socket.Connection.Exception')

/**
 * \Hoa\Socket\Connection
 */
-> import('Socket.Connection.~')

/**
 * \Hoa\Socket\Connection\Node
 */
-> import('Socket.Connection.Node');

}

namespace Hoa\Socket\Connection {

/**
 * Class \Hoa\Socket\Connection\Server.
 *
 * Established a server connection.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
     * @var \Hoa\Socket\Connection\Server resource
     */
    protected $_master   = null;

    /**
     * Stack of connections.
     *
     * @var \Hoa\Socket\Connection\Server array
     */
    protected $_stack    = null;

    /**
     * List of nodes (connections) when selecting.
     *
     * @var \Hoa\Socket\Connection\Server array
     */
    protected $_nodes    = array();

    /**
     * Node name.
     *
     * @var \Hoa\Socket\Connection\Server string
     */
    protected $_nodeName = null;

    /**
     * Temporize selected connections when selecting.
     *
     * @var \Hoa\Socket\Connection\Server array
     */
    protected $_iterator = array();



    /**
     * Constructor.
     * Configure a socket.
     *
     * @access  public
     * @param   \Hoa\Socket\Socketable  $socket     Socket.
     * @param   int                     $timeout    Timeout.
     * @param   int                     $flag       Flag, see the self::* constants.
     * @param   string                  $context    Context ID (please, see the
     *                                              \Hoa\Stream\Context class).
     * @return  void
     * @throw   \Hoa\Socket\Connection\Exception
     */
    public function __construct ( \Hoa\Socket\Socketable $socket, $timeout = 30,
                                  $flag = -1, $context = null ) {

        if($flag == -1)
            $flag = self::BIND | self::LISTEN;
        else
            switch($socket->getTransport()) {

                case 'tcp':
                    $flag &= self::LISTEN;
                  break;

                case 'udp':
                    if($flag & self::LISTEN)
                        throw new Exception(
                            'Cannot use the flag ' .
                            '\Hoa\Socket\Connection\Server::LISTEN ' .
                            'for connect-less transports (such as UDP).', 0);

                    $flag &= self::BIND;
                  break;
            }

        parent::__construct($socket, $timeout, $flag, $context);
        $this->setNodeName('\Hoa\Socket\Connection\Node');

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string               $streamName    Stream name (e.g. path or URL).
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     * @throw   \Hoa\Socket\Connection\Exception
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
            if($errno == 0)
                throw new Exception(
                    'Server cannot join %s.', 0, $streamName);
            else
                throw new Exception(
                    'Server returns an error (number %d): %s.',
                    1, array($errno, $errstr));

        $this->_stack[] = $this->_master;

        return $this->_master;
    }

    /**
     * Connect and accept the first connection.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Socket\Connection\Exception
     */
    public function connect ( ) {

        parent::connect();

        $client = @stream_socket_accept($this->_master);

        if(false === $client)
            throw new Exception(
                'Operation timed out (nothing to accept).', 2);

        $this->_setStream($client);

        return;
    }

    /**
     * Connect but wait for select and accept new connections.
     *
     * @access  public
     * @return  void
     */
    public function connectAndWait ( ) {

        parent::connect();

        return;
    }

    /**
     * Select connections.
     *
     * @access  public
     * @return  \Hoa\Socket\Connection\Server
     * @throw   \Hoa\Socket\Connection\Exception
     */
    public function select ( ) {

        $read      = $this->_stack;
        $write     = null;
        $except    = null;
        $nodeClass = $this->getNodeName();

        stream_select($read, $write, $except, $this->getTimeout(), 0);

        foreach($read as $i => $socket) {

            if($this->_master == $socket) {

                $client = @stream_socket_accept($this->_master);

                if(false === $client)
                    throw new Exception(
                        'Operation timed out (nothing to accept).', 3);

                $id                = $this->getNodeId($client);
                $node              = new $nodeClass($id, $client);
                $this->_nodes[$id] = $node;
                $this->_stack[]    = $client;
                $this->_setStream($socket);
            }
            else
                $this->_iterator[] = $socket;
        }

        return $this;
    }

    /**
     * Set and get the current selected connection.
     *
     * @access  public
     * @return  \Hoa\Socket\Connection\Server
     */
    public function current ( ) {

        $current = current($this->_iterator);
        $this->_setStream($current);

        return $this->_nodes[$this->getNodeId($current)];
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

            return @fclose($current);
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
