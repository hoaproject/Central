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
 * @subpackage  Hoa_Test_Praspel_FreeVariable
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
 * Class Hoa_Test_Praspel_FreeVariable.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_FreeVariable
 */

class Hoa_Test_Praspel_FreeVariable {

    /**
     * Praspel's root.
     *
     * @var Hoa_Test_Praspel object
     */
    protected $_root    = null;

    /**
     * Clause where free variable is declared.
     *
     * @var Hoa_Test_Praspel_Clause object
     */
    protected $_clause  = null;

    /**
     * Free variable name.
     *
     * @var Hoa_Test_Praspel_FreeVariable string
     */
    protected $_name    = null;

    /**
     * Collection of types.
     *
     * @var Hoa_Test_Praspel_FreeVariable array
     */
    protected $_types   = array();

    /**
     * Choosen type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type object
     */
    protected $_choosen = null;



    /**
     * Set the free variable name.
     *
     * @access  public
     * @param   Hoa_Test_Praspel         $root      Praspel's root.
     * @param   Hoa_Test_Praspel_Clause  $clause    Clause.
     * @param   string                   $name      Free variable name.
     * @return  void
     */
    public function __construct ( Hoa_Test_Praspel        $root,
                                  Hoa_Test_Praspel_Clause $clause,
                                                          $name ) {

        $this->setRoot($root);
        $this->setClause($clause);
        $this->setName($name);

        return;
    }

    /**
     * Type the free variable.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @param   ...     ...      Type arguments.
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function hasType ( $name ) {

        $arguments = func_get_args();
        array_shift($arguments);
        $type      = new Hoa_Test_Praspel_Type(
            $this->getRoot(),
            $name,
            $arguments
        );

        return $this->_types[] = $type->getType();
    }

    /**
     * Choose one type.
     *
     * @access  public
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function chooseOneType ( ) {

        return $this->_choosen =
                   $this->_types[Hoa_Test_Urg::Ud(0, count($this->_types) - 1)];
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
     * Declare a dependence.
     *
     * @access  public
     * @param   string  $name    Free variable name.
     * @return  Hoa_Test_Praspel_FreeVariable
     * @throws  Hoa_Test_Praspel_Exception
     */
    public function depends ( $name ) {

        if(!($this->getClause() instanceof Hoa_Test_Praspel_Clause_Requires))
            throw new Hoa_Test_Praspel_Exception(
                'Only “requires” clause should have a dependence. ' .
                'So %s cannot be dependent of %s.',
                0, array($this->getName(), $name));

        try {

            $freeVar = $this->getClause()->getRoot()->getClause('requires')
                            ->getFreeVariable($name);
        }
        catch ( Hoa_Test_Praspel_Exception $e ) {

            throw new Hoa_Test_Praspel_Exception(
                'Cannot found free variable %s for making a dependence from %s.',
                1, array($name, $this->getName()));
        }

        foreach($freeVar->getTypes() as $i => $type)
            $this->_types[] = $type;

        return $freeVar;
    }

    /**
     * Set the clause.
     *
     * @access  protected
     * @param   Hoa_Test_Praspel_Clause  $clause    Clause.
     * @return  Hoa_Test_Praspel_Clause
     */
    protected function setClause ( Hoa_Test_Praspel_Clause $clause ) {

        $old           = $this->_clause;
        $this->_clause = $clause;

        return $old;
    }

    /**
     * Get the clause.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Clause
     */
    public function getClause ( ) {

        return $this->_clause;
    }

    /**
     * Set the free variable name.
     *
     * @access  public
     * @param   string  $name    Free variable name.
     * @return  string
     */
    protected function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the free variable name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
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
     * Set the Praspel's root.
     *
     * @access  protected
     * @param   Hoa_Test_Praspel  $root    Praspel's root.
     * @return  Hoa_Test_Praspel
     */
    protected function setRoot ( Hoa_Test_Praspel $root ) {

        $old         = $this->_root;
        $this->_root = $root;

        return $old;
    }

    /**
     * Get the Praspel's root.
     *
     * @access  public
     * @return  Hoa_Test_Praspel
     */
    public function getRoot ( ) {

        return $this->_root;
    }
}
