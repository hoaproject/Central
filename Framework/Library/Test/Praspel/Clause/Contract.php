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
 * @subpackage  Hoa_Test_Praspel_Clause_Contract
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
 * Hoa_Test_Praspel_FreeVariable
 */
import('Test.Praspel.FreeVariable');

/**
 * Class Hoa_Test_Praspel_Clause_Contract.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Clause_Contract
 */

abstract class Hoa_Test_Praspel_Clause_Contract {

    /**
     * Praspel's root.
     *
     * @var Hoa_Test_Praspel object
     */
    protected $_root          = null;

    /**
     * Collection of free variables.
     *
     * @var Hoa_Test_Praspel_Clause_Contract array
     */
    protected $_freeVariables = array();



    /**
     * Constructor.
     * Set the Praspel's root.
     *
     * @access  public
     * @param   Hoa_Test_Praspel  $root    Praspel's root.
     * @return  void
     */
    public function __construct ( Hoa_Test_Praspel $root ) {

        $this->setRoot($root);
    }

    /**
     * Declare a free variable.
     *
     * @access  public
     * @param   string  $name    Free variable name.
     * @return  Hoa_Test_Praspel_FreeVariable
     */
    public function declareFreeVariable ( $name ) {

        if(true === $this->freeVariableExists($name))
            return $this->_freeVariables[$name];

        return $this->_freeVariables[$name] = new Hoa_Test_Praspel_FreeVariable(
            $this->getRoot(),
            $this,
            $name
        );
    }

    /**
     * Check if a free variable already exists or not.
     *
     * @access  public
     * @param   string  $name    Free variable name.
     * @return  Hoa_Test_Praspel_FreeVariable
     */
    public function freeVariableExists ( $name ) {

        return isset($this->_freeVariables[$name]);
    }

    /**
     * Get a specific free variable.
     *
     * @access  public
     * @param   string  $name    Free variable name.
     * @return  Hoa_Test_Praspel_FreeVariable
     * @throw   Hoa_Test_Praspel_Exception
     */
    public function getFreeVariable ( $name ) {

        if(false === $this->freeVariableExists($name))
            throw new Hoa_Test_Praspel_Exception(
                'Free variable %s is not found.', 0, $name);

        return $this->_freeVariables[$name];
    }

    /**
     * Get all free variables.
     *
     * @access  public
     * @return  array
     */
    public function getFreeVariables ( ) {

        return $this->_freeVariables;
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
