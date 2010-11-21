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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Loop_Foreach
 *
 */

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Instruction_Block
 */
import('Pom.Token.Instruction.Block');

/**
 * Class Hoa_Pom_Token_ControlStructure_Loop_Foreach.
 *
 * Represent a foreach loop.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Loop_Foreach
 */

class       Hoa_Pom_Token_ControlStructure_Loop_Foreach
    extends Hoa_Pom_Token_Instruction_Block {

    /**
     * Array expression.
     *
     * @var mixed object
     */
    protected $_arrayExpression = null;

    /**
     * Key.
     *
     * @var Hoa_Pom_Token_Variable object
     */
    protected $_key             = null;

    /**
     * Value.
     *
     * @var Hoa_Pom_Token_Variable object
     */
    protected $_value           = null;

    /**
     * Whether value is referenced.
     *
     * @var Hoa_Pom_Token_ControlStructure_Loop_Foreach bool
     */
    protected $_referenced      = false;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $arrayExpression    Array expression.
     * @return  void
     */
    public function __construct ( $arrayExpression ) {

        $this->setArrayExpression($arrayExpression);

        return;
    }

    /**
     * Set array expression.
     *
     * @access  public
     * @param   mixed   $arrayExpression    Array expression.
     * @return  mixed
     */
    public function setArrayExpression ( $arrayExpression) {

        switch(get_class($arrayExpression)) {

            // ternay works here.
            case 'Hoa_Pom_Token_Array':
            case 'Hoa_Pom_Token_Call':
            case 'Hoa_Pom_Token_Cast':
            case 'Hoa_Pom_Token_Clone':
            case 'Hoa_Pom_Token_Comment':
            case 'Hoa_Pom_Token_New':
            case 'Hoa_Pom_Token_Operation':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'An array expression cannot be constitued by a class that ' .
                    'is an instance of %s.', 0, get_class($expression));
        }

        $old               = $old;
        $this->_expression = $expression;

        return $old;
    }

    /**
     * Set key.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $key    Key.
     * @return  Hoa_Pom_Token_Variable
     */
    public function setKey ( Hoa_Pom_Token_Variable $key ) {

        $old        = $this->_key;
        $this->_key = $key;

        return $old;
    }

    /**
     * Remove key.
     *
     * @access  public
     * @return  mixed
     */
    public function removeKey ( ) {

        $old        = $this->_key;
        $this->_key = null;

        return $old;
    }

    /**
     * Set value.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $value    Value.
     * @return  Hoa_Pom_Token_Variable
     */
    public function setValue ( Hoa_Pom_Token_Variable $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Reference value or not.
     *
     * @access  public
     * @param   bool    $reference    Reference value or not.
     * @return  bool
     */
    public function referenceValue ( $enable = true ) {

        $old              = $this->_reference;
        $this->_reference = $enable;

        return $old;
    }

    /**
     * Get array expression.
     *
     * @access  public
     * @return  mixed
     */
    public function getArrayExpression ( ) {

        return $this->_arrayExpression;
    }

    /**
     * Get key.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Variable
     */
    public function getKey ( ) {

        return $this->_key;
    }

    /**
     * Check if a key exists.
     *
     * @access  public
     * @return  bool
     */
    public function keyExists ( ) {

        return $this->_key !== null;
    }

    /**
     * Get value.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Variable
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Check if a value exists.
     *
     * @access  protected
     * @return  bool
     */
    protected function valueExists ( ) {

        return $this->_value !== null;
    }

    /**
     * Check if value is referenced or not.
     *
     * @access  public
     * @return  bool
     */
    public function isReferencedValue ( ) {

        return $this->_referenced;
    }
}
