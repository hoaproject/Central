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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Front
 */

class Hoa_Controller_Front implements Hoa_Framework_Parameterizable {

    /**
     * Hoa_Controller_Front instance.
     *
     * @var Hoa_Controller_Front object
     */
    private static $_instance  = null;

    /**
     * If exception should be thrown out from controller, this parameter should
     * be set to true, else false.
     *
     * @var Hoa_Controller_Front bool
     */
    protected $throwException  = false;

    /**
     * Hoa_Controller_Plugin_Standard instance.
     *
     * @var Hoa_Controller_Plugin_Standard object
     */
    protected $_plugin         = null;

    /**
     * List of requests for each controller.
     *
     * @var Hoa_Controller_Front array
     */
    protected $_requests       = array();

    /**
     * Attached objects.
     *
     * @var Hoa_Controller_Front array
     */
    protected $_attachedObject = array();

    /**
     * The Hoa_Controller parameters.
     *
     * @var Hoa_Framework_Parameter object
     */
    private $_parameters       = null;



    /**
     * Prepare different objet, collections etc.
     * Prepare request ttachedObject objects collection, and also plugin system.
     * This constructor is private because of singleton design pattern.
     *
     * @access  private
     * @param   array    $parameters      Parameters.
     * @param   bool     $autodispatch    Auto dispatch.
     * @return  void
     */
    private function __construct ( Array $parameters   = array(),
                                         $autoDispatch = false) {

        $this->_parameters = new Hoa_Framework_Parameter(
            $this,
            array(
                'controller'  => 'index',
                'action'      => 'index',
                'view'        => 'hend',
                'view.layout' => 'front'
            ),
            array(
                'data.array'          => array(),
                'data.array.personal' => null,

                'route.type'          => 'Get',
                'route.parameter.default.module' => '(:controller:)',
                'route.parameter.default.action' => '(:action:)',

                /* Example of parameters for route.type = Rewrite.
                'route.type'           => 'Rewrite',
                'route.parameter.base' => '/MyBase',
                'route.parameter.rules.default.pattern' => '/(:module)/(:action).html',
                'route.parameter.rules.default.default.module' => '(:controller:)',
                'route.parameter.rules.default.default.action' => '(:action:)',
                */

                'controller.class'      => '(:controller:U:)Controller',
                'controller.file'       => '(:controller:U:).php',
                'controller.directory'  => 'hoa://Application/Controller/',

                'action.class'          => '(:action:U:)Controller',
                'action.method'         => '(:action:U:)Action',
                'action.file'           => '(:action:U:).php',
                'action.directory'      => '(:%controller.directory:)(:%controller.file:r:)/',

                'model.share.directory' => 'hoa://Application/Model/',
                'model.directory'       => '(:%model.share.directory:)(:%controller.file:r:)/',

                'view.theme'            => '(:view:U:)Theme',
                'view.directory'        => 'hoa://Application/View/(:%view.theme:)/',
                'view.layout.file'      => '(:view.layout:U:).phtml',
                'view.layout.enable'    => true,
                'view.action'           => '(:controller:U:)/(:action:U:).phtml'
            )
        );

        $this->_attachedObject = array();
        $this->_plugin         = new Hoa_Controller_Plugin_Standard();

        $this->addRequest(0, $parameters);

        if(false !== $autoDispatch)
            $this->dispatch();

        return;
    }

    /**
     * Get instance.
     * See the Singleton design pattern.
     *
     * @access  public
     * @param   array   $parameters      Parameters.
     * @param   bool    $autodispatch    Auto dispatch.
     * @return  object
     */
    public static function getInstance ( $parameters   = array(),
                                         $autoDispatch = false ) {

        if(null === self::$_instance)
            self::$_instance = new self($parameters, $autoDispatch);

        return self::$_instance;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Add a request.
     * In a loop of controllers, each controller has its own request object.
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

        $this->_requests[$index] = clone $this->_parameters;
        $this->_requests[$index]->setParameters($this, $parameters);

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

        $this->_attachedObject[$variable] = $object;

        return $this;
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
        $router     = new Hoa_Controller_Router_Standard();

        /**
         * Instance response, and create a new output.
         */
        $response   = new Hoa_Controller_Response_Standard();
        $response->newOutput();

        /**
         * Instance router, set response and view.
         */
        $dispatcher = new Hoa_Controller_Dispatcher_Abstract();
        $dispatcher->setResponse($response)
                   ->setView(new Hoa_View())
                   ->setAttachedObject($this->_attachedObject);

        try {

            /**
             * Loop of controllers.
             */
            foreach($this->_requests as $i => $parameters) {

                /**
                 * Notify pre router.
                 */
                $preRouter      = $this->_plugin->notifyPreRouter($parameters);

                /**
                 * Set request to router, and run route.
                 */
                $parameters->shareWith(
                    $this,
                    $router,
                    Hoa_Framework_Parameter::PERMISSION_READ |
                    Hoa_Framework_Parameter::PERMISSION_WRITE
                );
                $route          = $router->setRequest($parameters)->route();

                /**
                 * Notify post router.
                 */
                $postRouter     = $this->_plugin->notifyPostRouter($parameters, $router);

                /**
                 * Notify pre dispatcher.
                 */
                $preDispatcher  = $this->_plugin->notifyPreDispatcher($parameters, $router);

                /**
                 * Set request to dispatch, and run dispatch.
                 */
                $parameters->shareWith(
                    $this,
                    $dispatcher,
                    Hoa_Framework_Parameter::PERMISSION_READ  |
                    Hoa_Framework_Parameter::PERMISSION_WRITE |
                    Hoa_Framework_Parameter::PERMISSION_SHARE
                );
                $dispatch       = $dispatcher->setRequest($parameters)
                                             ->setRouterToView($router)
                                             ->dispatch();

                /**
                 * Notify post dispatcher.
                 */
                $postDispatcher = $this->_plugin->notifyPostDispatcher(
                    $parameters,
                    $dispatcher,
                    $dispatch
                );

                $response->appendOutput($dispatch);

                unset($request);
            }
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

        return;
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

        return;
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

        return;
    }

    /**
     * Set the parameter throwException. If it is set, all exception will be
     * thrown out of the controller, else a simple message (from the method
     * raiseError) will be print.
     *
     * @access  public
     * @param   bool    $throw    Throw exception or not ?
     * @return  Hoa_Controller_Front
     */
    public function setThrowException ( $throw = false ) {

        $old                  = $this->throwException;
        $this->throwException = $throw;

        return $this;
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
