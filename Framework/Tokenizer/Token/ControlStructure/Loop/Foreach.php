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
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_Loop_Foreach
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Token_Util_Exception
 */
import('Tokenizer.Token.Util.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Interface_Tokenizable
 */
import('Tokenizer.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Instruction_Block
 */
import('Tokenizer.Token.Instruction.Block');

/**
 * Class Hoa_Tokenizer_Token_ControlStructure_Loop_Foreach.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_Loop_Foreach
 */

class          Hoa_Tokenizer_Token_ControlStructure_Loop_Foreach
    extends    Hoa_Tokenizer_Token_Instruction_Block
    implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Array expression.
     *
     * @var mixed object
     */
    protected $_arrayExpression = null;

    /**
     * Key.
     *
     * @var Hoa_Tokenizer_Token_Variable object
     */
    protected $_key             = null;

    /**
     * Value.
     *
     * @var Hoa_Tokenizer_Token_Variable object
     */
    protected $_value           = null;

    /**
     * Whether value is referenced.
     *
     * @var Hoa_Tokenizer_Token_ControlStructure_Loop_Foreach bool
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
            case 'Hoa_Tokenizer_Token_Array':
            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Cast':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_Variable':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
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
     * @param   Hoa_Tokenizer_Token_Variable  $key    Key.
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function setKey ( Hoa_Tokenizer_Token_Variable $key ) {

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
     * @param   Hoa_Tokenizer_Token_Variable  $value    Value.
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function setValue ( Hoa_Tokenizer_Token_Variable $value ) {

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
     * @return  Hoa_Tokenizer_Token_Variable
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
     * @return  Hoa_Tokenizer_Token_Variable
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

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Tokenizer_Token_Util_Interface
     */
    public function tokenize ( ) {

        if(false === $this->valueExists())
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'A foreach loop must have a value variable.', 0);

        return array_merge(
            array(array(
                0 => Hoa_Tokenizer::_FOREACH,
                1 => 'foreach',
                2 => -1,
            )),
            array(array(
                0 => Hoa_Tokenizer::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $this->getArrayExpression()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_AS,
                1 => 'as',
                2 => -1
            )),
            (true === $this->keyExists()
                 ? array_merge(
                       $this->getKey()->tokenize()
                       array(array(
                           0 => Hoa_Tokenizer::_DOUBLE_ARROW,
                           1 => '=>',
                           2 => -1
                       ))
                   )
                 : array()
            ),
            $this->getValue()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            parent::tokenize()
        );
    }
}
