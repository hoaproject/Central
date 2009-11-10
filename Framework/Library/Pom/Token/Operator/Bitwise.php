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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operator_Bitwise
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * Class Hoa_Pom_Token_Operator_Bitwise.
 *
 * Represent bitwise operators.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operator_Bitwise
 */

class Hoa_Pom_Token_Operator_Bitwise extends Hoa_Pom_Token_Operator {

    /**
     * Operator.
     *
     * @var Hoa_Pom_Token_Operator_Bitwise string
     */
    protected $_operator   = '&';

    /**
     * Operator type.
     *
     * @var Hoa_Pom_Token_Operator_Bitwise mixed
     */
    protected $_type       = Hoa_Pom::_BITWISE_AND;

    /**
     * Operator arity.
     *
     * @var Hoa_Pom_Token_Operator_Bitwise int
     */
    protected $_arity      = parent::BINARY;

    /**
     * Operator precedence.
     *
     * @var Hoa_Pom_Token_Operator_Bitwise int
     */
    protected $_precedence = 10;



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

            case '&';
                $this->setType(Hoa_Pom::_BITWISE_AND);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(10);
              break;

            case '|':
                $this->setType(Hoa_Pom::_BITWISE_OR);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(8);
              break;

            case '^':
                $this->setType(Hoa_Pom::_BITWISE_XOR);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(9);
              break;

            case '~':
                $this->setType(Hoa_Pom::_BITWISE_NOT);
                $this->setArity(parent::UNARY);
                $this->setPrecedence(18);
              break;

            case '>>':
                $this->setType(Hoa_Pom::_SR);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(13);
              break;

            case '<<':
                $this->setType(Hoa_Pom::_SL);
                $this->setArity(parent::BINARY);
                $this->setPrecedence(13);
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'Operator %s is not a bitwise operator.', 0, $operator);
        }

        return parent::setOperator($operator);
    }
}
