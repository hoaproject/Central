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
 * \Hoa\Socket\Exception
 */
-> import('Socket.Exception');

}

namespace Hoa\Socket\Connection {

/**
 * Class \Hoa\Socket\Connection\Group.
 *
 * Represent a group of connection handlers.
 * Add semantics around Hoa\Socket\Connection\Handler.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Group implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * All connections.
     *
     * @var \Hoa\Socket\Connection\Group array
     */
    protected $_connections = array();



    /**
     * Check if a connection offset exists.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return true === array_key_exists($offset, $this->_connections);
    }

    /**
     * Get a specific connection.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @return  \Hoa\Socket\Connection\Handler
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset))
            return null;

        return $this->_connections[$offset];
    }

    /**
     * Add a connection.
     *
     * @access  public
     * @param   mixed                           $offset        Offset.
     * @param   \Hoa\Socket\Connection\Handler  $connection    Connection
     *                                                         (handler).
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function offsetSet ( $offset, $connection ) {

        if(!($connection instanceof Handler))
            throw new \Hoa\Socket\Exception(
                '%s only accepts %s\Handler objects.',
                0, array(__CLASS__, __NAMESPACE__));

        if(null === $offset)
            $this->_connections[]        = $connection;
        else
            $this->_connections[$offset] = $connection;

        if(1 < count($this))
            $this->getFirstConnection()->merge($connection);

        return;
    }

    /**
     * Nothing (not allowed).
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function offsetUnset ( $offset ) {

        throw new \Hoa\Socket\Exception(
            'This operation is not allowed: you cannot unset a connection ' .
            'from a group.', 1);

        return;
    }

    /**
     * Get iterator of all declared connections.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        return new \ArrayIterator($this->_connections);
    }

    /**
     * Count number of declared connections.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_connections);
    }

    /**
     * Semantics alias of $this->offsetSet(null, $connection).
     *
     * @access  public
     * @param   \Hoa\Socket\Connection\Handler  $connection    Connection
     *                                                         (handler).
     * @return  \Hoa\Socket\Connection\Group
     */
    public function merge ( Handler $connection ) {

        $this[] = $connection;

        return $this;
    }

    /**
     * Run the group of connections.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function run ( ) {

        if(0 === count($this))
            throw new \Hoa\Socket\Exception(
                'Nothing to run. You should merge a connection.', 2);

        return $this->getFirstConnection()->run();
    }

    /**
     * Get the first declared connection (where other connections have been
     * merged).
     *
     * @access  public
     * @return  \Hoa\Socket\Connection\Handler
     */
    public function getFirstConnection ( ) {

        return $this[key($this->_connections)];
    }
}

}
