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
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_Conditional_If_If
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
 * Class Hoa_Tokenizer_Token_ControlStructure_Conditional_If_If.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_Conditional_If_If
 */

class          Hoa_Tokenizer_Token_ControlStructure_Conditional_If_If
    extends    Hoa_Tokenizer_Token_Instruction_Block
    implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Expression.
     *
     * @var mixed object
     */
    protected $_expression = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $expression    Expression.
     * @return  void
     */
    public function __construct ( $expression ) {

        $this->setExpression($expression);

        return;
    }

    /**
     * Set expression.
     *
     * @access  public
     * @param   mixed   $expression    Expression.
     * @return  mixed
     */
    public function setExpression ( $expression) {

        switch(get_class($expression)) {

            case 'Hoa_Tokenizer_Token_Array':
            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Cast':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_Variable':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'An expression cannot be constitued by a class that ' .
                    'is an instance of %s.', 0, get_class($expression));
        }

        $old               = $old;
        $this->_expression = $expression;

        return $old;
    }

    /**
     * Get expression.
     *
     * @access  public
     * @return  mixed
     */
    public function getExpression ( ) {

        return $this->_expression;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            array(array(
                0 => Hoa_Tokenizer::_IF,
                1 => 'if',
                2 => -1
            )),
            array(array(
                0 => Hoa_Tokenizer::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $this->getExpression()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            parent::tokenize()
        );
    }
}
