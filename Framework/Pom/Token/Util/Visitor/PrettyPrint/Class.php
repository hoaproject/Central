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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Class
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
 * Hoa_Pom_Token_Cast
 */
import('Pom.Token.Cast');

/**
 * Hoa_Pom_Token_Class
 */
import('Pom.Token.Class');

/**
 * Hoa_Pom_Token_Class_Access
 */
import('Pom.Token.Class.Access');

/**
 * Hoa_Pom_Token_Class_Attribute
 */
import('Pom.Token.Class.Attribute');

/**
 * Hoa_Pom_Token_Class_Constant
 */
import('Pom.Token.Class.Constant');

/**
 * Hoa_Pom_Token_Class_Method
 */
import('Pom.Token.Class.Method');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Class.
 *
 * Visit a class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_Class
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint_Class extends Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate {

    /**
     * Visit a class.
     *
     * @access  public
     * @return  string
     */
    public function visitClass ( Hoa_Visitor_Element $element, &$handle = null ) {

        $ifirst     = true;
        $interfaces = null;

        foreach($element->getInterfaces() as $i => $interface) {

            if(false === $ifirst)
                $interfaces .= ', ';
            else
                $ifirst      = false;

            $interfaces .= $interface->accept($this->getVisitor(), $handle);
        }

        $constants  = null;

        foreach($element->getConstants() as $i => $constant)
            $constants .= $constant->accept($this->getVisitor(), $handle) . "\n\n";

        $attributes = null;

        foreach($element->getAttributes() as $i => $attribute)
            $attributes .= $attribute->accept($this->getVisitor(), $handle) . "\n\n";

        $methods    = null;

        foreach($element->getMethods() as $i => $method)
            $methods .= $method->accept($this->getVisitor(), $handle);

        return
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle) . "\n\n"
                 : ''
            ) .
            (true === $element->isAbstract()
                 ? 'abstract '
                 : ''
            ) .
            (true === $element->isFinal()
                 ? 'final '
                 : ''
            ) .
            'class ' .
            $element->getName()->accept($this->getVisitor(), $handle) .
            ' ' .
            (true === $element->hasParent()
                 ? 'extends ' .
                   $element->getParent()->accept($this->getVisitor(), $handle)
                   . ' '
                 : ''
            ) .
            (true === $element->hasInterfaces()
                 ? 'implements ' . $interfaces . ' '
                 : ''
            ) .
            '{' . "\n\n" .
            $constants .
            $attributes .
            "\n\n" .
            $methods . "\n" .
            '}';
    }

    /**
     * Visit a class access.
     *
     * @access  public
     * @return  string
     */
    public function visitClassAccess ( Hoa_Visitor_Element $element, &$handle = null ) {

        return $element->getAccess();
    }

    /**
     * Visit a class attribute.
     *
     * @access  public
     * @return  string
     */
    public function visitClassAttribute ( Hoa_Visitor_Element $element, &$handle = null ) {

        return $this->indent(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle) . "\n"
                 : ''
            ) .
            $element->getAccess()->accept($this->getVisitor(), $handle) .
            ' ' .
            (true === $element->isStatic()
                 ? 'static '
                 : ''
            ) .
            $element->getName()->accept($this->getVisitor(), $handle) .
            (true === $element->hasValue()
                ? $element->getOperator()->accept($this->getVisitor(), $handle) .
                  $element->getValue()->accept($this->getVisitor(), $handle)
                : ''
            ) .
            ';'
        );
    }

    /**
     * Visit a class constant.
     *
     * @access  public
     * @return  string
     */
    public function visitClassConstant ( Hoa_Visitor_Element $element, &$handle = null ) {

        if(false === $element->hasValue())
            throw new Hoa_Pom_Token_Util_Exception(
                'A constant must have a value.', 0);

        return $this->indent(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle) . "\n"
                 : ''
            ) .
            'const ' .
            $element->getName()->accept($this->getVisitor(), $handle) .
            $element->getOperator()->accept($this->getVisitor(), $handle) .
            $element->getValue()->accept($this->getVisitor(), $handle) . ';'
        );
    }

    /**
     * Visit a class method.
     *
     * @access  public
     * @return  string
     */
    public function visitClassMethod ( Hoa_Visitor_Element $element, &$handle = null ) {

        return $this->indent(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle) . "\n"
                 : ''
            ) .
            (true === $element->isFinal()
                 ? 'final '
                 : ''
            ) .
            (true === $element->isAbstract()
                 ? 'abstract '
                 : ''
            ) .
            $element->getAccess()->accept($this->getVisitor(), $handle) .
            ' ' .
            (true === $element->isStatic()
                 ? 'static '
                 : ''
            ) .
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Function_Named', $element, $handle) .
            "\n"
        );
    }
}
