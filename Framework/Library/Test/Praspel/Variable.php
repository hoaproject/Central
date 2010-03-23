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
     * Go forward to set the next argument on the current type (and carry the
     * current used type).
     *
     * @var Hoa_Test_Praspel_Type object
     */
    public $_comma      = null;



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
     * @return  Hoa_Test_Praspel_Type
     */
    public function isTypedAs ( $name ) {

        if(true === $this->isTypeDeclared($name))
            return $this;

        return $this->_comma = new Hoa_Test_Praspel_Type($this, $name);
    }

    /**
     * Close the current defining type.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Variable
     */
    public function _ok ( ) {

        if(null === $this->_comma)
            return $this;

        $type         = $this->_comma->getType();
        $this->_comma = null;

        $this->_types[$type->getName()] = $type;

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

        return true === array_key_exists($name, $this->_types);
    }

    /**
     * Choose one type.
     *
     * @access  protected
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    protected function chooseOneType ( ) {

        $i = Hoa_Test_Urg::Ud(0, count($this->_types) - 1);
        reset($this->_types);

        $type = null;

        foreach($this->_types as $name => $type)
            if(0 === $i--)
                break;

        return $this->_choosen = $type;
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

        $context = $this->getParent();

        if($this->getParent() instanceof Hoa_Test_Praspel_Clause_Requires) {

            if($name[0] == '\\')
                throw new Hoa_Test_Praspel_Exception(
                    'Constructors are not allowed in “requires” clause, given %s.',
                    0, $name);

            $context = $this->getParent();
        }
        elseif($this->getParent() instanceof Hoa_Test_Praspel_Clause_Ensures) {

            if($name == '\result')
                throw new Hoa_Test_Praspel_Exception(
                    'The operator “typeof” is not commutative. ' .
                    '\result must be in the left position.', 1);

            if(0 !== preg_match('#\\\old\(\s*(\w+)\s*\)#i', $name, $matches)) {

                $context = $this->getParent()->getParent();

                if(false === $context->clauseExists('requires'))
                    throw new Hoa_Test_Praspel_Exception(
                        'Foobar %s',
                        2, $name);

                $name    = $matches[1];
                $context = $context->getClause('requires');
            }
        }

        if(false === $context->variableExists($name))
            throw new Hoa_Test_Praspel_Exception(
                'Cannot ensure a property on the non-existing variable %s.',
                3, $name);

        $type = $context->getVariable($name)->getChoosenType();

        if(null === $type)
            return $this;

        if(false === $this->isTypeDeclared($type->getName()))
            $this->_types[$type->getName()] = $type;

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

        foreach($this->getTypes() as $i => $type)
            $out .= '            ' . $type->getName() . "\n";

        return $out;
    }
}
