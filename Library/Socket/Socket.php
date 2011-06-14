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
 * \Hoa\Socket\Transport
 */
-> import('Socket.Transport');

}

namespace Hoa\Socket {

/**
 * Class \Hoa\Socket.
 *
 * Socket analyzer.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Socket {

    /**
     * Address type: IPv6.
     *
     * @const int
     */
    const ADDRESS_IPV6   = 0;

    /**
     * Address type: IPv4.
     *
     * @const int
     */
    const ADDRESS_IPV4   = 1;

    /**
     * Address type: domain.
     *
     * @const int
     */
    const ADDRESS_DOMAIN = 2;

    /**
     * Address type: path.
     *
     * @const int
     */
    const ADDRESS_PATH   = 3;

    /**
     * Address.
     *
     * @var \Hoa\Socket string
     */
    protected $_address     = null;

    /**
     * Address type. Please, see the self::ADDRESS_* constants.
     *
     * @var \Hoa\Socket int
     */
    protected $_addressType = 0;

    /**
     * Port.
     *
     * @var \Hoa\Socket int
     */
    protected $_port        = -1;

    /**
     * Transport.
     *
     * @var \Hoa\Socket string
     */
    protected $_transport   = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $uri    URI.
     * @return  void
     */
    public function __construct ( $uri ) {

        $this->setURI($uri);

        return;
    }

    /**
     * Set URI.
     *
     * @access  public
     * @param   string  $uri    URI.
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    public function setURI ( $uri ) {

        $m = preg_match(
            '#(?<scheme>[^:]+)://' .
                '(?:\[(?<ipv6_>[^\]]+)\]:(?<ipv6_port>\d+)$|' .
                '(?<ipv4>(\*|\d+(?:\.\d+){3}))(?::(?<ipv4_port>\d+))?$|' .
                '(?<domain>[^:]+)(?::(?<domain_port>\d+))?$|' .
                '(?<ipv6>.+)$)#',
            $uri,
            $matches);

        if(0 === $m)
            throw new Exception(
                'URI %s is not recognized (it is not an IPv6, IPv4 nor ' .
                'domain name.', 0, $uri);

        $this->setTransport($matches['scheme']);

        if(isset($matches['ipv6_']) && !empty($matches['ipv6_'])) {

            $this->_address     = $matches['ipv6_'];
            $this->_addressType = self::ADDRESS_IPV6;
            $this->setPort($matches['ipv6_port']);
        }
        elseif(isset($matches['ipv6']) && !empty($matches['ipv6'])) {

            $this->_address     = $matches['ipv6'];
            $this->_addressType = self::ADDRESS_IPV6;
        }
        elseif(isset($matches['ipv4']) && !empty($matches['ipv4'])) {

            $this->_address     = $matches['ipv4'];
            $this->_addressType = self::ADDRESS_IPV4;

            if('*' == $this->_address)
                $this->_address = '0.0.0.0';

            if(isset($matches['ipv4_port']))
                $this->setPort($matches['ipv4_port']);
        }
        elseif(isset($matches['domain'])) {

            $this->_address     = $matches['domain'];

            if(false !== strpos($this->_address, '/'))
                $this->_addressType = self::ADDRESS_PATH;
            else
                $this->_addressType = self::ADDRESS_DOMAIN;

            if(isset($matches['domain_port']))
                $this->setPort($matches['domain_port']);
        }

        return;
    }

    /**
     * Set the port.
     *
     * @access  protected
     * @param   int  $port    Port.
     * @return  int
     * @throw   \Hoa\Socket\Exception
     */
    protected function setPort ( $port ) {

        if($port < 0)
            throw new \Hoa\Socket\Exception(
                'Port must be greater or equal than zero, given %d.', 1, $port);

        $old         = $this->_port;
        $this->_port = $port;

        return $old;
    }

    /**
     * Set the transport.
     *
     * @access  protected
     * @param   string  $transport    Transport (TCP, UDP etc.).
     * @return  string
     * @throw   \Hoa\Socket\Exception
     */
    protected function setTransport ( $transport ) {

        $transport = strtolower($transport);

        if(false === \Hoa\Socket\Transport::exists($transport))
            throw new \Hoa\Socket\Exception(
                'Transport %s is not enabled on this machin.', 2, $transport);

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
     * Get the address type.
     *
     * @access  public
     * @return  int
     */
    public function getAddressType ( ) {

        return $this->_addressType;
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

        $out = null;

        if(true === $this->hasTransport())
            $out .= $this->getTransport() . '://';

        if(true === $this->hasPort()) {

            if(self::ADDRESS_IPV6 === $this->getAddressType())
                $out .= '[' . $this->getAddress() . ']';
            else
                $out .= $this->getAddress();

            return $out . ':' . $this->getPort();
        }

        return $out . $this->getAddress();
    }
}

}
