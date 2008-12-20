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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Controller_Hoa_Controller_AbstractInterface.
 *
 * Dispatch part for action (from Hoa_Action_Standard).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Abstract
 */

class Hoa_Controller_Dispatcher_Action {

    /**
     * Response.
     *
     * @var Hoa_Controller_Response_Standard object
     */
    protected $response    = null;

    /**
     * View.
     *
     * @var Hoa_View object
     */
    protected $view        = null;

    /**
     * Request.
     *
     * @var Hoa_Controller_Request_Abstract object
     */
    private $_request      = null;

    /**
     * Dispatcher.
     *
     * @var Hoa_Controller_Dispatcher_Abstract object
     */
    private $_dispatcher   = null;

    /**
     * Router pattern.
     *
     * @var Hoa_Controller_Router_Pattern object
     */
    protected $_pattern    = null;

    /**
     * Magics variables (but used for attached objects).
     *
     * @var Hoa_Controler_Dispatcher_Action array
     */
    protected $_properties = array();



    /**
     * Set objects.
     *
     * @access  public
     * @param   Hoa_Controller_Request_Abstract     $request            Request.
     * @param   Hoa_Controller_Dispatcher_Abstract  $dispatcher         Dispatcher.
     * @param   Hoa_Controller_Response_Standard    $response           Response.
     * @param   Hoa_View                            $view               View.
     * @param   ArrayObject                         $attachedObjects    Attached
     *                                                                  objects.
     * @return  void
     */
    public function __construct ( Hoa_Controller_Request_Abstract    $request,
                                  Hoa_Controller_Dispatcher_Abstract $dispatcher,
                                  Hoa_Controller_Response_Standard   $response,
                                  Hoa_View                           $view,
                                  ArrayObject                        $attachedObject) {

        $this->_request    = $request;
        $this->_dispatcher = $dispatcher;
        $this->_pattern    = new Hoa_Controller_Router_Pattern();
        $this->response    = $response;
        $this->view        = $view;

        $iterator = $attachedObject->getIterator();
        while($iterator->valid()) {

            $this->__set($iterator->key(), $iterator->current());
            $iterator->next();
        }
    }

    /**
     * Overloading property.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  mixed
     */
    public function __set ( $name, $value ) {

        $old = null;

        if(isset($this->_properties[$name]))
            $old = $this->_properties[$name];

        $this->_properties[$name] = $value;

        return $old;
    }

    /**
     * Overloading property.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  mixed
     */
    public function __get ( $name ) {

        if(!isset($this->_properties[$name]))
            return null;

        $cast = gettype($this->_properties[$name]);

        switch($cast) {

            // A bug from the first releases of PHP 5 (maybe corrected since PHP
            // 5.3).
            case 'array':
                return (array) $this->_properties[$name];
              break;

            default:
                return $this->_properties[$name];
        }
    }

    /**
     * Protect request setter for main and secondary controllers.
     *
     * @access  public
     * @param   string  $request    Request.
     * @return  mixed
     */
    public function requestGetParameter ( $request = '' ) {

        return $this->_request->getParameter($request);
    }

    /**
     * Protect request setter for main and secondary controllers.
     *
     * @access  public
     * @return  array
     */
    public function requestGetParameters ( ) {

        return $this->_request->getParameters();
    }

    /**
     * Return a parameter parsed by the routeur (from URL e.g.).
     *
     * @access  public
     * @param   string  $param       Parameter to recover.
     * @param   bool    $personal    Recover from data.array.personal or not ?
     * @return  string
     */
    public function getParam ( $param = null, $personal = false ) {

        $request = 'data.array' . (false !== $personal ? '.personal' : '');
        $array   = $this->_request->getParameter($request);

        if(isset($array[$param]))
            $param = $array[$param];

        return $param;
    }
}
