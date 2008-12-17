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
 * @package     Hoa_View
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_View_Exception
 */
import('View.Exception');

/**
 * Hoa_View_Helper_Abstract
 */
import('View.Helper.Abstract');

/**
 * Class Hoa_View.
 *
 * Manage view.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_View
 */

class Hoa_View {

    /**
     * View directory (layout + view).
     *
     * @var Hoa_View string
     */
    protected $directory = null;

    /**
     * Layout filename.
     *
     * @var Hoa_View string
     */
    protected $layout = null;

    /**
     * Has layout.
     *
     * @var Hoa_View bool
     */
    protected $hasLayout = false;

    /**
     * Layout content.
     *
     * @var Hoa_View string
     */
    protected $layoutContent = null;

    /**
     * View filename
     *
     * @var Hoa_View string
     */
    protected $view = null;

    /**
     * View content.
     *
     * @var Hoa_View string
     */
    protected $viewContent = null;

    /**
     * View helper directory (for application, not framework).
     *
     * @var Hoa_View string
     */
    protected $helperDirectory = null;

    /**
     * View variables.
     *
     * @var Hoa_View array
     */
    private $_properties = array();



    /**
     * __construct
     * Set parameters.
     *
     * @access  public
     * @param   directory  string    Directory.
     * @return  void
     */
    public function __construct ( $directory = null ) {

        if(null !== $directory)
            $this->setDirectory($directory);
    }

    /**
     * setDirectory
     * Set directory.
     *
     * @access  public
     * @param   directory  string    Directory.
     * @return  string
     * @throw   Hoa_View_Exception
     */
    public function setDirectory ( $directory = '' ) {

        if(!is_dir($directory))
            throw new Hoa_View_Exception('%s is not a directory.', 0, $directory);

        $old             = $this->directory;
        $this->directory = $directory;

        return $old;
    }

    /**
     * setLayout
     * Set layout.
     *
     * @access  public
     * @param   layout  string    Layout.
     * @param   enable  string    Enable layout.
     * @return  string
     */
    public function setLayout ( $layout = '', $enable = true ) {

        if(!file_exists($this->directory . $layout))
            throw new Hoa_View_Exception('Layout view file %s is not found.',
                1, $this->directory . $layout);

        $old          = $this->layout;
        $this->layout = $layout;

        $this->enableLayout($enable);

        return $old;
    }

    /**
     * enableLayout
     * Enable layout file.
     *
     * @access  public
     * @param   enable  bool    Enable layout.
     * @return  bool
     */
    public function enableLayout ( $enable = true ) {

        $old             = $this->hasLayout;
        $this->hasLayout = $enable;

        return $old;
    }

    /**
     * hasLayout
     * Has layout.
     *
     * @access  public
     * @return  bool
     */
    public function hasLayout ( ) {

        return $this->hasLayout;
    }

    /**
     * setView
     * Set view.
     *
     * @access  public
     * @param   view    string    View.
     * @return  string
     */
    public function setView ( $view = '' ) {

        /*if(!file_exists($this->directory . $view))
            throw new Hoa_View_Exception('View file %s is not found.',
                2, $this->directory . $view);*/

        $old        = $this->view;
        $this->view = $view;

        return $old;
    }

    /**
     * setHelperDirectory
     * Set helper directory.
     *
     * @access  public
     * @param   directory  string    Directory.
     * @return  string
     */
    public function setHelperDirectory ( $directory = '' ) {

        $old                   = $this->helperDirectory;
        $this->helperDirectory = $directory;

        return $old;
    }

    /**
     * __set
     * Overloading property.
     *
     * @access  public
     * @param   name    string    Name.
     * @param   value   string    Value.
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
     * __get
     * Overloading property.
     *
     * @access  public
     * @param   name    string    Name.
     * @return  mixed
     */
    public function __get ( $name ) {

        if(!isset($this->_properties[$name]))
            return null;

        $cast = gettype($this->_properties[$name]);

        switch($cast) {

            case 'array':
                return (array) $this->_properties[$name];
              break;

            default:
                return $this->_properties[$name];
        }
    }

    /**
     * __isset
     * Overloading property.
     *
     * @access  public
     * @param   name    string    Name.
     * @return  bool
     */
    public function __isset ( $name ) {

        return isset($this->_properties[$name]);
    }

    /**
     * __unset
     * Overloading property.
     *
     * @access  public
     * @param   name    string    Name.
     * @return  void
     */
    public function __unset ( $name ) {

        unset($this->_properties[$name]);
    }

    /**
     * render
     * Make a render.
     *
     * @access  public
     * @param   specific      string    Specific file.
     * @param   enableLayout  bool      Enable layout.
     * @return  string
     */
    public function render ( $specific = null, $enableLayout = false ) {

        $content = $this->__get('_page') . $this->viewRenderer($specific);
        $this->__set('_page', $content);

        if($enableLayout === false)
            $enableLayout = $specific === null;

        if(false !== $enableLayout && false !== $this->hasLayout())
            $content = $this->layoutRenderer();

        return $content;
    }

    /**
     * viewRenderer
     * View renderer.
     *
     * @access  public
     * @param   specific  string    Specific file.
     * @return  string
     */
    public function viewRenderer ( $specific = null ) {

        $view = $specific === null ? $this->view : $specific;

        if(!file_exists($this->directory . $view))
            throw new Hoa_View_Exception('View file %s is not found.',
                3, $this->directory . $view);

        ob_start();
        ob_implicit_flush(false);

        require $this->directory . $view;
        $this->viewContent = ob_get_clean();

        return $this->viewContent;
    }

    /**
     * layoutRenderer
     * Layout renderer.
     *
     * @access  public
     * @return  string
     */
    public function layoutRenderer ( ) {

        ob_start();
        ob_implicit_flush(false);

        require $this->directory . $this->layout;
        $this->layoutContent = ob_get_clean();

        return $this->layoutContent;
    }

    /**
     * __call
     * Overloading helpers.
     *
     * @access  public
     * @param   name       string    Name of called method, but not used.
     * @param   arguments  array     Arguments.
     * @return  void
     */
    public function __call ( $name, Array $argument ) {

        $className = $name . 'Helper';

        $app = $this->helperDirectory . $className . '.php';
        $fw  = dirname(__FILE__) . '/helper/' . $className . '.php';

        if(!file_exists($app))
            if(!file_exists($fw))
                throw new Hoa_View_Exception('Cannot find %s helper.', 4, $fw);
            else
                $path = $fw;
        else
            $path = $app;

        require_once $path;

        try {

            $reflection = new ReflectionClass($className);
            $object     = $reflection->newInstanceArgs($argument);
            $return     = $object->__toString();
        }
        catch ( ReflectionException $e ) {
            throw new Hoa_View_Exception($e->getMessage(), $e->getCode());
        }
        catch ( Hoa_View_Exception $e ) {
            throw $e;
        }

        return $return;
    }
}
