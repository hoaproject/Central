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
 * @subpackage  Hoa_Pom_Token_Function_Argument
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
 * Hoa_Pom_Token_Variable
 */
import('Pom.Token.Variable');

/**
 * Hoa_Pom_Token_Operator_Assignement
 */
import('Pom.Token.Operator.Assignement');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Function_Argument.
 *
 * Represent an argument of a function.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Function_Argument
 */

class Hoa_Pom_Token_Function_Argument implements Hoa_Visitor_Element {

    /**
     * Whether argument is passed by reference.
     *
     * @var Hoa_Pom_Token_Function_Argument bool
     */
    protected $_isReferenced = false;

    /**
     * Type (Array or class name).
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_type         = null;

    /**
     * Name.
     *
     * @var Hoa_Pom_Token_Variable object
     */
    protected $_name         = null;

    /**
     * Operator.
     *
     * @var Hoa_Pom_Token_Operator_Assign object
     */
    protected $_operator     = null;

    /**
     * Default (scalar) value.
     *
     * @var mixed object
     */
    protected $_default      = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $name    Argument name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_Variable $name ) {

        $this->setName($name);
        $this->setOperator();

        return;
    }

    /**
     * Reference this argument.
     *
     * @access  public
     * @param   bool    $active    Whether argument is passed by reference.
     * @return  bool
     */
    public function referenceMe ( $active = true ) {

        $old                 = $this->_isReferenced;
        $this->_isReferenced = $active;

        return $old;
    }

    /**
     * Set type.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $type    Argument type.
     * @return  Hoa_Pom_Token_String
     */
    public function setType ( Hoa_Pom_Token_String $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Set name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $name    Argument name.
     * @return  Hoa_Pom_Token_Variable
     */
    public function setName ( Hoa_Pom_Token_Variable $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Set operator.
     *
     * @access  protected
     * @return  Hoa_Pom_Token_Operator_Assignement
     */
    protected function setOperator ( ) {

        $old             = $this->_operator;
        $this->_operator = new Hoa_Pom_Token_Operator_Assignement('=');

        return $old;
    }

    /**
     * Set default value.
     *
     * @access  public
     * @param   mixed   $default    Default argument.
     * @return  mixed
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function setDefaultValue ( $default ) {

        if($default instanceof Hoa_Pom_Token_Util_Interface_SuperScalar) {

            if(false === $default->isUniformSuperScalar())
                throw new Hoa_Pom_Token_Util_Exception(
                    'Default value should effectively be a super-scalar, ' .
                    'but a uniform super-scalar, given %s.',
                    0, get_class($default));
        }
        elseif(!($default instanceof Hoa_Pom_Token_Util_Interface_Scalar))
            throw new Hoa_Pom_Token_Util_Exception(
                'Default value must be a scalar or a uniform super-scalar, given %s.',
                1, get_class($default));

        $old            = $this->_default;
        $this->_default = $default;

        return $old;
    }

    /**
     * Check if argument is typed or not.
     *
     * @access  public
     * @return  bool
     */
    public function isTyped ( ) {

        return null !== $this->_type;
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
     * Whether if returned values are given by reference.
     *
     * @access  public
     * @return  bool
     */
    public function isReferenced ( ) {

        return $this->_isReferenced;
    }

    /**
     * Get name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Variable
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get operator.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Operator_Assignement
     */
    public function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Check if argument has a default value or not.
     *
     * @access  public
     * @return  bool
     */
    public function hasDefaultValue ( ) {

        return null !== $this->_default;
    }

    /**
     * Get default value.
     *
     * @access  public
     * @return  mixed
     */
    public function getDefaultValue ( ) {

        return $this->_default;
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
