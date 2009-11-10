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
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Action_Standard
 */

class Hoa_Controller_Action_Standard extends Hoa_Controller_Dispatcher_Action {

    /**
     * Magics variables (but used for attached objects).
     *
     * @var Hoa_Controller_Action_Standard array
     */
    private $_properties = array();



    /**
     * Prepare dispatch with calling parent constructor.
     * Prepare autoload model, and set view parameters.
     *
     * @access  public
     * @param   Hoa_Framework_Parameter             $parameter          Parameter.
     * @param   Hoa_Controller_Dispatcher_Abstract  $dispatcher         Dispatcher.
     * @param   Hoa_Controller_Response_Standard    $response           Response.
     * @param   Hoa_View                            $view               View.
     * @param   array                               $attachedObjects    Attached
     *                                                                  objects.
     * @return  void
     */
    public function __construct ( Hoa_Framework_Parameter            $parameters,
                                  Hoa_Controller_Dispatcher_Abstract $dispatcher,
                                  Hoa_Controller_Response_Standard   $response,
                                  Hoa_View                           $view,
                                  Array                              $attachedObjects) {

        parent::__construct($parameters, $dispatcher, $response, $view);

        foreach($attachedObjects as $key => $value)
            $this->__set($key, $value);

        spl_autoload_register(array($this, 'autoloadModel'));
    }

    /**
     * Load model.
     *
     * @access  public
     * @param   string  $className    Class name.
     * @return  bool
     */
    public function autoloadModel ( $classname ) {

        $path = $this->getFormattedParameter('model.directory') .
                $classname . '.php';

        if(!file_exists($path)) {

            $path = $this->getFormattedParameter('model.share.directory') .
                    $classname . '.php';

            if(!file_exists($path))
                return false;
        }

        require_once $path;

        return true;
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
