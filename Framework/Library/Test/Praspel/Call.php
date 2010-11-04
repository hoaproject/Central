<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * @subpackage  Hoa_Test_Praspel_Call
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Class Hoa_Test_Praspel_Call.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Call
 */

class Hoa_Test_Praspel_Call {

    /**
     * Root of Praspel's object model.
     *
     * @var Hoa_Test_Praspel object
     */
    protected $_root        = null;

    /**
     * Convict.
     *
     * @var mixed object
     */
    protected $_convict     = null;

    /**
     * Magic caller name.
     *
     * @var Hoa_Test_Praspel_Call string
     */
    protected $_magicCaller = null;

    /**
     * Class name.
     *
     * @var Hoa_Test_Praspel_Call string
     */
    protected $_class       = null;

    /**
     * Method name.
     *
     * @var Hoa_Test_Praspel_Call string
     */
    protected $_method      = null;

    /**
     * Method arguments value.
     *
     * @var Hoa_Test_Praspel_Call array
     */
    protected $_values      = array();

    /**
     * Call result.
     *
     * @var Hoa_Test_Praspel_Call mixed
     */
    protected $_result      = null;

    /**
     * Call exception (if thrown).
     *
     * @var Exception object
     */
    protected $_exception   = null;



    /**
     * Prepare and run a call.
     *
     * @access  public
     * @param   Hoa_Test_Praspel  $root           Root of this object model.
     * @param   object            &$convict       Convict.
     * @param   string            $magicCaller    Magic caller name.
     * @param   string            $class          Class name.
     * @param   string            $method         Method name.
     * @return  void
     */
    public function __construct ( Hoa_Test_Praspel $root, &$convict,
                                  $magicCaller, $class, $method ) {

        $this->setRoot($root);
        $this->setConvict($convict);
        $this->setMagicCaller($magicCaller);
        $this->setClass($class);
        $this->setMethod($method);
        $this->call();
    }

    /**
     * Call the method.
     *
     * @access  protected
     * @return  void
     * @throws  Hoa_Test_Praspel_Exception
     */
    protected function call ( ) {

        $freeVariables = $this->getRoot()
                              ->getClause('requires')
                              ->getVariables();
        $values        = array($this->getMethod());

        foreach($freeVariables as $i => $freeVariable) {

            $freeVariable->chooseOneDomain()->clear()->randomize();
            $values[] = $freeVariable->getChoosenDomain()->getValue();
        }

        $this->setValues($values);

        $convict = &$this->getConvict();

        if(null === $convict) {

            $reflection = new ReflectionClass($this->getClass());
            $convict    = $reflection->newInstance();
            $this->setConvict($convict);
        }

        try {

            ob_start();
            ob_implicit_flush(false);
            $obLevel = ob_get_level();

            $this->_result = call_user_func_array(
                array($convict, $this->getMagicCaller()),
                $values
            );

            while(ob_get_level() >= $obLevel)
                ob_end_clean();
        }
        catch ( Exception $e ) {

            $this->_exception = $e;
        }

        return;
    }

    /**
     * Set the root of Praspel's object model.
     *
     * @access  protected
     * @param   Hoa_Test_Praspel  $root    Root of this object model.
     * @return  Hoa_Test_Praspel
     */
    protected function setRoot ( Hoa_Test_Praspel $root ) {

        $old         = $this->_root;
        $this->_root = $root;

        return $old;
    }

    /**
     * Set the convict.
     *
     * @access  protected
     * @param   object     $convict    Convict.
     * @return  object
     */
    protected function setConvict ( &$convict ) {

        $old            = $this->_convict;
        $this->_convict = &$convict;

        return $old;
    }

    /**
     * Set the magic caller.
     *
     * @access  protected
     * @param   string     $magicCaller    Magic caller name.
     * @return  string
     */
    public function setMagicCaller ( $magicCaller ) {

        $old                = $this->_magicCaller;
        $this->_magicCaller = $magicCaller;

        return $old;
    }

    /**
     * Set the class name.
     *
     * @access  protected
     * @param   string     $class    Class name.
     * @return  string
     */
    public function setClass ( $class ) {

        $old          = $this->_class;
        $this->_class = $class;

        return $old;
    }

    /**
     * Set the method name.
     *
     * @access  protected
     * @param   string     $method    Method name.
     * @return  string
     */
    public function setMethod ( $method ) {

        $old           = $this->_method;
        $this->_method = $method;

        return $old;
    }

    /**
     * Set the method arguments values.
     *
     * @access  protected
     * @param   array     $values    Values.
     * @return  array
     */
    public function setValues ( $values ) {

        array_shift($values);

        $old           = $this->_values;
        $this->_values = $values;

        return $old;
    }

    /**
     * Get the root.
     *
     * @access  protected
     * @return  Hoa_Test_Praspel
     */
    protected function getRoot ( ) {

        return $this->_root;
    }

    /**
     * Get the convict.
     *
     * @access  public
     * @return  object
     */
    public function &getConvict ( ) {

        return $this->_convict;
    }

    /**
     * Get the magic caller name.
     *
     * @access  protected
     * @return  string
     */
    protected function getMagicCaller ( ) {

        return $this->_magicCaller;
    }

    /**
     * Get the class name.
     *
     * @access  protected
     * @return  string
     */
    protected function getClass ( ) {

        return $this->_class;
    }

    /**
     * Get the method name.
     *
     * @access  protected
     * @return  string
     */
    protected function getMethod ( ) {

        return $this->_method;
    }

    /**
     * Get the method arguments values.
     *
     * @access  protected
     * @return  array
     */
    public function getValues ( ) {

        return $this->_values;
    }

    /**
     * Get result of the call.
     *
     * @access  public
     * @return  mixed
     */
    public function getResult ( ) {

        return $this->_result;
    }

    /**
     * Get exception from the call.
     *
     * @access  public
     * @return  Exception
     */
    public function getException ( ) {

        return $this->_exception;
    }

    /**
     * Check if the call throws an exception.
     *
     * @access  public
     * @return  bool
     */
    public function hasException ( ) {

        return null !== $this->getException();
    }
}
