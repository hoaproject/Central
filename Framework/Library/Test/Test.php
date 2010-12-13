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
 * @package     Hoa_Test
 *
 */

/**
 * Hoa_Test_Exception
 */
import('Test.Exception');

/**
 * Hoa_Test_Orchestrate
 */
import('Test.Orchestrate');

/**
 * Hoa_Test_Praspel
 */
import('Test.Praspel.~');

/**
 * Class Hoa_Test.
 *
 * Make tests.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 */

class Hoa_Test implements Hoa_Core_Parameterizable {

    /**
     * Singleton.
     *
     * @var Hoa_Test object
     */
    private static $_instance = null;

    /**
     * Parameters of Hoa_Test.
     *
     * @var Hoa_Core_Parameter object
     */
    protected $_parameters    = null;



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Core_Parameter(
            $this,
            array(),
            array(
                'convict'      => null,

                'root'         => 'hoa://Data/Variable/Test/',

                'repository'   => '(:%root:)Repository/',
                'revision'     => '(:_YmdHis:)/',

                'incubator'    => '(:%repository:)(:%revision:)Incubator/',
                'instrumented' => '(:%repository:)(:%revision:)Instrumented/',
                'sampler'      => '(:%repository:)(:%revision:)Sampler/',

                'dictionary'   => '(:%root:)Dictionary/',
                'maxtry'       => 64
            )
        );

        $this->setParameters($parameters);
    }

    /**
     * For a temporary retro-compatibility. Will be deleted.
     */
    public static function getInstance ( ) {

        return new self();
    }

    /**
     * Initialize tests, i.e. create a new revision in the repository of test:
     * incubator + instrumented.
     *
     * @access  public
     * @param   string  $directory    Directory of the SUT (System Under Test).
     * @return  void
     */
    public function initialize ( $directory ) {

        $this->setParameter('convict', $directory);
        $orchestrate = new Hoa_Test_Orchestrate($this->_parameters);
        $this->_parameters->shareWith(
            $this,
            $orchestrate,
            Hoa_Core_Parameter::PERMISSION_READ
        );
        $orchestrate->compute();

        return;
    }

    /**
     * Use sampler to call a method.
     *
     * @access  public
     * @param   string  $contractId    Contract ID.
     * @param   string  $class         Class to call.
     * @param   string  $method        Method to call.
     * @return  void
     */
    public function sample ( $contractId, $class, $method ) {

        if(!class_exists($class))
            throw new Hoa_Test_Exception(
                'Class %s does not exist and cannot be tested.', 0, $class);

        $cut        = new $class();
        $hop        = '__hoa_' . $method . '_contract';
        $cut->$hop();
        $praspel    = Hoa_Test_Praspel::getInstance();
        $contract   = $praspel->getContract($contractId);
        $requires   = $contract->getClause('requires');
        $parameters = array(0 => $method);
        $handle     = null;

        foreach($requires->getVariables() as $variable) {

            $handle = $variable->getChoosenDomain();
            $handle->clear()->randomize();

            $parameters[] = $handle->getValue();
        }

        call_user_func_array(
            array($cut, '__hoa_magicCaller'),
            $parameters
        );

        return;
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
}
