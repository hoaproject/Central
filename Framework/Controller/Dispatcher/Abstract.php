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
 * @subpackage  Hoa_Controller_Dispatcher_Abstract
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Dispatcher_Action
 */
import('Controller.Dispatcher.Action');

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
 * Class Hoa_Controller_Dispatcher_Abstract.
 *
 * Core of front controller.
 * This class dispatch primary and secondary directory.
 * It means : find all files, get instance of primary and secondary controller,
 * create link between them and continue to set up front controller and these
 * components.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Dispatcher_Abstract
 */

class Hoa_Controller_Dispatcher_Abstract {

    /**
     * Request.
     *
     * @var Hoa_Controller_Request_Abstract object
     */
    protected $_request        = null;

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
     * Dispatch.
     *
     * @var Hoa_Controller_Dispatcher_Abstract mixed
     */
    protected $_dispatch       = '';



    /**
     * Set response to Hoa_Controller_Dispatcher_Abstract.
     * Return $this for fluide interface.
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
     * @param   ArrayObject  $objects    Attached objects.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setAttachedObject ( ArrayObject $objects ) {

        $this->_attachedObject = $objects;

        return $this;
    }

    /**
     * Set request to Hoa_Controller_Dispatcher_Abstract.
     *
     * @access  public
     * @param   Hoa_Controller_Request_Abstract  $request    Request.
     * @return  Hoa_Controller_Dispatcher_Abstract
     */
    public function setRequest ( Hoa_Controller_Request_Abstract $request ) {

        $this->_request = $request;

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
        $controllerClass = $this->getControllerClass();
        $actionMethod    = $this->getActionMethod();

        /**
         * Get controller path.
         */
        $directory       = $this->getDirectory();
        $controllerFile  = $this->getControllerFile();

        /**
         * Load primary controller.
         */
        $this->load($directory . $controllerFile);

        $gcm = get_class_methods($controllerClass);

        if(!is_array($gcm))
            throw new Hoa_Controller_Exception_ControllerIsNotFound(
                'Class %s is not found in %s file.', 0,
                array($controllerClass, $controllerFile));

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
            $controllerDirectory = $this->getControllerDirectory();
            $actionFile          = $this->getActionFile();
            $class               = $this->getActionClass();
            $action              = $actionMethod;

            /**
             * Load secondary controller.
             */
            $this->load($directory . $controllerDirectory . $actionFile);
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
                $this->_request,
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

                $object      = null;
                throw new Hoa_Controller_Exception_ControllerNotExtendsActionStandard(
                    'Class %s must be extend Hoa_Controller_Action_Standard.',
                    1, $class);
            }
        }
        catch ( ReflectionException $e ) {
            throw new Hoa_Controller_Exception_Reflection($e->getMessage(), $e->getCode());
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

    /**
     * Get directory to controller.
     *
     * @access  public
     * @return  string
     */
    public function getDirectory ( ) {

        return $this->_request->getParameter('directory');
    }

    /**
     * Get controller class name.
     *
     * @access  public
     * @return  string
     */
    public function getControllerClass ( ) {

        return $this->_request->getParameter('controller.class');
    }

    /**
     * Get controller file name.
     *
     * @access  public
     * @return  string
     */
    public function getControllerFile ( ) {

        return $this->_request->getParameter('controller.file');
    }

    /**
     * Get controller directory.
     *
     * @access  public
     * @return  string
     */
    public function getControllerDirectory ( ) {

        return $this->_request->getParameter('controller.directory');
    }

    /**
     * Get action class.
     *
     * @access  public
     * @return  string
     */
    public function getActionClass ( ) {

        return $this->_request->getParameter('action.class');
    }

    /**
     * Get action method.
     *
     * @access  public
     * @return  string
     */
    public function getActionMethod ( ) {

        return $this->_request->getParameter('action.method');
    }

    /**
     * Get action file.
     *
     * @access  public
     * @return  string
     */
    public function getActionFile ( ) {

        return $this->_request->getParameter('action.file');
    }

    /**
     * Load file.
     *
     * @access  private
     * @param   string   $file    File to load.
     * @return  void
     * @throw   Hoa_Controller_Exception_ControllerIsNotFound
     */
    private function load ( $file = '' ) {

        if(!file_exists($file))
            throw new Hoa_Controller_Exception_ControllerIsNotFound(
                'File %s is not found.', 2, $file);

        require_once $file;
    }
}
