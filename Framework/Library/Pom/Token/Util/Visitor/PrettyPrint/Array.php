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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Array
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
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Array.
 *
 * Visit an array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Array
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Array extends Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate {

    /**
     * Visit an array.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function visitArray ( Hoa_Visitor_Element $element,
                                 &$handle = null,
                                  $eldnah = null ) {

        $first = true;
        $array = null;

        foreach($element->getArray() as $i => $a) {

            if(false === $first)
                $array .= ', ';
            else
                $first  = false;

            $array .=
                (null !== $a[Hoa_Pom_Token_Array::KEY]
                     ? $a[Hoa_Pom_Token_Array::KEY]->accept(
                           $this->getVisitor(),
                           $handle,
                           $eldnah
                       ) .
                       ' => '
                     : ''
                ) .
                $a[Hoa_Pom_Token_Array::VALUE]->accept(
                    $this->getVisitor(),
                    $handle,
                    $eldnah
                );
        }

        return 'array(' .
               $array .
               ')';
    }
}
