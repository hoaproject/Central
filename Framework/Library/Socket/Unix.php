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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Socket_Unix
 *
 */

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
 * Class Hoa_Socket_Unix.
 *
 * Handle Unix sockets.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Unix
 */

class Hoa_Socket_Unix implements Hoa_Socket_Interface {

    /**
     * Socket path.
     *
     * @var Hoa_socket_Unix string
     */
    protected $_address   = null;

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
     * @param   string    $path         Path to socket.
     * @param   string    $transport    Transport (Unix, UDG…).
     * @return  void
     */
    public function __construct ( $address, $transport ) {

        $this->setAdress($address);
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
    public function setAdress ( $address ) {

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
     * @throw   Hoa_Socket_Exception
     */
    public function setTransport ( $transport ) {

        $transport = strtolower($transport);

        if(false === Hoa_Socket_Transport::exists($transport))
            throw new Hoa_Socket_Exception(
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
