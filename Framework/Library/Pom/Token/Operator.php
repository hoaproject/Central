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
 * @subpackage  Hoa_Pom_Token_Operator
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
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Operator.
 *
 * Represent an operator.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Operator
 */

abstract class Hoa_Pom_Token_Operator implements Hoa_Visitor_Element {

    /**
     * Arity : whether operator is unary.
     *
     * @const int
     */
    const UNARY  = 0;

    /**
     * Arity : whether operator is binary.
     *
     * @const int
     */
    const BINARY = 1;

    /**
     * Arity : whether operator should be unary or binary (for example : minus).
     *
     * @const int
     */
    const MIXED  = 2;

    /**
     * Operator.
     *
     * @var Hoa_Pom_Token_Operator string
     */
    protected $_operator   = null;

    /**
     * Operator type.
     *
     * @var Hoa_Pom_Token_Operator mixed
     */
    protected $_type       = null;

    /**
     * Operator arity.
     *
     * @var Hoa_Pom_Token_Operator int
     */
    protected $_arity      = 0;

    /**
     * Operator precedence.
     *
     * @var Hoa_Pom_Token_Operator int
     */
    protected $_precedence = -1;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $operator    Operator.
     * @return  void
     */
    public function __construct ( $operator ) {

        $this->setOperator($operator);

        return;
    }

    /**
     * Set operator.
     *
     * @access  public
     * @param   string  $operator    Operator.
     * @return  string
     */
    public function setOperator ( $operator ) {

        $old             = $this->_operator;
        $this->_operator = $operator;

        return $old;
    }

    /**
     * Set type.
     *
     * @access  protected
     * @param   mixed      $type    Type of operator.
     * @return  mixed
     */
    protected function setType ( $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Set arity.
     *
     * @access  protected
     * @param   int        $arity    Arity of operator.
     * @return  int
     */
    protected function setArity ( $arity ) {

        $old          = $this->_arity;
        $this->_arity = $arity;

        return $old;
    }

    /**
     * Set precedence.
     *
     * @access  protected
     * @param   int        $precedence    Predence of operator.
     * @return  int
     */
    protected function setPrecedence ( $precedence ) {

        $old               = $this->_precedence;
        $this->_precedence = $precedence;

        return $old;
    }

    /**
     * Get operator.
     *
     * @access  public
     * @return  string
     */
    public function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Get type.
     *
     * @access  public
     * @return  mixed
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Get arity.
     *
     * @access  public
     * @return  int
     */
    public function getArity ( ) {

        return $this->_arity == self::UNARY
                   ? 1
                   : 2;
    }

    /**
     * Get precedence.
     *
     * @access  public
     * @return  int
     */
    public function getPrecedence ( ) {

        return $this->_precedence;
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
