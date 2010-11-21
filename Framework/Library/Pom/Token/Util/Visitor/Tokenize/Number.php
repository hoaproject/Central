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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Number
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
 * Hoa_Pom_Token_Number
 */
import('Pom.Token.Number');

/**
 * Hoa_Pom_Token_Number_DNumber
 */
import('Pom.Token.Number.DNumber');

/**
 * Hoa_Pom_Token_Number_LNumber
 */
import('Pom.Token.Number.LNumber');

/**
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_Number.
 *
 * Visit a number.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Number
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_Number extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit a number dnumber.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitNumberDNumber ( Hoa_Visitor_Element $element,
                                         &$handle = null,
                                          $eldnah = null ) {

        return array(array(
            0 => Hoa_Pom::_DNUMBER,
            1 => $element->getNumber(),
            2 => -1
        ));
    }

    /**
     * Visit a number lnumber.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitNumberLNumber ( Hoa_Visitor_Element $element,
                                         &$handle = null,
                                          $eldnah = null ) {

        return array(array(
            0 => Hoa_Pom::_LNUMBER,
            1 => $element->getNumber(),
            2 => -1
        ));
    }
}
