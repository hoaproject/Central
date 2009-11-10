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
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Dispatcher_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Exception_ControllerIsNotFound
 */
import('Controller.Exception.ControllerIsNotFound');

/**
 * Hoa_Controller_Exception_ActionIsNotFound
 */
import('Controller.Exception.ActionIsNotFound');

/**
 * Hoa_Controller_Exception_ControllerNotExtendsActionStandard
 */
import('Controller.Exception.ControllerNotExtendsActionStandard');

/**
 * Hoa_Controller_Exception_Reflection
 */
import('Controller.Exception.Reflection');

/**
 * Hoa_Controller_Dispatcher_Action
 */
import('Controller.Dispatcher.Action');

/**
 * Class Hoa_Controller_Dispatcher_Abstract.
 *
 * Core of front controller.
 * This class dispatch primary and secondary directory.
 * It means : find all files, get instance of primary and secondary controller,
 * create link between them and continue to set up front controller and these
 * components.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Dispatcher_Abstract
 */

class          Hoa_Controller_Dispatcher_Abstract
    implements Hoa_Framework_Parameterizable {

    /**
     * Parameters of current controller.
     *
     * @var Hoa_Framework_Parameter object
     */
    protected $_parameters     = null;

    /**
     * Response.
     *
     * @var Hoa_Controller_Response_Standard object
     */
    protected $_response       = null;

    /**
     * Attached objects.
     *
     * @var ArrayObject object
     */
    protected $_attachedObject = null;

    /**
     * View.
     *
     * @var Hoa_View object
     */
    protected $_view           = null;

    /**
     * Dispatch, i.e. result of the dispatcher.
     *
     * @var Hoa_Controller_Dispatcher_Abstract string
     */
    protected $_dispatch       = '';



    /**
     * Set parameter of current controller.
     *
     * @access  public
     * @param   Hoa_Framework_Parameter  $parameters    Parameters.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setRequest ( Hoa_Framework_Parameter $parameters ) {

        $this->_parameters = $parameters;

        return $this;
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
     * Set response.
     *
     * @access  public
     * @param   Hoa_Controller_Response_Standard  $response    Response.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setResponse ( Hoa_Controller_Response_Standard $response ) {

        $this->_response = $response;

        return $this;
    }

    /**
     * Set view to Hoa_Controller_Dispatcher_Abstract.
     * Return $this for fluide interface.
     *
     * @access  public
     * @param   Hoa_View  $view    View.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setView ( Hoa_View $view ) {

        $this->_view = $view;

        return $this;
    }

    /**
     * Set attached objects.
     *
     * @access  public
     * @param   array   $objects    Attached objects.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setAttachedObject ( Array $objects ) {

        $this->_attachedObject = $objects;

        return $this;
    }

    /**
     * Set router to view.
     *
     * @access  public
     * @param   Hoa_Controller_Router_Standard  $router    Router.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setRouterToView ( Hoa_Controller_Router_Standard $router ) {

        $this->_view->setRouter($router);

        return $this;
    }

    /**
     * Find directories, import files, instance objet and call method.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Controller_Exception_ControllerIsNotFound
     * @throw   Hoa_Controller_Exception_ActionIsNotFound
     * @throw   Hoa_Controller_Exception_ControllerNotExtendsActionStandard
     * @throw   Hoa_Controller_Exception_Reflection
     * @throw   Hoa_Controller_Exception
     */
    public function dispatch ( ) {

        /**
         * Get controller and action names.
         */
        $controllerClass = $this->getFormattedParameter('controller.class');
        $actionMethod    = $this->getFormattedParameter('action.method');

        /**
         * Get controller path.
         */
        $directory       = $this->getFormattedParameter('controller.directory');
        $controllerFile  = $this->getFormattedParameter('controller.file');

        /**
         * Load primary controller.
         */
        $file            = $directory . $controllerFile;
        if(!file_exists($file))
            throw new Hoa_Controller_Exception_ControllerIsNotFound(
                'Primary controller %s should be in the file %s, ' .
                'but this last is not found.',
                0, array($controllerClass, $file));

        require_once $file;

        $gcm = get_class_methods($controllerClass);

        if(!is_array($gcm))
            throw new Hoa_Controller_Exception_ControllerIsNotFound(
                'Peek in the primary controller file %s ' .
                'and the class %s is not found.',
                1, array($file, $controllerClass));

        /**
         * Action is in primary controller.
         */
        if(in_array($actionMethod, $gcm)) {

            $class  = $controllerClass;
            $action = $actionMethod;
        }
        /**
         * Action is in secondary controller.
         */
        else {

            /**
             * Get action path.
             */
            $actionDirectory = $this->getFormattedParameter('action.directory');
            $actionFile      = $this->getFormattedParameter('action.file');
            $class           = $this->getFormattedParameter('action.class');
            $action          = $actionMethod;

            /**
             * Load secondary controller.
             */
            $file            = $directory . $actionDirectory . $actionFile;
            if(!file_exists($file))
                throw new Hoa_Controller_Exception_ControllerIsNotFound(
                    'Secondary controller %s should be in the file %s, ' .
                    'but this last is not found.', 3, array($class, $file));

            require_once $file;
        }

        /**
         * Get controller class.
         */
        try {

            /**
             * Prepare reflection.
             */
            $reflection = new ReflectionClass($class);

            /**
             * Prepare arguments for constructor of primary (or secondary)
             * controller.
             */
            $arguments  = array(
                $this->_parameters,
                $this,
                $this->_response,
                $this->_view,
                $this->_attachedObject
            );

            /**
             * Create instance of primary (or secondary) controller.
             */
            $object = $reflection->newInstanceArgs($arguments);

            if($object instanceof Hoa_Controller_Action_Standard) {

                $this->_parameters->shareWith(
                    $this,
                    $object,
                    Hoa_Framework_Parameter::PERMISSION_READ
                );

                $this->_view->setDirectory($this->getFormattedParameter('view.directory'));
                $this->_view->setLayout($this->getFormattedParameter('view.layout.file'));
                $this->_view->enableLayout($this->getFormattedParameter('view.layout.enable'));
                $this->_view->setView($this->getFormattedParameter('view.action'));

                /**
                 * Bufferize result.
                 */
                ob_start();
                ob_implicit_flush(false);
                $obLevel     = ob_get_level();

                /**
                 * Initialize controller.
                 */
                $actionInit  = $reflection->hasMethod('init')
                                   ? $object->init()
                                   : null;

                if(!method_exists($object, $action))
                    throw new Hoa_Controller_Exception_ActionIsNotFound(
                        'Action %s is not found in controller %s.',
                        1, array($action, get_class($object)));

                /**
                 * Run action.
                 */
                $actionRun   = $object->$action();

                /**
                 * Stop buffer.
                 */
                $return      = null;
                while(ob_get_level() >= $obLevel)
                    $return .= ob_get_clean();
            }
            else {

                throw new Hoa_Controller_Exception_ControllerNotExtendsActionStandard(
                    'Class %s must be extend Hoa_Controller_Action_Standard.',
                    1, $class);
            }
        }
        catch ( ReflectionException $e ) {

            throw new Hoa_Controller_Exception_Reflection(
                $e->getMessage(),
                $e->getCode()
            );
        }
        catch ( Hoa_Controller_Exception $e ) {

            throw $e;
        }

        /**
         * Set dispatch (given by action method).
         */
        $this->setDispatch($return);

        return $this->getDispatch();
    }

    /**
     * Set dispatcher result.
     *
     * @access  public
     * @param   mixed   $dispatch    Dispatch.
     * @return  mixed
     */
    public function setDispatch ( $dispatch ) {

        $old             = $this->_dispatch;
        $this->_dispatch = $dispatch;

        return $old;
    }

    /**
     * Get dispatcher result.
     *
     * @access  public
     * @return  mixed
     */
    public function getDispatch ( ) {

        return $this->_dispatch;
    }
}
