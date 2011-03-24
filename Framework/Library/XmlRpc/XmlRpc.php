<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @license     New BSD License
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
