<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 *
 *
 * @category    Framework
 * @package     Hoa_XmlRpc
 *
 */

/**
 * Hoa_XmlRpc_Exception
 */
import('XmlRpc.Exception');

/**
 * Class Hoa_XmlRpc.
 *
 * Initialize arrays.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_XmlRpc
 */

class Hoa_XmlRpc {

    /**
     * Datatype.
     *
     * @var Hoa_XmlRpc array
     */
    protected $datatype = array(
        'i4'       => 'i4',
        'int'      => 'int',
        'boolean'  => 'boolean',
        'double'   => 'double',
        'string'   => 'string',
        'datetime' => 'dateTime.iso8601',
        'base64'   => 'base64',
        'array'    => 'array',
        'struct'   => 'struct'
    );

    /**
     * Metatype.
     *
     * @var Hoa_XmlRpc array
     */
    protected $metatype = array(
        'i4'       => 1,
        'int'      => 1,
        'boolean'  => 1,
        'double'   => 1,
        'string'   => 1,
        'datetime' => 1,
        'base64'   => 1,
        'array'    => 2,
        'struct'   => 3
    );

    /**
     * Server errors.
     *
     * @var Hoa_XmlRpc array
     */
    protected $error = array(
            0 => 'Timeout cannot be lower than 0.',
            1 => 'Connection to server %s:%d failed.',
            2 => 'Writing headers and payload into socket failed.',
            3 => 'Server did not send response before timeout (max : %d).',
            4 => 'Payload cannot be empty.',
        32600 => 'Invalid Xml-Rpc. not conforming to specifications.',
        32601 => 'Requested method not found.',
        32602 => 'Invalid method parameters.',
        32603 => 'Internal Xml-Rpc error.'
    );

    /**
     * Default encoding.
     *
     * @var Hoa_XmlRpc string
     */
    protected $defencoding = 'utf-8';
}
