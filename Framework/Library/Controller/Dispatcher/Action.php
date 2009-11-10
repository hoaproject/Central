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
 * @subpackage  Hoa_Controller_Dispatcher_Action
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Controller_Hoa_Controller_AbstractInterface.
 *
 * Dispatch part for action (from Hoa_Action_Standard).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Controller
 * @subpackage  Hoa_Controller_Dispacher_Action
 */

class          Hoa_Controller_Dispatcher_Action
    implements Hoa_Framework_Parameterizable_Readable {

    /**
     * Parameters of the current controller.
     *
     * @var Hoa_Framework_Parameter object
     */
    private $_parameters = null;

    /**
     * Dispatcher.
     *
     * @var Hoa_Controller_Dispatcher_Abstract object
     */
    private $_dispatcher = null;

    /**
     * Response.
     *
     * @var Hoa_Controller_Response_Standard object
     */
    protected $response  = null;

    /**
     * View.
     *
     * @var Hoa_View object
     */
    protected $view      = null;



    /**
     * Set objects.
     *
     * @access  public
     * @param   Hoa_Framewor_Parameter              $parameters    Parameters.
     * @param   Hoa_Controller_Dispatcher_Abstract  $dispatcher    Dispatcher.
     * @param   Hoa_Controller_Response_Standard    $response      Response.
     * @param   Hoa_View                            $view          View.
     * @return  void
     */
    public function __construct ( Hoa_Framework_Parameter            $parameters,
                                  Hoa_Controller_Dispatcher_Abstract $dispatcher,
                                  Hoa_Controller_Response_Standard   $response,
                                  Hoa_View                           $view ) {

        $this->_parameters = $parameters;
        $this->_dispatcher = $dispatcher;
        $this->response    = $response;
        $this->view        = $view;
    }

    /**
     * Get many parameters.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Get a parameter.
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
     * Get a formatted parameter.
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
     * Know if the controller is called automatically (e.g. from an URL) or
     * manually (e.g. with the data.personal.array).
     *
     * @access  public
     * @return  bool
     */
    public function isCalledAutomatically ( ) {

        return null === $this->getParameter('data.array.personal');
    }
}
