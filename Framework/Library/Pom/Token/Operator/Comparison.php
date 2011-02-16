<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_Operator_Comparison
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
 * Hoa_Pom_Token_Operator
 */
import('Pom.Token.Operator');

/**
 * Class Hoa_Pom_Token_Operator_Comparison.
 *
 * Represent comparisons operators.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operator_Comparison
 */

class Hoa_Pom_Token_Operator_Comparison extends Hoa_Pom_Token_Operator {

    /**
     * Operator.
     *
     * @var Hoa_Pom_Token_Operator_Comparison string
     */
    protected $_operator   = '==';

    /**
     * Operator type.
     *
     * @var Hoa_Pom_Token_Operator_Comparison mixed
     */
    protected $_type       = Hoa_Pom::_IS_EQUAL;

    /**
     * Operator arity.
     *
     * @var Hoa_Tokeniezr_Token_Operator_Comparison int
     */
    protected $_arity      = parent::BINARY;

    /**
     * Operator precedence.
     *
     * @var Hoa_Pom_Token_Operator_Comparison int
     */
    protected $_precedence = 11;



    /**
     * Set operator.
     *
     * @access  public
     * @param   string  $operator    Operator.
     * @return  string
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function setOperator ( $operator ) {

        switch($operator) {

            case '==';
                $this->setType(Hoa_Pom::_IS_EQUAL);
                $this->setPrecedence(11);
              break;

            case '===':
                $this->setType(Hoa_Pom::_IS_IDENTICAL);
                $this->setPrecedence(11);
              break;

            case '!=':
                $this->setType(Hoa_Pom::_IS_NOT_EQUAL);
                $this->setPrecedence(12);
              break;

            case '<>':
                $this->setType(Hoa_Pom::_IS_NOT_EQUAL);
                $this->setPrecedence(11);
              break;

            case '!==':
                $this->setType(Hoa_Pom::_IS_NOT_IDENTICAL);
                $this->setPrecedence(11);
              break;

            case '<':
                $this->setType(Hoa_Pom::_IS_SMALLER);
                $this->setPrecedence(12);
              break;

            case '>':
                $this->setType(Hoa_Pom::_IS_GREATER);
                $this->setPrecedence(12);
              break;

            case '<=':
                $this->setType(Hoa_Pom::_IS_SMALLER_OR_EQUAL);
                $this->setPrecedence(12);
              break;

            case '>=':
                $this->setType(Hoa_Pom::_IS_GREATER_OR_EQUAL);
                $this->setPrecedence(12);
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'Operator %s is not a comparison operator.', 0, $operator);
        }

        return parent::setOperator($operator);
    }
}
