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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Array
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
 * Hoa_Pom_Token_Array
 */
import('Pom.Token.Array');

/**
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_Array.
 *
 * Visit an array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Array
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_Array extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit an array.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitArray ( Hoa_Visitor_Element $element, &$handle = null ) {

        $first  = true;
        $array  = array();
        $handle = null;

        foreach($element->getArray() as $i => $a) {

            if(false === $first)
                $array[] = array(
                    0 => Hoa_Pom::_COMMA,
                    1 => ',',
                    2 => -1
                );
            else
                $first = false;

            $handle = array_merge(
                (null !== $a[Hoa_Pom_Token_Array::KEY]
                     ? array_merge(
                           $a[Hoa_Pom_Token_Array::KEY]->accept(
                               $this->getVisitor(),
                               $handle
                           ),
                           array(array(
                               0 => Hoa_Pom::_DOUBLE_ARROW,
                               1 => '=>',
                               2 => -1
                           ))
                       )
                     : array()
                ),
                $a[Hoa_Pom_Token_Array::VALUE]->accept(
                    $this->getVisitor(),
                    $handle
                )
            );

            foreach($handle as $key => $value)
                $array[] = $value;
        }

        return array_merge(
            array(array(
                0 => Hoa_Pom::_ARRAY,
                1 => 'array',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $array,
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            ))
        );
    }
}
