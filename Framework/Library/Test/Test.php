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
 * Hoa_Test_Oracle
 */
import('Test.Oracle.~');

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
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 */

class Hoa_Test implements Hoa_Framework_Parameterizable {

    /**
     * Singleton.
     *
     * @var Hoa_Test object
     */
    private static $_instance = null;

    /**
     * Parameters of Hoa_Test.
     *
     * @var Hoa_Framework_Parameter object
     */
    protected $_parameters    = null;

    /**
     * Stream's log for the Praspel output.
     *
     * @var Hoa_Framework_Stream array
     */
    protected $_logs          = array();



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     */
    private function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Framework_Parameter(
            $this,
            array(),
            array(
                'convict.directory'        => null,
                'convict.recursive'        => true,
                'convict.result'           => array(),

                'current.root'             => 'hoa://Data/Variable/Test/(:_Ymds:)/',

                'test.incubator'           => '(:%current.root:)Incubator/',
                'test.ordeal.oracle'       => '(:%current.root:)Ordeal/Oracle/',
                'test.ordeal.battleground' => '(:%current.root:)Ordeal/Battleground/',
                'test.ordeal.methodPrefix' => '__hoa_',
                'test.dictionary'          => 'hoa://Data/Variable/Test/Dictionary/',
                'test.maxtry'              => 64,

                'user.type'                => 'hoa://Data/Variable/Test/UserType/',

                'report'                   => '(:%current.root:)Report/'
            )
        );

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
     * Run test.
     *
     * @access  public
     * @return  void
     */
    public function run ( ) {

        $oracle = new Hoa_Test_Oracle();
        $this->_parameters->shareWith(
            $this,
            $oracle,
            Hoa_Framework_Parameter::PERMISSION_READ  |
            Hoa_Framework_Parameter::PERMISSION_WRITE |
            Hoa_Framework_Parameter::PERMISSION_SHARE
        );
        $oracle->setRequest($this->_parameters);
        $oracle->predict();
    }

    /**
     *
     */
    public function addOutputStreams ( Array $streams ) {

        foreach($streams as $i => $stream)
            $this->addOutputStream($stream);

        return $this->getLogStreams();
    }

    /**
     *
     */
    public function addOutputStream ( Hoa_Stream $stream ) {

        if(null === $stream)
            return;

        if(!($stream instanceof Hoa_Stream_Io_Out))
            throw new Hoa_Test_Exception(
                'Stream log must implement the Hoa_Stream_Io_Out interface.', 0);

        if(false === $stream->isOpened())
            throw new Hoa_Test_Exception(
                'Stream log is not opened, maybe it failed.', 1);

        $this->_logs[$stream->__toString()] = $stream;

        return $this->getLogStreams();
    }

    /**
     *
     */
    public function getLogStreams ( ) {

        return $this->_logs;
    }
}
