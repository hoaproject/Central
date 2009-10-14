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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Class
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
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_Class.
 *
 * Visit a class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Class
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_Class extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit a class.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitClass ( Hoa_Visitor_Element $element,
                                 &$handle = null,
                                  $eldnah = null ) {

        $ifirst     = true;
        $interfaces = array();

        foreach($element->getInterfaces() as $i => $interface) {

            if(false === $ifirst)
                $interfaces[] = array(
                    0 => Hoa_Pom::_COMMA,
                    1 => ',',
                    2 => -1
                );
            else
                $ifirst = false;

            $h            = $interface->accept($this->getVisitor(), $handle, $eldnah);
            $interfaces[] = $h[0];
        }

        $constants  = array();

        foreach($element->getConstants() as $i => $constant)
            foreach($constant->accept($this->getVisitor(), $handle, $eldnah) as $key => $value)
                $constants[] = $value;

        $attributes = array();

        foreach($element->getAttributes() as $i => $attribute)
            foreach($attribute->accept($this->getVisitor(), $handle, $eldnah) as $key => $value)
                $attributes[] = $value;

        $methods    = array();

        foreach($element->getMethods() as $i => $method)
            foreach($method->accept($this->getVisitor(), $handle, $eldnah) as $key => $value)
                $methods[] = $value;

        return array_merge(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle, $eldnah)
                 : array()
            ),
            (true === $element->isAbstract()
                 ? array(array(
                       0 => Hoa_Pom::_ABSTRACT,
                       1 => 'abstract',
                       2 => -1
                   ))
                 : array()
            ),
            (true === $element->isFinal()
                 ? array(array(
                       0 => Hoa_Pom::_FINAL,
                       1 => 'final',
                       2 => -1
                   ))
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_CLASS,
                1 => 'class',
                2 => -1
            )),
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah),
            (true === $element->hasParent()
                 ? array_merge(
                       array(array(
                           0 => Hoa_Pom::_EXTENDS,
                           1 => 'extends',
                           2 => -1
                       )),
                       $element->getParent()->accept($this->getVisitor(), $handle, $eldnah)
                   )
                 : array()
            ),
            (true === $element->hasInterfaces()
                 ? array_merge(
                       array(array(
                           0 => Hoa_Pom::_IMPLEMENTS,
                           1 => 'implements',
                           2 => -1
                       )),
                       $interfaces
                   )
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_OPEN_BRACE,
                1 => '{',
                2 => -1
            )),
            $constants,
            $attributes,
            $methods,
            array(array(
                0 => Hoa_Pom::_CLOSE_BRACE,
                1 => '}',
                2 => -1
            ))
        );
    }

    /**
     * Visit a class access.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitClassAccess ( Hoa_Visitor_Element $element,
                                       &$handle = null,
                                        $eldnah = null ) {

        return array(array(
            $element->getType(),
            $element->getAccess(),
            -1
        ));
    }

    /**
     * Visit a class attribute.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitClassAttribute ( Hoa_Visitor_Element $element,
                                          &$handle = null,
                                           $eldnah = null ) {

        return array_merge(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle, $eldnah)
                 : array()
            ),
            $element->getAccess()->accept($this->getVisitor(), $handle, $eldnah),
            (true === $element->isStatic()
                 ? array(array(
                       0 => Hoa_Pom::_STATIC,
                       1 => 'static',
                       2 => -1
                   ))
                 : array()
            ),
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah),
            (true === $element->hasValue()
                ? array_merge(
                      $element->getOperator()->accept($this->getVisitor(), $handle, $eldnah),
                      $element->getValue()->accept($this->getVisitor(), $handle, $eldnah)
                  )
                : array()
            ),
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }

    /**
     * Visit a class constant.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitClassConstant ( Hoa_Visitor_Element $element,
                                         &$handle = null,
                                          $eldnah = null ) {

        if(false === $element->hasValue())
            throw new Hoa_Pom_Token_Util_Exception(
                'A constant must have a value.', 0);

        return array_merge(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle, $eldnah)
                 : array()
            ),
            array(array(
                Hoa_Pom::_CONST,
                'const',
                -1
            )),
            $element->getName()->accept($this->getVisitor(), $handle, $eldnah),
            $element->getOperator()->accept($this->getVisitor(), $handle, $eldnah),
            $element->getValue()->accept($this->getVisitor(), $handle, $eldnah),
            array(array(
                Hoa_Pom::_SEMI_COLON,
                ';',
                -1
            ))
        );
    }

    /**
     * Visit a class method.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitClassMethod ( Hoa_Visitor_Element $element,
                                       &$handle = null,
                                        $eldnah = null ) {

        return array_merge(
            (true === $element->hasComment()
                 ? $element->getComment()->accept($this->getVisitor(), $handle, $eldnah)
                 : array()
            ),
            (true === $element->isFinal()
                 ? array(array(
                       0 => Hoa_Pom::_FINAL,
                       1 => 'final',
                       2 => -1
                   ))
                 : array()
            ),
            (true === $element->isAbstract()
                 ? array(array(
                       0 => Hoa_Pom::_ABSTRACT,
                       1 => 'abstract',
                       2 => -1
                   ))
                 : array()
            ),
            $element->getAccess()->accept($this->getVisitor(), $handle, $eldnah),
            (true === $element->isStatic()
                 ? array(array(
                       0 => Hoa_Pom::_STATIC,
                       1 => 'static',
                       2 => -1
                   ))
                 : array()
            ),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Function_Named', $element, $handle, $eldnah)
        );
    }
}
