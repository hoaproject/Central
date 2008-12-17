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
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Request
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Console_Request.
 *
 * This class stocks the Hoa_Console parameters.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Request
 */

class Hoa_Console_Request {

    /**
     * Parameters.
     *
     * @var Hoa_Console_Request string
     */
    protected $_parameters = array();



    /**
     * Set default parameters.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->setParameters($parameters);
    }

    /**
     * Set parameters.
     *
     * @access  public
     * @param   array    $parameters    Parameters.
     * @return  string
     */
    public function setParameters ( Array $parameters = array() ) {

        return $this->_parameters = $parameters;
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @return  string
     */
    public function getParameters ( ) {

        return $this->_parameters;
    }

    /**
     * Set one parameter.
     *
     * @access  public
     * @param   string  $parameter    One parameter.
     * @param   string  $value        Parameter value.
     * @param   bool    $force        Force to create.
     * @return  mixed
     */
    public function setParameter ( $parameter = '', $value = '', $force = true ) {

        if($force === false)
            if(!isset($this->_parameters[$parameter]))
                return false;

        $old                           = isset($this->_parameters[$parameter])
                                         ? $this->_parameters[$parameter] : null;
        $this->_parameters[$parameter] = $value;

        return $old;
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @param   string  $parameter    Parameter.
     * @return  string
     */
    public function getParameter ( $parameter = '' ) {

        return isset($this->_parameters[$parameter])
                 ? $this->_parameters[$parameter]
                 : null;
    }
}
