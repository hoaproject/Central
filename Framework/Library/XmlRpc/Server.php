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
 * @subpackage  Hoa_XmlRpc_Server
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_XmlRpc
 */
import('XmlRpc.~');

/**
 * Hoa_XmlRpc_Value
 */
import('XmlRpc.Value');

/**
 * Hoa_XmlRpc_Message
 */
import('XmlRpc.Message');

/**
 * Hoa_Xml
 */
import('Xml.~');

/**
 * Class Hoa_XmlRpc_Server.
 *
 * Recover $HTTP_RAW_POST_DATA, run method, and send a response.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_XmlRpc
 * @subpackage  Hoa_XmlRpc_Server
 */

class Hoa_XmlRpc_Server extends Hoa_XmlRpc {

    /**
     * Path to Xml Rpc methods directory.
     *
     * @var Hoa_XmlRpc_Server string
     */
    protected $path = '';

    /**
     * php://stdin content.
     *
     * @var Hoa_XmlRpc_Server string
     */
    protected $data = '';

    /**
     * Headers.
     *
     * @var Hoa_XmlRpc_Server string
     */
    protected $header = '';

    /**
     * Xml data, parsed into a nested array.
     *
     * @var Hoa_XmlRpc_Server array
     */
    protected $xml = array();

    /**
     * Method name.
     *
     * @var Hoa_XmlRpc_Server string
     */
    protected $method = '';

    /**
     * Payload.
     *
     * @var Hoa_XmlRpc_Server string
     */
    protected $payload = '';

    /**
     * Error has occured ?
     *
     * @var Hoa_XmlRpc_Server bool
     */
    protected $err = false;



    /**
     * __construct
     * Parse php://stdin.
     * Create appropriate class. For example :
     *     method          : system.listMethods,
     *     is associate to : Hoa_XmlRpc_Method_System_ListMethods class.
     * If class file is not found, an error is thrown.
     *
     * @access  public
     * @param   path    string    Path to Xml Rpc methods directory.
     * @return  string
     * @throw   Hoa_XmlRpc_Exception
     */
    public function __construct ( $path = '' ) {

        global $HTTP_RAW_POST_DATA;

        $this->data = $HTTP_RAW_POST_DATA;

        if(empty($this->data))
            return false;

        $xml          = new Hoa_Xml;
        $pos          = strpos($this->data, '<?xml');
        $this->header = trim(substr($this->data, 0, $pos));
        $this->xml    = $xml->parse(substr($this->data, $pos, -1), null);
        $this->method = $this->xml['methodName'];
        $headers      = preg_split("#\r|\n#", $this->header);

        foreach($headers as $header)
            header($header);

        if(file_exists($path . $this->method . '.php')) {

            require_once $path . $this->method . '.php';
            $class  = 'Hoa_XmlRpc_Method_' .
                      implode('_',
                          array_map('ucfirst',
                              explode('.', $this->method)
                          )
                      );
            $parameters   = array($path, $this->xml['params']);
            $reflection   = new ReflectionClass($class);

            if($reflection->hasMethod('__construct'))
                $object   = $reflection->newInstanceArgs($parameters);
            else
                $object   = $reflection->newInstance();

            $this->params = $object->get();
        }
        else {
            $this->params = new Hoa_XmlRpc_Value(array(
                'faultCode' => new Hoa_XmlRpc_Value(32601, 'i4'),
                'fautlString' => new Hoa_XmlRpc_Value($this->error[32601])
                ), 'struct');
            $this->params = $this->params->get();
            $this->err    = true;
        }

        echo $this->createPayload();
    }

    /**
     * createPayload
     * This method writes payload with parameters.
     *
     * @access  public
     * @return  string
     */
    public function createPayload ( ) {

        $this->payload  = '<?xml version="1.0" encoding="' . $this->defencoding . '"?>' . "\n\n" .
                          '<methodResponse>' . "\n";

        if($this->err === false)
        $this->payload .= '  <params>' . "\n" .
                          '    <param>' . "\n" .
                          '      <value>' . "\n" .
                          $this->params . "\n" .
                          '      </value>' . "\n" .
                          '    </param>' . "\n" .
                          '  </params>' . "\n";
        else
        $this->payload .= '  <fault>' . "\n" .
                          '    <value>' . "\n" .
                          $this->params . "\n" .
                          '    </value>' . "\n" .
                          '  </fault>' . "\n";

        $this->payload .= '</methodResponse>' . "\n";

        return $this->getPayload();
    }

    /**
     * getPayload
     * Get payload.
     *
     * @access  public
     * @return  string
     */
    public function getPayload ( ) {

        return $this->payload;
    }
}
