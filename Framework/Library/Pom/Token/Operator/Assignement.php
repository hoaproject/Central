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
 * @subpackage  Hoa_Pom_Token_Operator_Assignement
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
 * Class Hoa_Pom_Token_Operator_Assignement.
 *
 * Represent assignement operators.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operator_Assignement
 */

class Hoa_Pom_Token_Operator_Assignement extends Hoa_Pom_Token_Operator {

    /**
     * Operator.
     *
     * @var Hoa_Pom_Token_Operator_Assignement string
     */
    protected $_operator   = '=';

    /**
     * Operator type.
     *
     * @var Hoa_Pom_Token_Operator_Assignement mixed
     */
    protected $_type       = Hoa_Pom::_EQUAL;

    /**
     * Operator arity.
     *
     * @var Hoa_Pom_Token_Operator_Assignement int
     */
    protected $_arity      = parent::BINARY;

    /**
     * Operator precedence.
     *
     * @var Hoa_Pom_Token_Operator_Assignement int
     */
    protected $_precedence = 4;



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

            case '=';
                $this->setType(Hoa_Pom::_EQUAL);
              break;

            case '&=':
                $this->setType(Hoa_Pom::_AND_EQUAL);
              break;

            case '.=':
                $this->setType(Hoa_Pom::_CONCAT_EQUAL);
              break;

            case '/=':
                $this->setType(Hoa_Pom::_DIV_EQUAL);
              break;

            case '-=':
                $this->setType(Hoa_Pom::_MINUS_EQUAL);
              break;

            case '%=':
                $this->setType(Hoa_Pom::_MOD_EQUAL);
              break;

            case '*=':
                $this->setType(Hoa_Pom::_MUL_EQUAL);
              break;

            case '|=':
                $this->setType(Hoa_Pom::_OR_EQUAL);
              break;

            case '+=':
                $this->setType(Hoa_Pom::_PLUS_EQUAL);
              break;

            case '<<=':
                $this->setType(Hoa_Pom::_SL_EQUAL);
              break;

            case '>>=':
                $this->setType(Hoa_Pom::_SR_EQUAL);
              break;

            case '^=':
                $this->setType(Hoa_Pom::_XOR_EQUAL);
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'Operator %s is not an assign operator.', 0, $operator);
        }

        return parent::setOperator($operator);
    }
}
