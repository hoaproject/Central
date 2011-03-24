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
 * @subpackage  Hoa_XmlRpc_Message
 *
 */

/**
 * Class Hoa_XmlRpc_Message.
 *
 * Create payload to send to RPC server, and treat the response.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Message
 */

class Hoa_XmlRpc_Message extends Hoa_XmlRpc {

    /**
     * Method name.
     *
     * @var Hoa_XmlRpc_Message string
     */
    protected $method = '';

    /**
     * Parameters of payload from Hoa_XmlRpc_Value.
     *
     * @var Hoa_XmlRpc_Value string
     */
    protected $params = null;

    /**
     * Payload.
     *
     * @var Hoa_XmlRpc_Value string
     */
    protected $payload = '';



    /**
     * __construct
     * This method sets some variables,
     * and recovers parameters from $params object.
     *
     * @access  public
     * @param   method  string    Method name.
     * @param   params  object    Hoa_XmlRpc_Values
     * @return  void
     */
    public function __construct ( $method, Hoa_XmlRpc_Value $params ) {

        $this->method = $method;
        $this->params = $params->get();
    }

    /**
     * createPayload
     * Create payload.
     *
     * @access  public
     * @return  string
     */
    public function createPayload ( ) {

        $this->payload = '<?xml version="1.0" encoding="' . $this->defencoding . '"?>' . "\n\n" .
                         '<methodCall>' . "\n" .
                         '  <methodName>' . $this->method . '</methodName>' . "\n" .
                         '  <params>' . "\n" .
                         '    <param>' . "\n" .
                         $this->params . "\n" .
                         '    </param>' . "\n" .
                         '  </params>' . "\n" .
                         '</methodCall>' . "\n";

        return $this->getPayload();
    }

    /**
     * getPayload
     * Return payload.
     *
     * @access  public
     * @return  string
     */
    public function getPayload ( ) {

        return $this->payload;
    }
}
