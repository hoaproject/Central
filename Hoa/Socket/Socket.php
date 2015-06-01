<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

namespace Hoa\Socket;

use Hoa\Core;

/**
 * Class \Hoa\Socket.
 *
 * Socket analyzer.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Socket
{
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
     * @var string
     */
    protected $_address     = null;

    /**
     * Address type. Please, see the self::ADDRESS_* constants.
     *
     * @var int
     */
    protected $_addressType = 0;

    /**
     * Port.
     *
     * @var int
     */
    protected $_port        = -1;

    /**
     * Transport.
     *
     * @var string
     */
    protected $_transport   = null;



    /**
     * Constructor.
     *
     * @param   string  $uri    URI.
     * @return  void
     */
    public function __construct($uri)
    {
        $this->setURI($uri);

        return;
    }

    /**
     * Set URI.
     *
     * @param   string  $uri    URI.
     * @return  string
     * @throws  \Hoa\Socket\Exception
     */
    public function setURI($uri)
    {
        $m = preg_match(
            '#(?<scheme>[^:]+)://' .
                '(?:\[(?<ipv6_>[^\]]+)\]:(?<ipv6_port>\d+)$|' .
                '(?<ipv4>(\*|\d+(?:\.\d+){3}))(?::(?<ipv4_port>\d+))?$|' .
                '(?<domain>[^:]+)(?::(?<domain_port>\d+))?$|' .
                '(?<ipv6>.+)$)#',
            $uri,
            $matches);

        if (0 === $m) {
            throw new Exception(
                'URI %s is not recognized (it is not an IPv6, IPv4 nor ' .
                'domain name).',
                0,
                $uri
            );
        }

        $this->setTransport($matches['scheme']);

        if (isset($matches['ipv6_']) && !empty($matches['ipv6_'])) {
            $this->_address     = $matches['ipv6_'];
            $this->_addressType = self::ADDRESS_IPV6;
            $this->setPort($matches['ipv6_port']);
        } elseif (isset($matches['ipv6']) && !empty($matches['ipv6'])) {
            $this->_address     = $matches['ipv6'];
            $this->_addressType = self::ADDRESS_IPV6;
        } elseif (isset($matches['ipv4']) && !empty($matches['ipv4'])) {
            $this->_address     = $matches['ipv4'];
            $this->_addressType = self::ADDRESS_IPV4;

            if ('*' === $this->_address) {
                $this->_address = '0.0.0.0';
            }

            if (isset($matches['ipv4_port'])) {
                $this->setPort($matches['ipv4_port']);
            }
        } elseif (isset($matches['domain'])) {
            $this->_address = $matches['domain'];

            if (false !== strpos($this->_address, '/')) {
                $this->_addressType = self::ADDRESS_PATH;
            } else {
                $this->_addressType = self::ADDRESS_DOMAIN;
            }

            if (isset($matches['domain_port'])) {
                $this->setPort($matches['domain_port']);
            }
        }

        if (self::ADDRESS_IPV6 == $this->_addressType &&
            (
                !defined('STREAM_PF_INET6') ||
                (function_exists('socket_create') && !defined('AF_INET6'))
            )
           ) {
            throw new Exception(
                'IPv6 support has been disabled from PHP, we cannot use ' .
                'the %s URI.',
                1,
                $uri
            );
        }

        return;
    }

    /**
     * Set the port.
     *
     * @param   int  $port    Port.
     * @return  int
     * @throws  \Hoa\Socket\Exception
     */
    protected function setPort($port)
    {
        if ($port < 0) {
            throw new Exception(
                'Port must be greater or equal than zero, given %d.',
                2,
                $port
            );
        }

        $old         = $this->_port;
        $this->_port = $port;

        return $old;
    }

    /**
     * Set the transport.
     *
     * @param   string  $transport    Transport (TCP, UDP etc.).
     * @return  string
     * @throws  \Hoa\Socket\Exception
     */
    protected function setTransport($transport)
    {
        $transport = strtolower($transport);

        if (false === Transport::exists($transport)) {
            throw new Exception(
                'Transport %s is not enabled on this machin.',
                3,
                $transport
            );
        }

        $old              = $this->_transport;
        $this->_transport = $transport;

        return $old;
    }

    /**
     * Get the address.
     *
     * @return  string
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Get the address type.
     *
     * @return  int
     */
    public function getAddressType()
    {
        return $this->_addressType;
    }

    /**
     * Check if a port was declared.
     *
     * @return  string
     */
    public function hasPort()
    {
        return -1 != $this->getPort();
    }

    /**
     * Get the port.
     *
     * @return  int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * Check if a transport was declared.
     *
     * @return  bool
     */
    public function hasTransport()
    {
        return null !== $this->getTransport();
    }

    /**
     * Get the transport.
     *
     * @return  string
     */
    public function getTransport()
    {
        return $this->_transport;
    }

    /**
     * Get a string that represents the socket address.
     *
     * @return  string
     */
    public function __toString()
    {
        $out = null;

        if (true === $this->hasTransport()) {
            $out .= $this->getTransport() . '://';
        }

        if (true === $this->hasPort()) {
            if (self::ADDRESS_IPV6 === $this->getAddressType()) {
                $out .= '[' . $this->getAddress() . ']';
            } else {
                $out .= $this->getAddress();
            }

            return $out . ':' . $this->getPort();
        }

        return $out . $this->getAddress();
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Socket\Socket');
