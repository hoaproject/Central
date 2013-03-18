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

namespace Hoa\Socket\Server {

/**
 * Class \Hoa\Socket\Server\Group.
 *
 * Represent a group of server handlers.
 * Add semantics around Hoa\Socket\Server\Handler.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Group implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * All servers.
     *
     * @var \Hoa\Socket\Server\Group array
     */
    protected $_servers = array();



    /**
     * Check if a server offset exists.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return true === array_key_exists($offset, $this->_servers);
    }

    /**
     * Get a specific server.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @return  \Hoa\Socket\Server\Handler
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset))
            return null;

        return $this->_servers[$offset];
    }

    /**
     * Add a server.
     *
     * @access  public
     * @param   mixed                       $offset    Offset.
     * @param   \Hoa\Socket\Server\Handler  $server    Server (handler).
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function offsetSet ( $offset, $server ) {

        if(!($server instanceof Handler))
            throw new \Hoa\Socket\Exception(
                '%s only accepts %s\Handler objects.',
                0, array(__CLASS__, __NAMESPACE__));

        if(null === $offset)
            $this->_servers[]        = $server;
        else
            $this->_servers[$offset] = $server;

        if(1 < count($this))
            $this->getFirstServer()->merge($server);

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
            'This operation is not allowed: you cannot unset a server from a ' .
            'group.', 1);

        return;
    }

    /**
     * Get iterator of all declared servers.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        return new \ArrayIterator($this->_servers);
    }

    /**
     * Count number of declared servers.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_servers);
    }

    /**
     * Semantics alias of $this->offsetSet(null, $server).
     *
     * @access  public
     * @param   \Hoa\Socket\Server\Handler  $server    Server (handler).
     * @return  \Hoa\Socket\Server\Group
     */
    public function merge ( Handler $server ) {

        $this[] = $server;

        return $this;
    }

    /**
     * Run the group of servers.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function run ( ) {

        if(0 === count($this))
            throw new \Hoa\Socket\Exception(
                'Nothing to run. You should merge a server.', 2);

        return $this->getFirstServer()->run();
    }

    /**
     * Get the first declared server (where other servers have been merged).
     *
     * @access  public
     * @return  \Hoa\Socket\Server\Handler
     */
    public function getFirstServer ( ) {

        return $this[key($this->_servers)];
    }
}

}
