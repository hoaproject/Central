<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Socket\Socketable
 */
-> import('Socket.Socketable')

/**
 * \Hoa\Socket\Transport
 */
-> import('Socket.Transport');

}

namespace Hoa\Socket\Internet {

/**
 * Class \Hoa\Socket\Internet.
 *
 * Mother class for Internet sockets.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

abstract class Internet implements \Hoa\Socket\Socketable {

    /**
     * Address.
     *
     * @var \Hoa\Socket\Internet string
     */
    protected $_address   = null;

    /**
     * Port.
     *
     * @var \Hoa\Socket\Internet int
     */
    protected $_port      = -1;

    /**
     * Transport.
     *
     * @var \Hoa\Socket\Internet string
     */
    protected $_transport = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $address      Address.
     * @param   int     $port         Port.
     * @param   string  $transport    Transport (TCP, UDP etc.).
     * @return  void
     */
    public function __construct ( $address, $port, $transport ) {

        $this->setAddress($address);
        $this->setPort($port);
        $this->setTransport($transport);

        return;
    }

    /**
     * Set address.
     *
     * @access  public
     * @param   string  $address    Address.
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    abstract public function setAddress ( $address );

    /**
     * Set the port.
     *
     * @access  public
     * @param   int     $port    Port.
     * @return  int
     * @throw   \Hoa\Socket\Exception
     */
    public function setPort ( $port ) {

        if($port < 0)
            throw new \Hoa\Socket\Exception(
                'Port must be greater or equal than zero, given %d.', 0, $port);

        $old         = $this->_port;
        $this->_port = $port;

        return $old;
    }

    /**
     * Set the transport.
     *
     * @access  public
     * @param   string  $transport    Transport (TCP, UDP etc.).
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    public function setTransport ( $transport ) {

        $transport = strtolower($transport);

        if(false === \Hoa\Socket\Transport::exists($transport))
            throw new \Hoa\Socket\Exception(
                'Transport %s is not enabled on this machin.', 1, $transport);

        $old              = $this->_transport;
        $this->_transport = $transport;

        return $old;
    }

    /**
     * Get the address.
     *
     * @access  public
     * @return  string
     */
    public function getAddress ( ) {

        return $this->_address;
    }

    /**
     * Check if a port was declared.
     *
     * @access  public
     * @return  string
     */
    public function hasPort ( ) {

        return -1 != $this->getPort();
    }

    /**
     * Get the port.
     *
     * @access  public
     * @return  int
     */
    public function getPort ( ) {

        return $this->_port;
    }

    /**
     * Check if a transport was declared.
     *
     * @access  public
     * @return  bool
     */
    public function hasTransport ( ) {

        return null !== $this->getTransport();
    }

    /**
     * Get the transport.
     *
     * @access  public
     * @return  string
     */
    public function getTransport ( ) {

        return $this->_transport;
    }

    /**
     * Get a string that represents the socket address.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return (true === $this->hasTransport()
                  ? $this->getTransport() . '://'
                  : '') .
               $this->getAddress() .
               (true === $this->hasPort()
                  ? ':' . $this->getPort()
                  : '');
    }
}

}
