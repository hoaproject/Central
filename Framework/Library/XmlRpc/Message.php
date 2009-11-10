<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_XmlRpc_Message
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_XmlRpc_Message.
 *
 * Create payload to send to RPC server, and treat the response.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
