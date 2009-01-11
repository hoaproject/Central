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
 * @subpackage  Hoa_Tokenizer_Token_Operator_Execution
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
 * Class Hoa_Tokenizer_Token_Operator_Execution.
 *
 * Represent an execution operator.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Operator_Execution
 */

class Hoa_Tokenizer_Token_Operator_Execution extends Hoa_Tokenizer_Token_Operator {

    /**
     * Operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Execution string
     */
    protected $_operator   = '`';

    /**
     * Operator type.
     *
     * @var Hoa_Tokenizer_Token_Operator_Execution mixed
     */
    protected $_type       = Hoa_Tokenizer::_EXECUTION;

    /**
     * Operator arity.
     *
     * @var Hoa_Tokenizer_Token_Operator_Execution int
     */
    protected $_arity      = parent::UNARY;

    /**
     * Operator precedence.
     *
     * @var Hoa_Tokenizer_Token_Operator_Execution int
     */
    protected $_precedence = -1;



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

            case '`';
                $this->setType(Hoa_Tokenizer::_EXECUTION);
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Operator %s is not an execution operator.', 0, $operator);
        }

        return parent::setOperator($operator);
    }
}
