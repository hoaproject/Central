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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Variable
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
 * Hoa_Test_Praspel_Type
 */
import('Test.Praspel.Type');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Class Hoa_Test_Praspel_Variable.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Variable
 */

class Hoa_Test_Praspel_Variable {

    /**
     * Parent (here: clause).
     *
     * @var Hoa_Test_Praspel_Clause object
     */
    protected $_parent  = null;

    /**
     * Variable name.
     *
     * @var Hoa_Test_Praspel_Variable string
     */
    protected $_name    = null;

    /**
     * Collection of types.
     *
     * @var Hoa_Test_Praspel_Variable array
     */
    protected $_types   = array();

    /**
     * Choosen type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type object
     */
    protected $_choosen = null;

    /**
     * Old value.
     *
     * @var Hoa_Test_Praspel_Variable mixed
     */
    private $_oldValue  = null;

    /**
     * New value.
     *
     * @var Hoa_Test_Praspel_Variable mixed
     */
    private $_newValue  = null;

    /**
     * Make a disjunction between two variables.
     *
     * @var Hoa_Test_Praspel_Variable object
     */
    public $_or         = null;

    /**
     * Make a conjunction between two variables.
     *
     * @var Hoa_Test_Praspel_Clause object
     */
    public $_and        = null;



    /**
     * Set the variable name.
     *
     * @access  public
     * @param   Hoa_Test_Praspel_Clause  $parent    Parent (here: the clause).
     * @param   string                   $name      Variable name.
     * @return  void
     */
    public function __construct ( Hoa_Test_Praspel_Clause $parent, $name ) {

        $this->setParent($parent);
        $this->setName($name);
        $this->_or  = $this;
        $this->_and = $this->getParent();

        return;
    }

    /**
     * Type the variable.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @param   ...     ...      Type arguments.
     * @return  Hoa_Test_Praspel_Variable
     */
    public function isTypedAs ( $name ) {

        /*
        if(true === $this->isTypeDeclared($name))
            return $this->_types[$name];
        */

        $arguments = func_get_args();
        array_shift($arguments);
        $type      = new Hoa_Test_Praspel_Type(
            $this->getParent()->getParent(),
            $name,
            $arguments
        );

        $this->_types[] = $type->getType();

        return $this;
    }

    /**
     * Check if the variable has a specific declared type.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @return  bool
     */
    public function isTypeDeclared ( $name ) {

        //return true === array_key_exists($name, $this->_types);
    }

    /**
     * Choose one type.
     *
     * @access  protected
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    protected function chooseOneType ( ) {

        return $this->_choosen =
                   $this->_types[Hoa_Test_Urg::Ud(0, count($this->_types) - 1)];
    }

    /**
     * Declare a dependence.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  Hoa_Test_Praspel_Variable
     * @throws  Hoa_Test_Praspel_Exception
     */
    public function hasTheSameTypeAs ( $name ) {

        try {

            $type = $this->getParent()->getParent()->getClause('requires')
                        ->getVariable($name)
                        ->getChoosenType();
        }
        catch ( Hoa_Test_Praspel_Exception $e ) {

            throw new Hoa_Test_Praspel_Exception(
                'Cannot found variable %s on the requires clause for making a ' .
                'dependence from %s.',
                1, array($name, $this->getName()));
        }

        // /!\ FIX ME /!\
        // check if type already exists.
        $this->_types[] = $type;

        return $this;
    }

    /**
     * Set the variable name.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  string
     */
    protected function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the variable name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get choosen type.
     *
     * @access  public
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function getChoosenType ( ) {

        if(null === $this->_choosen)
            $this->chooseOneType();

        return $this->_choosen;
    }

    /**
     * Set old value.
     *
     * @access  public
     * @return  mixed
     */
    public function setOldValue ( $value ) {

        $old             = $this->_oldValue;
        $this->_oldValue = $value;

        return $old;
    }

    /**
     * Get old value.
     *
     * @access  public
     * @return  mixed
     */
    public function getOldValue ( ) {

        return $this->_oldValue;
    }

    /**
     * Set new value.
     *
     * @access  public
     * @return  mixed
     */
    public function setNewValue ( $value ) {

        $old             = $this->_newValue;
        $this->_newValue = $value;

        return $old;
    }

    /**
     * Get new value.
     *
     * @access  public
     * @return  mixed
     */
    public function getNewValue ( ) {

        return $this->_newValue;
    }

    /**
     * Get all types.
     *
     * @access  public
     * @return  array
     */
    public function getTypes ( ) {

        return $this->_types;
    }

    /**
     * Set the parent (here: the clause).
     *
     * @access  protected
     * @param   Hoa_Test_Praspel_Clause  $parent    Parent (here: the clause).
     * @return  Hoa_Test_Praspel_Clause
     */
    protected function setParent ( Hoa_Test_Praspel_Clause $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get the parent (here: the clause).
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Clause
     */
    public function getParent ( ) {

        return $this->_parent;
    }

    /**
     * Transform this object model into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = '        ' . $this->getName() . "\n";

        foreach($this->getTypes() as $i => $type) {

            $gc   = get_class($type);
            $out .= '            ' .
                    strtolower(substr($gc, strrpos($gc, '_') + 1)) . "\n";
        }

        return $out;
    }
}
