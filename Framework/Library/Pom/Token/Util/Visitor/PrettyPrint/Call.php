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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Call
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
 * Hoa_Pom_Token_Call_Attribute
 */
import('Pom.Token.Call.Attribute');

/**
 * Hoa_Pom_Token_Call_ClassConstant
 */
import('Pom.Token.Call.ClassConstant');

/**
 * Hoa_Pom_Token_Call_Function
 */
import('Pom.Token.Call.Function');

/**
 * Hoa_Pom_Token_Call_Method
 */
import('Pom.Token.Call.Method');

/**
 * Hoa_Pom_Token_Call_StaticAttribute
 */
import('Pom.Token.Call.StaticAttribute');

/**
 * Hoa_Pom_Token_Call_StaticMethod
 */
import('Pom.Token.Call.StaticMethod');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Call.
 *
 * Visit a call.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Call
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Call extends Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate {

    /**
     * Visit a call attribute.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitCallAttribute ( Hoa_Visitor_Element $element,
                                         &$handle = null,
                                          $eldnah = null ) {

        return $element->getObject()->accept($this->getVisitor(), $handle, $eldnah) .
               '->' .
               $element->getAttribute()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a call class constant.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitCallClassConstant ( Hoa_Visitor_Element $element,
                                             &$handle = null,
                                              $eldnah = null ) {

        return $element->getClass()->accept($this->getVisitor(), $handle, $eldnah) .
               '::' .
               $element->getConstant()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a call function.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitCallFunction ( Hoa_Visitor_Element $element,
                                        &$handle = null,
                                         $eldnah = null ) {

        $arguments = null;
        $argSet    = false;

        foreach($element->getArguments() as $i => $argument) {

            if(true === $argSet)
                $arguments .= ', ';
            else
                $argSet     = true;

            $arguments .= $argument->accept($this->getVisitor(), $handle, $eldnah);
        }

        return $element->getName()->accept($this->getVisitor(), $handle, $eldnah) .
               '(' .
               $arguments .
               ')';
    }

    /**
     * Visit a call method.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitCallMethod ( Hoa_Visitor_Element $element,
                                      &$handle = null,
                                       $eldnah = null ) {

        return $element->getObject()->accept($this->getVisitor(), $handle, $eldnah) .
               '->' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Call_Function', $element, $handle, $eldnah);
    }

    /**
     * Visit a call static attribute.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitCallStaticAttribute ( Hoa_Visitor_Element $element,
                                               &$handle = null,
                                                $eldnah = null ) {

        return $element->getClass()->accept($this->getVisitor(), $handle, $eldnah) .
               '::' .
               $element->getAttribute()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a call static method.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitCallStaticMethod ( Hoa_Visitor_Element $element,
                                            &$handle = null,
                                             $eldnah = null ) {

        return $element->getClass()->accept($this->getVisitor(), $handle, $eldnah) .
               '::' .
               $element->getMethod()->accept($this->getVisitor(), $handle, $eldnah);
    }
}
