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
 * @subpackage  Hoa_Pom_Token_Operator_InDeCrementing
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

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
 * Class Hoa_Pom_Token_Operator_InDeCrementing.
 *
 * Represent arithmetics operators.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operator_InDeCrementing
 */

class Hoa_Pom_Token_Operator_InDeCrementing extends Hoa_Pom_Token_Operator {

    /**
     * Operator.
     *
     * @var Hoa_Pom_Token_Operator_InDeCrementing string
     */
    protected $_operator   = '++';

    /**
     * Operator type.
     *
     * @var Hoa_Pom_Token_Operator_InDeCrementing mixed
     */
    protected $_type       = Hoa_Pom::_INC;

    /**
     * Operator arity.
     *
     * @var Hoa_Pom_Token_Operator_InDeCrementing int
     */
    protected $_arity      = parent::UNARY;

    /**
     * Operator precedence.
     *
     * @var Hoa_Pom_Token_Operator_InDeCrementing int
     */
    protected $_precedence = 19;



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

            case '++';
                $this->setType(Hoa_Pom::_INC);
              break;

            case '--':
                $this->setType(Hoa_Pom::_DEC);
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'Operator %s is not a de/in-crementing  operator.', 0, $operator);
        }

        return parent::setOperator($operator);
    }
}
