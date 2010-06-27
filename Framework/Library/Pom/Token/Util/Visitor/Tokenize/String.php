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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_String
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
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Pom_Token_String_Boolean
 */
import('Pom.Token.String.Boolean');

/**
 * Hoa_Pom_Token_String_Constant
 */
import('Pom.Token.String.Constant');

/**
 * Hoa_Pom_Token_String_EncapsedConstant
 */
import('Pom.Token.String.EncapsedConstant');

/**
 * Hoa_Pom_Token_String_Null
 */
import('Pom.Token.String.Null');

/**
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_String.
 *
 * Visit a string.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_String
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_String extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit a string.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitString ( Hoa_Visitor_Element $element,
                                  &$handle = null,
                                   $eldnah = null ) {

        return array(array(
            0 => Hoa_Pom::_STRING,
            1 => $element->getString(),
            2 => -1
        ));
    }

    /**
     * Visit a string encapsed constant.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitStringEncapsedConstant ( Hoa_Visitor_Element $element,
                                                  &$handle = null,
                                                   $eldnah = null ) {

        return array(array(
            0 => Hoa_Pom::_CONSTANT_ENCAPSED_STRING,
            1 => $element->getString(),
            2 => -1
        ));
    }
}
