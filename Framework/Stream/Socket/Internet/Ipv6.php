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
 * @package     Hoa_Stream
 * @subpackage  Hoa_Stream_Socket_Internet_Ipv6
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Stream_Socket_Exception
 */
import('Stream.Socket.Exception');

/**
 * Hoa_Stream_Socket_Internet
 */
import('Stream.Socket.Internet');

/**
 * Class Hoa_Stream_Socket_Internet_Ipv6.
 *
 * Handle IPv6.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Stream
 * @subpackage  Hoa_Stream_Socket_Internet_Ipv6
 */

class Hoa_Stream_Socket_Internet_Ipv6 extends Hoa_Stream_Socket_Internet {

    /**
     * Set address.
     *
     * @access  public
     * @param   string  $address    Address.
     * @return  string
     * @throw   Hoa_Stream_Socket_Exception
     */
    public function setAddress ( $address ) {

        if(0 == preg_match('##', $address))
            throw new Hoa_Stream_Socket_Exception(
                'Address %s is not a valid IPv6 address.', 0, $address);

        $old            = $this->_address;
        $this->_address = $address;

        return $old;
    }
}
