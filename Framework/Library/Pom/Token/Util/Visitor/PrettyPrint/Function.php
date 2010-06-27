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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Function
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
 * Hoa_Pom_Token_Function_Argument
 */
import('Pom.Token.Function.Argument');

/**
 * Hoa_Pom_Token_Function_Named
 */
import('Pom.Token.Function.Named');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Function.
 *
 * Visit a function.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Function
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Function extends Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate {

    /**
     * Visit a function argument.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitFunctionArgument ( Hoa_Visitor_Element $element,
                                            &$handle = null,
                                             $eldnah = null ) {

        return
            (true === $element->isTyped()
                 ? (strtolower($element->getType()->getString()) == 'array'
                        ? 'Array'
                        : $element->getType->accept($this->getVisitor(), $handle, $eldnah)
                   ) . ' '
                 : ''
            ) .
            (true === $element->isReferenced()
                 ? '&'
                 : ''
            ) .
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah) .
            (true === $element->hasDefaultValue()
                 ? $element->getOperator()->accept($this->getVisitor(), $handle, $eldnah) .
                   $element->getDefaultValue()->accept($this->getVisitor(), $handle, $eldnah)
                 : ''
            );
    }

    /**
     * Visit a function named.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitFunctionNamed ( Hoa_Visitor_Element $element,
                                         &$handle = null,
                                          $eldnah = null ) {

        $argSet    = false;
        $arguments = null;
        $body      = null;

        foreach($element->getArguments() as $i => $argument) {

            if(true === $argSet)
                $arguments .= ', ';
            else
                $argSet     = true;

            $arguments .= $argument->accept($this->getVisitor(), $handle, $eldnah);
        }

        foreach($element->getBody() as $i => $b)
            $body .= $b->accept($this->getVisitor(), $handle, $eldnah);

        return
            (true === $element->hasComment() && true === $element->isCommentEnabled()
                 ? $element->getComment()->accept($this->getVisitor(), $handle, $eldnah) . "\n"
                 : ''
            ) .
            'function ' .
            (true === $element->isReferenced()
                 ? '&'
                 : ''
            ) .
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah) .
            ' ( ' .
            $arguments .
            ' ) {' .
            $body .
            '}' . "\n";
    }
}
