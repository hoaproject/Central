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
 * @subpackage  Hoa_Tokenizer_Token_Operator_Arithmetical
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
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Operator
 */
import('Tokenizer.Token.Operator');

/**
 * Class Hoa_Tokenizer_Token_Operator_Arithmetical.
 *
 * Represent arithmetical operators.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Operator_Arithmetical
 */

class Hoa_Tokenizer_Token_Operator_Arithmetical extends Hoa_Tokenizer_Token_Operator {

    /**
     * Operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Arithmetical string
     */
    protected $_operator   = '+';

    /**
     * Operator type.
     *
     * @var Hoa_Tokenizer_Token_Operator_Arithmetical mixed
     */
    protected $_type       = Hoa_Tokenizer::_PLUS;

    /**
     * Operator arity.
     *
     * @var Hoa_Tokenizer_Token_Operator_Arithmetical int
     */
    protected $_arity      = parent::BINARY;

    /**
     * Operator precedence.
     *
     * @var Hoa_Tokenizer_Token_Operator_Arithmetical int
     */
    protected $_precedence = 14;



    /**
     * Set operator.
     *
     * @access  public
     * @param   string  $operator    Operator.
     * @return  string
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setOperator ( $operator ) {

        switch($operator) {

            case '+';
                $this->setType(Hoa_Tokenizer::_PLUS);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(14);
              break;

            case '-':
                $this->setType(Hoa_Tokenizer::_MINUS);
                $this->setArity(parent::MIXED);
                $this->setPrecedence(14);
              break;

            case '*':
                $this->setType(Hoa_Tokenizer::_MUL);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(15);
              break;

            case '/':
                $this->setType(Hoa_Tokenizer::_DIV);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(15);
              break;

            case '%':
                $this->setType(Hoa_Tokenizer::_MOD);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(15);
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Operator %s is not an arithmetic operator.', 0, $operator);
        }

        return parent::setOperator($operator);
    }
}
