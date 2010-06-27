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
 * @subpackage  Hoa_Test_Praspel_Variable
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
 * Hoa_Test_Praspel_TypeDisjunction
 */
import('Test.Praspel.TypeDisjunction');

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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Variable
 */

class Hoa_Test_Praspel_Variable extends Hoa_Test_Praspel_TypeDisjunction {

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

        parent::__construct();

        $this->setParent($parent);
        $this->setName($name);
        $this->_and = $this->getParent();

        return;
    }

    /**
     * Choose one type.
     *
     * @access  public
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function chooseOneType ( ) {

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
}
