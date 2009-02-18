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
 * @subpackage  Hoa_Test_Praspel_Call
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
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
    protected $_roots     = null;

    /**
     * Object to call.
     *
     * @var mixed object
     */
    protected $_object    = null;

    /**
     * Method to call.
     *
     * @var Hoa_Test_Praspel_Call string
     */
    protected $_method    = null;

    /**
     * Call result.
     *
     * @var Hoa_Test_Praspel_Call mixed
     */
    protected $_result    = null;

    /**
     * Call exception (if thrown).
     *
     * @var Exception object
     */
    protected $_exception = null;



    /**
     * Prepare and run a call.
     *
     * @access  public
     * @param   Hoa_Test_Praspel  $root         Root of this object model.
     * @param   object            $object       Object where method is.
     * @param   string            $method       Method name.
     * @return  void
     */
    public function __construct ( Hoa_Test_Praspel $root, $object, $method ) {

        $this->setRoot($root);
        $this->setObject($object);
        $this->setMethod($method);
        $this->call();
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
     * Set the object to call.
     *
     * @access  protected
     * @param   object     $object    Object where method is.
     * @return  object
     */
    protected function setObject ( $object ) {

        $old           = $this->_object;
        $this->_object = $object;

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
     * Call the method.
     *
     * @access  protected
     * @return  void
     */
    protected function call ( ) {

        $freeVariables = $this->getRoot()
                              ->getClause('requires')
                              ->getFreeVariables();
        $values        = array();

        foreach($freeVariables as $i => $freeVariable)
            $values[] = $freeVariable->chooseOneType()->getValue();

        try {

            $this->_result = call_user_func_array(
                array($this->getObject(), $this->getMethod()),
                $values
            );
        }
        catch ( Exception $e ) {

            $this->_exception = $e;
        }

        return;
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
     * Get the object.
     *
     * @access  protected
     * @return  object
     */
    protected function getObject ( ) {

        return $this->_object;
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
     * @return  string
     */
    public function getException ( ) {

        return $this->_exception;
    }
}
