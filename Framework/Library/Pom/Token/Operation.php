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
 * @subpackage  Hoa_Pom_Token_Operation
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
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Operation.
 *
 * Represent an operation.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operation
 */

class Hoa_Pom_Token_Operation implements Hoa_Visitor_Element {

    /**
     * Sequence of elements that constitute an operation.
     *
     * @var Hoa_Pom_Token_Operation array
     */
    protected $_sequence = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $elements    Could be an instance of an element or a
     *                               collection of elements.
     * @return  void
     */
    public function __construct ( $elements = array() ) {

        $this->addElements((array) $elements);

        return;
    }

    /**
     * Add many elements.
     *
     * @access  public
     * @param   array   $elements    Add many elements to the sequence.
     * @return  array
     */
    public function addElements ( Array $elements = array() ) {

        foreach($elements as $i => $element)
            $this->addElement($element);

        return $this->getSequence();
    }

    /**
     * Add an element.
     *
     * @access  public
     * @param   mixed    $element    Element to add.
     * @return  array
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addElement ( $element ) {

        if(   !($element instanceof Hoa_Pom_Token_Array)
           && !($element instanceof Hoa_Pom_Token_Call)
           && !($element instanceof Hoa_Pom_Token_Cast)
           && !($element instanceof Hoa_Pom_Token_Clone)
           && !($element instanceof Hoa_Pom_Token_Comment)
           && !($element instanceof Hoa_Pom_Token_New)
           && !($element instanceof Hoa_Pom_Token_Number)
           && !($element instanceof Hoa_Pom_Token_Operator)
           && !($element instanceof Hoa_Pom_Token_String)
           && !($element instanceof Hoa_Pom_Token_Variable))
            throw new Hoa_Pom_Token_Util_Exception(
                'An operation cannot be composed by a class that ' .
                'is an instance of %s.', 0, get_class($element));

        return $this->_sequence[] = $element;
    }

    /**
     * Add an operator (i.e. add an element typed like an operator).
     *
     * @access  public
     * @param   Hoa_Pom_Token_Operator  $operator    Operator to add.
     * @return  array
     */
    public function addOperator ( Hoa_Pom_Token_Operator $operator ) {

        return $this->addElement($operator);
    }

    /**
     * Get the complete sequence.
     *
     * @access  public
     * @return  array
     */
    public function getSequence ( ) {

        return $this->_sequence;
    }
 
    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
