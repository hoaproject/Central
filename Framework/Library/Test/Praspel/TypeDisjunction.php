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
 * @subpackage  Hoa_Test_Praspel_TypeDisjunction
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
 * Class Hoa_Test_Praspel_TypeDisjunction.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_TypeDisjunction
 */

abstract class Hoa_Test_Praspel_TypeDisjunction {

    /**
     * Collection of types.
     *
     * @var Hoa_Test_Praspel_TypeDisjunction array
     */
    protected $_types  = array();

    /**
     * Current defining type.
     *
     * @var Hoa_Test_Praspel_Type object
     */
    protected $_type   = null;

    /**
     * Make a disjunction between two variables.
     *
     * @var Hoa_Test_Praspel_TypeDisjunction object
     */
    public $_or        = null;

    /**
     * 
     *
     * 
     */
    protected $_i      = 0;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_or = $this;

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

        return $this->_type = new Hoa_Test_Praspel_Type($this, $name);
    }

    /**
     * Close the current defining type.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_TypeDisjunction
     */
    public function _ok ( ) {

        if(null === $this->_type)
            return $this;

        $type                                         = $this->_type->getType();
        $this->_type                                  = null;
        $this->_types[$this->_i++ . $type->getName()] = $type;

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
     * Get all types.
     *
     * @access  public
     * @return  array
     */
    public function getTypes ( ) {

        return $this->_types;
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
