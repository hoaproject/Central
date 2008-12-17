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
 * @subpackage  Hoa_Controller_Router_Standard
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Router_Pattern
 */
import('Controller.Router.Pattern');

/**
 * Hoa_Controller_Router_Interface
 */
import('Controller.Router.Interface');

/**
 * Hoa_Controller_Exception_RouterFactory
 */
import('Controller.Exception.RouterFactory');

/**
 * Hoa_Controller_Exception_RouterDoesNotReturnAnArray
 */
import('Controller.Exception.RouterDoesNotReturnAnArray');

/**
 * Hoa_Factory
 */
import('Factory.~');

/**
 * Class Hoa_Controller_Router_Standard.
 *
 * Front controller router defines paths, directories, and filenames to front
 * controller components.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Standard
 */

class Hoa_Controller_Router_Standard extends    Hoa_Controller_Router_Pattern
                                     implements Hoa_Controller_Router_Interface {

    /**
     * Request.
     *
     * @var Hoa_Controller_Router_Standard object
     */
    protected $_request = null;

    /**
     * Data array.
     *
     * @var Hoa_Controller_Router_Standard array
     */
    protected $dataArray = null;

    /**
     * Directory.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $directory = null;

    /**
     * Controller value.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $controllerValue = null;

    /**
     * Controller class.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $controllerClass = null;

    /**
     * Controller file.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $controllerFile = null;

    /**
     * Controller directory.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $controllerDirectory = null;

    /**
     * Action value.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $actionValue = null;

    /**
     * Action class.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $actionClass = null;

    /**
     * Action method.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $actionMethod = null;

    /**
     * Action file.
     *
     * @var Hoa_Controller_Router_Standard string
     */
    protected $actionFile = null;



    /**
     * Set request to Hoa_Controller_Router_Standard.
     *
     * @access  public
     * @param   object  $request    Hoa_Controller_Request_Abstract
     * @return  object
     */
    public function setRequest ( Hoa_Controller_Request_Abstract $request ) {

        $this->resetObject();
        $this->_request  = $request;

        return $this;
    }

    /**
     * Start router.
     *
     * @access  public
     * @param   array   $parameters    Just to be compatible with the interface.
     * @return  void
     */
    public function route ( Array $parameters = array() ) {

        $this->_request->setParameter(
            'data.array'          , $this->getDataArray());

        $this->_request->setParameter(
            'directory'           , $this->getDirectory());

        $this->_request->setParameter(
            'controller.value'    , $this->getControllerValue());

        $this->_request->setParameter(
            'controller.directory', $this->getControllerDirectory());

        $this->_request->setParameter(
            'controller.class'    , $this->getControllerClass());

        $this->_request->setParameter(
            'controller.file'     , $this->getControllerFile());

        $this->_request->setParameter(
            'action.value'        , $this->getActionValue());

        $this->_request->setParameter(
            'action.class'        , $this->getActionClass());

        $this->_request->setParameter(
            'action.method'       , $this->getActionMethod());
    
        $this->_request->setParameter(
            'action.file'         , $this->getActionFile());
    }

    /**
     * Set data array.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Controller_Exception_RouterFactory
     * @throw   Hoa_Controller_Exception_RouterDoesNotReturnAnArray
     * @throw   Hoa_Controller_Exception
     */
    public function setDataArray ( ) {

        $return      = null;
        $routerType  = $this->_request->getParameter('route.type');
        $routerParam = array($this->_request->getParameter('route.parameter'));
        $personal    = $this->_request->getParameter('data.array.personal');

        if(null === $personal) {

            try {

                $return = Hoa_Factory::get('Controller.Router',
                                           $routerType, $routerParam, 'route',
                                           'Hoa_Controller_Router_Interface');
            }
            catch ( Hoa_Factory_Exception $e ) {

                throw new Hoa_Controller_Exception_RouterFactory(
                    $e->getMessage(), $e->getCode());
            }
        }
        else
            $return = $personal;

        if(!is_array($return))
            throw new Hoa_Controller_Exception_RouterDoesNotReturnAnArray(
                'Router %s does not return an array.', 0, $routerType);

        $old             = $this->dataArray;
        $this->dataArray = $return;

        return $old;
    }

    /**
     * Get data array.
     *
     * @access  public
     * @return  void
     */
    public function getDataArray ( ) {

        if($this->dataArray === null)
            $this->setDataArray();

        return $this->dataArray;
    }

    /**
     * Set controller value.
     *
     * @access  public
     * @return  string
     */
    public function setControllerValue ( ) {

        $key   = $this->_request->getParameter('route.controller.key');
        $value = $this->getDataArray();

        if(isset($value[$key]))
            $value = $value[$key];
        else
            $value = $this->_request->getParameter('route.controller.default');

        $value                 = preg_replace('#[^a-z0-9\._-]#i', '', $value);
        $old                   = $this->controllerValue;
        $this->controllerValue = $value;

        return $old;
    }

    /**
     * Get controller value.
     *
     * @access  public
     * @return  string
     */
    public function getControllerValue ( ) {

        if($this->controllerValue === null)
            $this->setControllerValue();

        return $this->controllerValue;
    }

    /**
     * Set controller class.
     *
     * @access  public
     * @return  string
     */
    public function setControllerClass ( ) {

        $pattern    = $this->_request->getParameter('pattern.controller.class');
        $controller = $this->getControllerValue();
        $class      = $this->transform($pattern, $controller);

        $old                   = $this->controllerClass;
        $this->controllerClass = $class;

        return $old;
    }

    /**
     * Get controller class.
     *
     * @access  public
     * @return  string
     */
    public function getControllerClass ( ) {

        if($this->controllerClass === null)
            $this->setControllerClass();

        return $this->controllerClass;
    }

    /**
     * Set controller file.
     *
     * @access  public
     * @return  string
     */
    public function setControllerFile ( ) {

        $pattern    = $this->_request->getParameter('pattern.controller.file');
        $controller = $this->getControllerValue();
        $file       = $this->transform($pattern, $controller);

        $old                  = $this->controllerFile;
        $this->controllerFile = $file;

        return $old;
    }

    /**
     * Get controller file.
     *
     * @access  public
     * @return  string
     */
    public function getControllerFile ( ) {

        if($this->controllerFile === null)
            $this->setControllerFile();

        return $this->controllerFile;
    }

    /**
     * Set controller directory.
     *
     * @access  public
     * @return  string
     */
    public function setControllerDirectory ( ) {

        $pattern    = $this->_request->getParameter('pattern.controller.directory');
        $controller = $this->getControllerValue();
        $directory  = $this->transform($pattern, $controller);

        $old                       = $this->controllerDirectory;
        $this->controllerDirectory = $directory;

        return $old;
    }

    /**
     * Get controller directory.
     *
     * @access  public
     * @return  string
     */
    public function getControllerDirectory ( ) {

        if($this->controllerDirectory === null)
            $this->setControllerDirectory();

        return $this->controllerDirectory;
    }

    /**
     * Set action value.
     *
     * @access  public
     * @return  string
     */
    public function setActionValue ( ) {

        $key   = $this->_request->getParameter('route.action.key');
        $value = $this->getDataArray();

        if(isset($value[$key]))
            $value = $value[$key];
        else
            $value = $this->_request->getParameter('route.action.default');

        $value             = preg_replace('#[^a-z0-9\._-]#i', '', $value);
        $old               = $this->actionValue;
        $this->actionValue = $value;

        return $old;
    }

    /**
     * Get action value.
     *
     * @access  public
     * @return  string
     */
    public function getActionValue ( ) {

        if($this->actionValue === null)
            $this->setActionValue();

        return $this->actionValue;
    }

    /**
     * Set action class.
     *
     * @access  public
     * @return  string
     */
    public function setActionClass ( ) {

        $pattern    = $this->_request->getParameter('pattern.action.class');
        $controller = $this->getActionValue();
        $class      = $this->transform($pattern, $controller);

        $old                = $this->actionClass;
        $this->actionClass = $class;

        return $old;
    }

    /**
     * Get action class.
     *
     * @access  public
     * @return  string
     */
    public function getActionClass ( ) {

        if($this->actionClass === null)
            $this->setActionClass();

        return $this->actionClass;
    }

    /**
     * Set action method.
     *
     * @access  public
     * @return  string
     */
    public function setActionMethod ( ) {

        $pattern    = $this->_request->getParameter('pattern.action.method');
        $controller = $this->getActionValue();
        $method     = $this->transform($pattern, $controller);

        $old                = $this->actionMethod;
        $this->actionMethod = $method;

        return $old;
    }

    /**
     * Get action method.
     *
     * @access  public
     * @return  string
     */
    public function getActionMethod ( ) {

        if($this->actionMethod === null)
            $this->setActionMethod();

        return $this->actionMethod;
    }

    /**
     * Set action file.
     *
     * @access  public
     * @return  string
     */
    public function setActionFile ( ) {

        $pattern    = $this->_request->getParameter('pattern.action.file');
        $controller = $this->getActionValue();
        $file       = $this->transform($pattern, $controller);

        $old              = $this->actionFile;
        $this->actionFile = $file;

        return $old;
    }

    /**
     * Get action file.
     *
     * @access  public
     * @return  string
     */
    public function getActionFile ( ) {

        if($this->actionFile === null)
            $this->setActionFile();

        return $this->actionFile;
    }

    /**
     * Set directory.
     *
     * @access  public
     * @return  string
     */
    public function setDirectory ( ) {

        $pattern     = $this->_request->getParameter('route.directory');
        $replacement = $this->getControllerValue();
        $directory   = $this->transform($pattern, $replacement);

        $old             = $this->directory;
        $this->directory = $directory;

        return $old;
    }

    /**
     * Get directory.
     *
     * @access  public
     * @return  void
     */
    public function getDirectory ( ) {

        if($this->directory === null)
            $this->setDirectory();

        return $this->directory;
    }

    /**
     * Reset object.
     *
     * @access  public
     * @return  void
     */
    public function resetObject ( ) {

        foreach(get_class_vars(get_class($this)) as $key => $value)
            $this->$key = null;
    }
}
