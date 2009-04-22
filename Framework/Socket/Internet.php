<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Internet
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Socket_Exception
 */
import('Socket.Exception');

/**
 * Hoa_Socket_Interface
 */
import('Socket.Interface');

/**
 * Hoa_Socket_Transport
 */
import('Socket.Transport');

/**
 * Class Hoa_Socket_Internet.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Internet
 */

abstract class Hoa_Socket_Internet implements Hoa_Socket_Interface {

    /**
     * Address.
     *
     * @var Hoa_Socket_Internet string
     */
    protected $_address   = null;

    /**
     * Port.
     *
     * @var Hoa_Socket_Internet int
     */
    protected $_port      = -1;

    /**
     * Transport.
     *
     * @var Hoa_Socket_Internet string
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
    public function __construct ( $address, $port, $transport = null ) {

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
     * @throw   Hoa_Socket_Exception
     */
    abstract public function setAddress ( $address );

    /**
     * Set the port.
     *
     * @access  public
     * @param   int     $port    Port.
     * @return  int
     * @throw   Hoa_Socket_Exception
     */
    public function setPort ( $port ) {

        if($port < 0)
            throw new Hoa_Socket_Exception(
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
     * @throw   Hoa_Socket_Exception
     */
    public function setTransport ( $transport ) {

        $transport = strtolower($transport);

        if(false === Hoa_Socket_Transport::exists($transport))
            throw new Hoa_Socket_Exception(
                'Transport %s is not enabled on this machin.', 1, $transport);

        $old              = $this->_transport;
        $this->_transport = strtolower($transport);

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
