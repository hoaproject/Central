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
 * @subpackage  Hoa_Controller_Front
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Exception
 */
import('Controller.Exception');

/**
 * Hoa_Controller_Plugin_Standard
 */
import('Controller.Plugin.Standard');

/**
 * Hoa_Controller_Request_Abstract
 */
import('Controller.Request.Abstract');

/**
 * Hoa_Controller_Router_Standard
 */
import('Controller.Router.Standard');

/**
 * Hoa_Controller_Response_Standard
 */
import('Controller.Response.Standard');

/**
 * Hoa_Controller_Dispatcher_Abstract
 */
import('Controller.Dispatcher.Abstract');

/**
 * Hoa_Controller_Action_Standard
 */
import('Controller.Action.Standard');

/**
 * Hoa_View
 */
import('View.~');

/**
 * Class Hoa_Controller_Front.
 *
 * Front controller is the core of controller system.
 * We run the front controller, and his dispatch.
 * The dispatcher will search primary and secondary controllers,
 * that will be parametered by request object and object parameters (from
 * router).
 * The result will be stocked in response object
 * that will be output at the end of the dispatch.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Front
 */

class Hoa_Controller_Front {

    /**
     * Hoa_Controller_Front instance.
     *
     * @var Hoa_Controller_Front object
     */
    private static $_instance          = null;

    /**
     * If exception should be thrown out from controller, this parameter should
     * be set to true, else false.
     *
     * @var Hoa_Controller_Front bool
     */
    protected $throwException          = false;

    /**
     * Hoa_Controller_Plugin_Standard instance.
     *
     * @var Hoa_Controller_Plugin_Standard string
     */
    protected $_plugin                 = null;

    /**
     * List of requests for each controller.
     *
     * @var Hoa_Controller_Front oject
     */
    protected $_request                = null;

    /**
     * Attached objects.
     *
     * @var Hoa_Controller_Front object
     */
    protected $_attachedObject         = null;

    /**
     * Default parameters.
     *
     * @var Hoa_Controler_Front array
     */
    private $defaultParameters         = array(
        'data.array'                   => array(),
        'data.array.personal'          => null,

        'route.type'                   => 'Get',
        'route.parameter'              => array(
            'default'                  => array(
                'module'               => 'index',
                'action'               => 'index'
            )
        ),
        /* Example of parameter for route.type = Rewrite.
        'route.parameter'              => array(
            'base'                     => '/MyBase',
            'rules'                    => array(
                'default'              => array(
                    'pattern'          => '/(:module)/(:action).html',
                    'default'          => array(
                        'module'       => 'index',
                        'action'       => 'index'
                    )
                )
            )
        ),
        */
        'route.controller.key'         => 'module',
        'route.controller.value'       => null,
        'route.action.key'             => 'action',
        'route.action.value'           => null,
        'route.directory'              => 'Application/Controller/',

        'view.theme'                   => 'MyTheme',
        'view.directory'               => 'Application/View/(:Theme)/',
        'view.layout'                  => 'Front',
        'view.enable.layout'           => true,
        'view.helper.directory'        => 'Application/View/Helper/',

        'model.directory'              => 'Application/Model/(:Controller)/',

        'pattern.controller.class'     => '(:Controller)Controller',
        'pattern.controller.file'      => '(:Controller)Controller.php',
        'pattern.controller.directory' => '(:Controller)Controller/',
        'pattern.action.class'         => '(:Action)Controller',
        'pattern.action.method'        => '(:Action)Action',
        'pattern.action.file'          => '(:Action)Controller.php',
        'pattern.view.layout'          => '(:Layout)Layout.phtml',
        'pattern.view.file'            => '(:Action)Action.phtml',
        'pattern.view.directory'       => '(:Controller)View/',
        'pattern.model.directory'      => '(:Controller)Model/'
    );

    /**
     * Current request parameters (by default, it is a copy of defautParameters).
     *
     * @var Hoa_Controller_Front array
     */
    protected $parameters              = array();



    /**
     * Prepare different objet, collections etc.
     * Prepare request ttachedObject objects collection, and also plugin system.
     * This constructor is private because of singleton design pattern.
     *
     * @access  private
     * @param   array    $parameters      Parameters (set to false if you do not
     *                                    want to add request automatically).
     * @param   bool     $autodispatch    Auto dispatch.
     * @return  void
     */
    private function __construct ( $parameters   = array(),
                                   $autoDispatch = false) {

        $this->_request = new ArrayObject(
            array(), ArrayObject::ARRAY_AS_PROPS, 'ArrayIterator');

        $this->_attachedObject = new ArrayObject(
            array(), ArrayObject::ARRAY_AS_PROPS, 'ArrayIterator');

        $this->_plugin  = new Hoa_Controller_Plugin_Standard();

        if(false !== $parameters)
            $this->addRequest(0, $parameters);

        if(false !== $autoDispatch)
            $this->dispatch();
    }

    /**
     * Get instance.
     * See the Singleton design pattern.
     *
     * @access  public
     * @param   array   $parameters      Parameters (set to false if you do not
     *                                   want to add request automatically).
     * @param   bool    $autodispatch    Auto dispatch.
     * @return  object
     */
    public static function getInstance ( $parameters   = array(),
                                         $autoDispatch = false) {

        if(null === self::$_instance)
            self::$_instance = new self($parameters, $autoDispatch);

        return self::$_instance;
    }

    /**
     * Add a request.
     * In a loop of controllers, each controller has his own request object.
     *
     * @access  public
     * @param   int     $index         Request index.
     * @param   array   $parameters    Parameters.
     * @return  object
     */
    public function addRequest ( $index = 0, Array $parameters = array() ) {

        if(!is_int($index))
            throw new Hoa_Controller_Exception('Index %s could be an integer.',
                0, $index);

        #IF_DEFINED HOA_STANDALONE
        if(empty($parameters))
            Hoa_Framework::configurePackage(
                'Controller', $parameters, Hoa_Framework::CONFIGURATION_MIXE,
                array('route.parameter'));
        #END_IF

        // Reset $this->parameters to $this->defaultParameters.
        $this->parameters = $this->defaultParameters;
        $this->setParameters($parameters);

        $this->_request->offsetSet(
            $index,
            new Hoa_Controller_Request_Abstract(
                $this->getParameters()
            )
        );

        return $this;
    }

    /**
     * Attach an object for all controllers.
     * These attached objects will be set and accessible by default
     * for all controllers in a variable.
     *
     * @access  public
     * @param   string  $variable    Object variable name.
     * @param   object  $object      Objet.
     * @return  object
     * @throw   Hoa_Controller_Exception
     */
    public function attachObject ( $variable = '', $object = null) {

        if(!preg_match('#([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#', $variable))
            throw new Hoa_Controller_Exception('%s is not a valid variable name.',
                1, $variable);

        if(!is_object($object))
            throw new Hoa_Controller_Exception('Object must be an object.', 2);

        $this->_attachedObject->offsetSet($variable, $object);

        return $this;
    }

    /**
     * Set parameters.
     *
     * @access  protected
     * @param   array      $parameters    Parameters.
     * @param   array      $recursive     Used for recursive parameters.
     * @return  array
     */
    protected function setParameters ( Array $parameters = array(),
                                             $recursive  = array()  ) {

        if($recursive === array()) {
            $array       =& $this->parameters;
            $recursivity = false;
        }
        else {
            $array       =& $recursive;
            $recursivity = true;
        }

        if(empty($parameters))
            return $array;

        foreach($parameters as $option => $value) {

            if($option == 'route.parameter') {

                $array[$option] = $value;
                continue;
            }

            if(empty($option) || (empty($value) && !is_bool($value)))
                continue;

            if(is_array($value))
                $array[$option] = $this->setParameters($value, $array[$option]);

            else
                $array[$option] = $value;
        }

        return $array;
    }

    /**
     * Get parameters.
     *
     * @access  protected
     * @param   string     $category    Category.
     * @return  array
     */
    protected function getParameters ( $category = null ) {

        if(!isset($this->parameters[$category]))
            return $this->parameters;

        return $this->parameters[$category];
    }

    /**
     * Start dispatching.
     * It's the main method of front controller.
     * We start router, response, dispatcher, and start the loop of controllers,
     * according to request objects collection, and router.
     * Plugin are notified between each new tasks.
     * And finally, response is output.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Controller_Exception
     */
    public function dispatch ( ) {

        /**
         * Instance router.
         */
        $router     = $this->getRouter();

        /**
         * Instance response, and create a new output.
         */
        $response   = $this->getResponse()->newOutput();

        /**
         * Instance router, set response and view.
         */
        $dispatcher = $this->getDispatcher()
                           ->setResponse($response)
                           ->setView(new Hoa_View())
                           ->setAttachedObject($this->_attachedObject);

        try {

            // Loop of controllers.
            $this->_request->ksort();
            $controller = $this->_request->getIterator();

            do {

                /**
                 * Current request object.
                 */
                $request        = $controller->current();

                /**
                 * Notify pre router.
                 */
                $preRouter      = $this->_plugin->notifyPreRouter($request);

                /**
                 * Set request to router, and run route.
                 */
                $route          = $router->setRequest($request)->route();

                /**
                 * Notify post router.
                 */
                $postRouter     = $this->_plugin->notifyPostRouter($request, $router);

                /**
                 * Notify pre dispatcher.
                 */
                $preDispatcher  = $this->_plugin->notifyPreDispatcher($request, $router);

                /**
                 * Set request to dispatch, and run dispatch.
                 */
                $dispatch       = $dispatcher->setRequest($request)
                                             ->setRouterToView($router)
                                             ->dispatch();

                /**
                 * Notify post dispatcher.
                 */
                $postDispatcher = $this->_plugin->notifyPostDispatcher(
                                      $request,
                                      $dispatcher,
                                      $dispatch
                                  );

                $response->appendOutput($dispatch);

                unset($request);

                $controller->next();

            } while($controller->valid());
        }
        catch ( Hoa_Controller_Exception $e ) {

            if(false === $this->getThrowException())
                $e->raiseError();
            else
                throw $e;
        }

        /**
         * Response.
         */
        $response->sendHeaders();
        $response->output();
    }

    /**
     * Get router object.
     *
     * @access  protected
     * @return  object
     */
    protected function getRouter ( ) {

        return new Hoa_Controller_Router_Standard();
    }

    /**
     * Get dispatcher object.
     *
     * @access  protected
     * @return  object
     */
    protected function getDispatcher ( ) {

        return new Hoa_Controller_Dispatcher_Abstract();
    }

    /**
     * Get response object.
     *
     * @access  protected
     * @return  object
     */
    protected function getResponse ( ) {

        return new Hoa_Controller_Response_Standard();
    }

    /**
     * Register plugin to notify.
     *
     * @access  public
     * @param   object  $plugin    Hoa_Controller_Plugin_Interface.
     * @return  array
     * @throw   Hoa_Controller_Plugin_Exception
     */
    public function registerPlugin ( Hoa_Controller_Plugin_Interface $plugin ) {

        $this->_plugin->register($plugin);
    }

    /**
     * Unregiser plugin.
     *
     * @access  public
     * @param   string  $pluginIndex    Plugin name.
     * @return  void
     */
    public function unregisterPlugin ( $pluginIndex = '' ) {

        $this->_plugin->unregister($pluginIndex);
    }

    /**
     * Set the parameter throwException. If it is set, all exception will be
     * thrown out of the controller, else a simple message (from the method
     * raiseError) will be print.
     *
     * @access  public
     * @param   bool    $throw    Throw exception or not ?
     * @return  bool
     */
    public function setThrowException ( $throw = false ) {

        $old                  = $this->throwException;
        $this->throwException = $throw;

        return $old;
    }

    /**
     * Get the parameter throwException.
     *
     * @access  public
     * @return  bool
     */
    public function getThrowException ( ) {

        return $this->throwException;
    }
}
