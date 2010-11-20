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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Dispatcher
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Controller_Exception
 */
import('Controller.Exception');

/**
 * Hoa_Controller_Router
 */
import('Controller.Router') and load();

/**
 * Hoa_Controller_Application
 */
import('Controller.Application');

/**
 * Class Hoa_Controller_Dispatcher.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Dispatcher
 */

class Hoa_Controller_Dispatcher implements Hoa_Core_Parameterizable {

    /**
     * The Hoa_Controller_Dispatcher parameters.
     *
     * @var Hoa_Core_Parameter object
     */
    private $_parameters    = null;

    /**
     * Current view.
     *
     * @var Hoa_View_Viewable object
     */
    protected $_currentView = null;



    /**
     * Build a new dispatcher.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Core_Parameter(
            $this,
            array(
                'controller' => 'main',
                'action'     => 'main',
                'method'     => null
            ),
            array(
                'synchronous.file'        => 'hoa://Application/Controller/(:controller:U:).php',
                'synchronous.controller'  => '(:controller:U:)Controller',
                'synchronous.action'      => '(:action:U:)Action',

                'asynchronous.file'       => '(:%synchronous.file:)',
                'asynchronous.controller' => '(:%synchronous.controller:)',
                'asynchronous.action'     => '(:%synchronous.action:)Async'
            )
        );

        return;
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
     * Dispatch a router rule to a controller and an action (could be a class, a
     * stream, a closure, a function etc.).
     *
     * @access  public
     * @param   Hoa_Controller_Router  $router    Router.
     * @param   Hoa_View_Viewable      $view      View.
     * @return  void
     */
    public function dispatch ( Hoa_Controller_Router $router,
                               Hoa_View_Viewable     $view = null ) {

        $rule       = $router->getTheRule();

        if(null === $rule)
            $rule   = $router->route()->getTheRule();

        if(null === $view)
            $view   = $this->_currentView;
        else
            $this->_currentView = $view;

        $components = $rule[Hoa_Controller_Router::RULE_COMPONENT];
        $controller = $components['controller'];
        $action     = $components['action'];
        $called     = null;
        $arguments  = array();
        $reflection = null;
        $async      = $this->isCalledAsynchronously();

        if(false === $async) {

            $_file       = 'synchronous.file';
            $_controller = 'synchronous.controller';
            $_action     = 'synchronous.action';
        }
        else {

            $_file       = 'asynchronous.file';
            $_controller = 'asynchronous.controller';
            $_action     = 'asynchronous.action';
        }

        if(!isset($_SERVER['REQUEST_METHOD']))
            throw new Hoa_Controller_Exception(
                'Cannot identified the request method.', 0);

        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->_parameters->setKeyword($this, 'method', strtolower($method));

        if($action instanceof Closure) {

            $called               = $action;
            $reflection          = new ReflectionMethod($action, '__invoke');
            $components['_this'] = new Hoa_Controller_Application(
                $router,
                $this,
                $view
            );

            foreach($reflection->getParameters() as $i => $parameter) {

                $name = $parameter->getName();

                if(isset($components[$name])) {

                    $arguments[$name] = $components[$name];
                    continue;
                }

                if(false === $parameter->isOptional())
                    throw new Hoa_Controller_Exception(
                        'The closured action for the rule with pattern %s needs ' .
                        'a value for the parameter $%s and this value does not ' .
                        'exist.',
                        1, array($rule[Hoa_Controller_Router::RULE_PATTERN],
                                 $parameter->getName()));
            }
        }
        elseif(null === $controller && is_string($action)) {

            $reflection          = new ReflectionFunction($action);
            $components['_this'] = new Hoa_Controller_Application(
                $router,
                $this,
                $view
            );

            foreach($reflection->getParameters() as $i => $parameter) {

                $name = $parameter->getName();

                if(isset($components[$name])) {

                    $arguments[$name] = $components[$name];
                    continue;
                }

                if(false === $parameter->isOptional())
                    throw new Hoa_Controller_Exception(
                        'The functional action for the rule with pattern %s needs ' .
                        'a value for the parameter $%s and this value does not ' .
                        'exist.',
                        2, array($rule[Hoa_Controller_Router::RULE_PATTERN],
                                 $parameter->getName()));
            }
        }
        else {

            if(!is_object($controller)) {

                $this->_parameters->setKeyword($this, 'controller', $controller);
                $this->_parameters->setKeyword($this, 'action',     $action);

                $file       = $this->getFormattedParameter($_file);
                $controller = $this->getFormattedParameter($_controller);
                $action     = $this->getFormattedParameter($_action);

                if(!file_exists($file))
                    throw new Hoa_Controller_Exception(
                        'File %s is not found (method: %s, asynchronous: %s).',
                        3, array($file, $method,
                                 true === $async ? 'true': 'false'));

                require_once $file;

                if(!class_exists($controller))
                    throw new Hoa_Controller_Exception(
                        'Controller %s is not found in the file %s ' .
                        '(method: %s, asynchronous: %s).',
                        4, array($controller, $file, $method,
                                 true === $async ? 'true': 'false'));

                $controller = new $controller($router, $this, $view);
            }

            if(!($controller instanceof Hoa_Controller_Application))
                throw new Hoa_Controller_Exception(
                    'The controller must extend the Hoa_Controller_Application.', 4);

            $controller->construct();

            if(!method_exists($controller, $action))
                throw new Hoa_Controller_Exception(
                    'Action %s does not exist on the controller %s ' .
                    '(method: %s, asynchronous: %s).',
                    6, array($action, get_class($controller), $method,
                             true === $async ? 'true': 'false'));

            $called     = $controller;
            $reflection = new ReflectionMethod($controller, $action);

            foreach($reflection->getParameters() as $i => $parameter) {

                $name = $parameter->getName();

                if(isset($components[$name])) {

                    $arguments[$name] = $components[$name];

                    continue;
                }

                if(false === $parameter->isOptional())
                    throw new Hoa_Controller_Exception(
                        'The action %s on the controller %s needs a value for ' .
                        'the parameter $%s and this value does not exist.',
                        7, array($action, get_class($controller),
                                 $parameter->getName()));
            }
        }

        if($reflection instanceof ReflectionFunction)
            $return = $reflection->invokeArgs($arguments);
        elseif($reflection instanceof ReflectionMethod)
            $return = $reflection->invokeArgs($called, $arguments);

        return;
    }

    /**
     * Try to know if the dispatcher is called asynchronously.
     *
     * @access  public
     * @return  bool
     */
    public function isCalledAsynchronously ( ) {

        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            return false;

        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    }
}
