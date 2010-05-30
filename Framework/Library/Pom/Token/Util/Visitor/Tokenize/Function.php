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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Function
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
 * Hoa_Pom_Token_Function_Argument
 */
import('Pom.Token.Function.Argument');

/**
 * Hoa_Pom_Token_Function_Named
 */
import('Pom.Token.Function.Named');

/**
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_Function.
 *
 * Visit a function.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Function
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_Function extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit a function argument.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitFunctionArgument ( Hoa_Visitor_Element $element,
                                            &$handle = null,
                                             $eldnah = null ) {

        return array_merge(
            (true === $element->isTyped()
                 ? (strtolower($element->getType()->getString()) == 'array'
                        ? array(array(
                              0 => Hoa_Pom::_ARRAY,
                              1 => $element->getType()->getString(),
                              2 => -1
                          ))
                        : $element->getType()->accept($this->getVisitor(), $handle, $eldnah)
                   )
                 : array()
            ),
            (true === $element->isReferenced()
                 ? array(array(
                       0 => Hoa_Pom::_REFERENCE,
                       1 => '&',
                       2 => -1
                   ))
                 : array()
            ),
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah),
            (true === $element->hasDefaultValue()
                 ? array_merge(
                       $element->getOperator()->accept($this->getVisitor(), $handle, $eldnah),
                       $element->getDefaultValue()->accept($this->getVisitor(), $handle, $eldnah)
                   )
                 : array()
            )
        );
    }

    /**
     * Visit a function named.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitFunctionNamed ( Hoa_Visitor_Element $element,
                                         &$handle = null,
                                          $eldnah = null ) {

        $argSet    = false;
        $arguments = array();
        $body      = array();

        foreach($element->getArguments() as $i => $argument) {

            if(true === $argSet)
                $arguments[] = array(
                    0 => Hoa_Pom::_COMMA,
                    1 => ',',
                    2 => -1
                );
            else
                $argSet      = true;

            foreach($argument->accept($this->getVisitor(), $handle, $eldnah) as $key => $value)
                $arguments[] = $value;
        }

        foreach($element->getBody() as $i => $b)
            foreach($b->accept($this->getVisitor(), $handle, $eldnah) as $key => $value)
                $body[] = $value;

        return array_merge(
            (true === $element->hasComment() && true === $element->isCommentEnabled()
                 ? $element->getComment()->accept($this->getVisitor(), $handle, $eldnah)
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_FUNCTION,
                1 => 'function',
                2 => -1
            )),
            (true === $element->isReferenced()
                 ? array(array(
                       0 => Hoa_Pom::_REFERENCE,
                       1 => '&',
                       3 => -1
                   ))
                 : array()
            ),
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $arguments,
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_BRACE,
                1 => '{',
                2 => -1
            )),
            $body,
            array(array(
                0 => Hoa_Pom::_CLOSE_BRACE,
                1 => '}',
                2 => -1
            ))
        );
    }
}
