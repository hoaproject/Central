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
 * @package     Hoa_Test
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Exception
 */
import('Test.Exception');

/**
 * Hoa_Test_Request
 */
import('Test.Request');

/**
 * Hoa_Test_Oracle
 */
import('Test.Oracle.~');

/**
 * Class Hoa_Test.
 *
 *
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 */

class Hoa_Test {

    /**
     * Singleton.
     *
     * @var Hoa_Test object
     */
    private static $_instance      = null;

    /**
     * The request object.
     *
     * @var Hoa_Test_Request object
     */
    protected $_request            = null;

    /**
     * The Hoa_Test parameters.
     *
     * @var Hoa_Test array
     */
    protected $parameters          = array(
        'convict.directory'        => null,
        'convict.recursive'        => true,
        'convict.result'           => array(),

        'test.incubator'           => 'Data/Test/Incubator/',
        'test.ordeal.oracle'       => 'Data/Test/Ordeal/Oracle/',
        'test.ordeal.battleground' => 'Data/Test/Ordeal/Battleground/',
        'test.predicate.maxtry'    => 64,

        'user.type'                => null,

        'report'                   => 'Test/Report/'
    );



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     */
    private function __construct ( Array $parameters = array() ) {

        #IF_DEFINED HOA_STANDALONE
        if(empty($parameters))
            Hoa_Framework::configurePackage(
                'Test', $parameters, Hoa_Framework::CONFIGURATION_DOT);
        #END_IF

        $this->setParameters($parameters);
    }

    /**
     * Singleton : get instance of Hoa_Test.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public static function getInstance ( Array $parameters = array() ) {

        if(null === self::$_instance)
            self::$_instance = new self($parameters);

        return self::$_instance;
    }

    /**
     * Set parameters.
     *
     * @access  protected
     * @param   array      $parameters    Parameters.
     * @param   array      $recursive     Used for recursive parameters.
     * @return  array
     */
    protected function setParameters ( Array $parameters = array(),
                                             $recursive  = array() ) {

        if($recursive === array()) {
            $array       =& $this->parameters;
            $recursivity = false;
        }
        else {
            $array       =& $recursive;
            $recursivity = true;
        }

        if(empty($parameters))
            return $array;

        foreach($parameters as $option => $value) {

            if(empty($option) || (empty($value) && !is_bool($value)))
                continue;

            if(is_array($value))
                $array[$option] = $this->setParameters($value, $array[$option]);

            else
                $array[$option] = $value;
        }

        return $array;
    }

    /**
     * Run test.
     *
     * @access  public
     * @return  void
     */
    public function run ( ) {

        $this->setRequest();

        $oracle = new Hoa_Test_Oracle();
        $oracle->setRequest($this->getRequest());
        $oracle->predict();
    }

    /**
     * Get all parameters.
     *
     * @access  protected
     * @return  array
     */
    protected function getParameters ( ) {

        return $this->parameters;
    }

    /**
     * Get a specific parameter.
     *
     * @access  protected
     * @param   string     $parameter    The parameter name.
     * @return  mixed
     * @throw   Hoa_Test_Exception
     */
    protected function getParameter ( $parameter ) {

        if(!isset($this->parameters[$parameter]))
            throw new Hoa_Test(
                'The parameter %s does not exists.', 0, $parameter);

        return $this->parameters[$parameter];
    }

    /**
     * Set the request object, with parameters.
     *
     * @access  protected
     * @return  Hoa_Test_Request
     */
    protected function setRequest ( ) {

        $old            = $this->_request;
        $this->_request = new Hoa_Test_Request(
                              $this->getParameters()
                          );

        return $old;
    }

    /**
     * Get the request object.
     *
     * @access  protected
     * @return  Hoa_Test_Request
     */
    protected function getRequest ( ) {

        return $this->_request;
    }
}
