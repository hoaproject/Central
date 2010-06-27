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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operator
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
 * Hoa_Pom_Token_Operator_Arithmetical
 */
import('Pom.Token.Operator.Arithmetical');

/**
 * Hoa_Pom_Token_Operator_Assignement
 */
import('Pom.Token.Operator.Assignement');

/**
 * Hoa_Pom_Token_Operator_Bitwise
 */
import('Pom.Token.Operator.Bitwise');

/**
 * Hoa_Pom_Token_Operator_Comparison
 */
import('Pom.Token.Operator.Comparison');

/**
 * Hoa_Pom_Token_Operator_ErrorControl
 */
import('Pom.Token.Operator.ErrorControl');

/**
 * Hoa_Pom_Token_Operator_Execution
 */
import('Pom.Token.Operator.Execution');

/**
 * Hoa_Pom_Token_Operator_InDeCrementing
 */
import('Pom.Token.Operator.InDeCrementing');

/**
 * Hoa_Pom_Token_Operator_Logical
 */
import('Pom.Token.Operator.Logical');

/**
 * Hoa_Pom_Token_Operator_String
 */
import('Pom.Token.Operator.String');

/**
 * Hoa_Pom_Token_Operator_Type
 */
import('Pom.Token.Operator.Type');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operator.
 *
 * Visit an operator.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operator
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operator extends Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate {

    /**
     * Visit all operators.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitOperators ( Hoa_Visitor_Element $element,
                                     &$handle = null,
                                      $eldnah = null ) {

        return ' ' . $element->getOperator() . ' ';
    }
}
