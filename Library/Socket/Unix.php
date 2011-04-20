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

namespace Hoa\Socket {

/**
 * Class \Hoa\Socket\Unix.
 *
 * Handle Unix sockets.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Unix implements Socketable {

    /**
     * Socket path.
     *
     * @var Hoa_socket\Unix string
     */
    protected $_address   = null;

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
     * @param   string    $path         Path to socket.
     * @param   string    $transport    Transport (Unix, UDG…).
     * @return  void
     */
    public function __construct ( $address, $transport ) {

        $this->setAddress($address);
        $this->setTransport($transport);

        return;
    }

    /**
     * Set address.
     *
     * @access  public
     * @param   string  $adress    Address.
     * @return  string
     */
    public function setAddress ( $address ) {

        $old            = $this->_address;
        $this->_address = $address;

        return $old;
    }

    /**
     * Set the transport.
     *
     * @access  public
     * @param   string  $transport    Transport (Unix, UDG…).
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    public function setTransport ( $transport ) {

        $transport = strtolower($transport);

        if(false === Transport::exists($transport))
            throw new Exception(
                'Transport %s is not enabled on this machin.', 0, $transport);

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
               $this->getAddress();
    }
}

}
