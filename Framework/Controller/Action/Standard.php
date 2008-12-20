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
 * @subpackage  Hoa_Controller_Action_Standard
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
 * Class Hoa_Controller_Action_Standard.
 *
 * Each primary and secondary controller must extend this class.
 * This class allows primary and secondary controllers to manipulate model,
 * and to be linked with front controller and these components (such as
 * the dispatch).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Action_Standard
 */

class Hoa_Controller_Action_Standard extends Hoa_Controller_Dispatcher_Action {

    /**
     * View directory.
     *
     * @var Hoa_Controller_Action_Standard string
     */
    protected $viewDirectory = null;

    /**
     * View layout filename.
     *
     * @var Hoa_Controller_Action_Standard string
     */
    protected $viewLayout = null;

    /**
     * View enable layout.
     *
     * @var Hoa_Controller_Action_Standard string
     */
    protected $viewEnableLayout = null;

    /**
     * View filename.
     *
     * @var Hoa_Controller_Action_Standard string
     */
    protected $viewFile = null;

    /**
     * View helper directory.
     *
     * @var Hoa_Controller_Action_Standard string
     */
    protected $viewHelperDirectory = null;



    /**
     * Prepare dispatch with calling parent constructor.
     * Prepare autoload model, and set view parameters.
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

        parent::__construct($request, $dispatcher, $response, $view, $attachedObject);

        spl_autoload_register(array($this, 'autoloadModel'));

        $viewDirectory       = $this->getViewDirectory();
        $viewLayout          = $this->getViewLayout();
        $viewEnableLayout    = $this->getViewEnableLayout();
        $viewFile            = $this->getViewFile();
        $viewHelperDirectory = $this->getViewHelperDirectory();

        $this->view = $view;
        $this->view->setDirectory($viewDirectory);
        $this->view->setLayout($viewLayout);
        $this->view->enableLayout($viewEnableLayout);
        $this->view->setView($viewFile);
        $this->view->setHelperDirectory($viewHelperDirectory);
    }

    /**
     * Load model.
     *
     * @access  public
     * @param   string  $className    Class name.
     * @return  object
     */
    public function autoloadModel ( $classname ) {

        $routerPattern = new Hoa_Controller_Router_Pattern();
        $controller    = $this->requestGetParameter('controller.value');
        $pattern       = $this->requestGetParameter('pattern.model.directory');
        $pattern       = $routerPattern->transform($pattern, $controller);
        $directory     = $this->requestGetParameter('model.directory');
        $directory     = $routerPattern->transform($directory, $pattern);

        $path          = $directory . $classname . '.php';

        if(!file_exists($path))
            return false;

        require_once $path;
    }

    /**
     * Set view directory.
     *
     * @access  private
     * @return  string
     */
    private function setViewDirectory ( ) {

        $directory           = $this->requestGetParameter('view.directory');
        $theme               = $this->requestGetParameter('view.theme');
        $directory           = $this->_pattern->transform($directory, $theme);

        $old                 = $this->viewDirectory;
        $this->viewDirectory = $directory;

        return $old;
    }

    /**
     * Get view directory.
     *
     * @access  public
     * @return  string
     */
    public function getViewDirectory ( ) {

        if(null === $this->viewDirectory)
            $this->setViewDirectory();

        return $this->viewDirectory;
    }

    /**
     * Set view layout filename.
     *
     * @access  private
     * @return  string
     */
    private function setViewLayout ( ) {

        $pattern          = $this->requestGetParameter('pattern.view.layout');
        $layout           = $this->requestGetParameter('view.layout');
        $layout           = $this->_pattern->transform($pattern, $layout);

        $old              = $this->viewLayout;
        $this->viewLayout = $layout;

        return $old;
    }

    /**
     * Get view layout filename.
     *
     * @access  public
     * @return  string
     */
    public function getViewLayout ( ) {

        if(null === $this->viewLayout)
            $this->setViewLayout();

        return $this->viewLayout;
    }

    /**
     * Set view enable layout.
     *
     * @access  private
     * @return  string
     */
    private function setViewEnableLayout ( ) {

        $enableLayout           = $this->requestGetParameter('view.enable.layout');

        $old                    = $this->viewEnableLayout;
        $this->viewEnableLayout = $enableLayout;

        return $old;
    }

    /**
     * Get view enable layout.
     *
     * @access  public
     * @return  string
     */
    public function getViewEnableLayout ( ) {

        if(null === $this->viewEnableLayout)
            $this->setViewEnableLayout();

        return $this->viewEnableLayout;
    }

    /**
     * Set view filename.
     *
     * @access  private
     * @return  string
     */
    private function setViewFile ( ) {

        $pattern        = $this->requestGetParameter('pattern.view.directory');
        $directory      = $this->requestGetParameter('controller.value');
        $directory      = $this->_pattern->transform($pattern, $directory);

        $pattern        = $this->requestGetParameter('pattern.view.file');
        $file           = $this->requestGetParameter('action.value');
        $file           = $this->_pattern->transform($pattern, $file);

        $old            = $this->viewFile;
        $this->viewFile = $directory . $file;

        return $old;
    }

    /**
     * Get view filename.
     *
     * @access  public
     * @return  string
     */
    public function getViewFile ( ) {

        if(null === $this->viewFile)
            $this->setViewFile();

        return $this->viewFile;
    }

    /**
     * Set view helper directory.
     *
     * @access  public
     * @return  string
     */
    public function setViewHelperDirectory ( ) {

        $old                       = $this->viewHelperDirectory;
        $this->viewHelperDirectory = $this->requestGetParameter('view.helper.directory');

        return $old;
    }

    /**
     * Get view helper directory.
     *
     * @access  public
     * @return  string
     */
    public function getViewHelperDirectory ( ) {

        if(null === $this->viewHelperDirectory)
            $this->setViewHelperDirectory();

        return $this->viewHelperDirectory;
    }

    /**
     * Build a path.
     *
     * @access  public
     * @param   array   $values    Values of path.
     * @param   string  $rule      Specific rule name.
     * @return  string
     */
    public function buildPath ( Array $values = array(), $rule = null ) {

        return $this->view->buildPath($values, $rule);
    }
}
