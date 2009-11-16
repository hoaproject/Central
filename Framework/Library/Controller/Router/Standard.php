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
 * @subpackage  Hoa_Controller_Router_Standard
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Controller_Exception_RouterDoesNotReturnAnArray
 */
import('Controller.Exception.RouterDoesNotReturnAnArray');

/**
 * Hoa_Controller_Exception_RouterDoesNotReturnAString
 */
import('Controller.Exception.RouterDoesNotReturnAString');

/**
 * Hoa_Controller_Router_Interface
 */
import('Controller.Router.Interface');

/**
 * Hoa_Controller_Router_Get
 */
import('Controller.Router.Get');

/**
 * Hoa_Controller_Router_Rewrite
 */
import('Controller.Router.Rewrite');

/**
 * Class Hoa_Controller_Router_Standard.
 *
 * Front controller router defines paths, directories, and filenames to front
 * controller components.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Router_Standard
 */

class Hoa_Controller_Router_Standard implements Hoa_Framework_Parameterizable {

    /**
     * Parameters from Hoa_Controller (i.e. a request).
     *
     * @var Hoa_Framework_Parameter object
     */
    private $_parameters = null;

    /**
     * Specific router.
     *
     * @var Hoa_Controller_Router_Interface object
     */
    protected $_router  = null;



    /**
     * Start router.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Controller_Exception_RouterDoesNotReturnAnArray
     */
    public function route ( ) {

        $array     = null;
        $personal  = $this->getParameter('data.array.personal');

        if(null === $personal)
            $array = $this->getRouter()->route(
                         $this->_parameters->unlinearizeBranche(
                             $this,
                             'route.parameter'
                         )
                     );
        else
            $array = $personal;

        if(!is_array($array))
            throw new Hoa_Controller_Exception_RouterDoesNotReturnAnArray(
                'Router %s does not return an array, given %s.', 0,
                array($this->getParameter('route.type'), gettype($array)));

        $this->setParameter('data.array', $array);

        if(isset($array['module']))
            $this->_parameters->setKeyword($this, 'controller', $array['module']);

        if(isset($array['action']))
            $this->_parameters->setKeyword($this, 'action', $array['action']);

        return $array;
    }

    /**
     * Build a path.
     *
     * @access  public
     * @param   array   $values    Values of path.
     * @param   string  $rule      Specific rule name.
     * @return  string
     * @throw   Hoa_Controller_Exception_RouterDoesNotReturnAString
     */
    public function build ( Array $values = array(), $rule = null ) {

        $return = $this->getRouter()->build(
                      $this->_parameters->unlinearizeBranche(
                          $this,
                          'route.parameter'
                      ),
                      $this->_dataArray,
                      $values,
                      $rule
                  );

        if(!is_string($return))
            throw new Hoa_Controller_Exception_RouterDoesNotReturnAString(
                'Router %s does not return a string.', 1,
                $this->getParameter('route.type'));

        return $return;
    }

    /**
     * Set the current parameter (i.e. the current request).
     *
     * @access  public
     * @param   Hoa_Framework_Parameter  $parameters    Parameters.
     * @return  Hoa_Controller_Router_Standard
     */
    public function setRequest ( Hoa_Framework_Parameter $parameters ) {

        $this->_parameters = $parameters;
        $this->_router     = null;

        return $this;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
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
     * @param   string  $key      Key.
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
     * Set specific router.
     *
     * @access  protected
     * @return  Hoa_Controller_Router_Interface
     */
    protected function setRouter ( ) {

        $old           = $this->_router;
        $router        = 'Hoa_Controller_Router_' .
                         $this->getParameter('route.type');
        $this->_router = new $router();

        return $old;
    }

    /**
     * Get the specific router.
     *
     * @access  public
     * @return  Hoa_Controller_Router_Interface
     */
    public function getRouter ( ) {

        if(null === $this->_router)
            $this->setRouter();

        return $this->_router;
    }
}
