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
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Function_Argument
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Token_Util_Exception
 */
import('Tokenizer.Token.Util.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Interface
 */
import('Tokenizer.Token.Util.Interface');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Operator_Assign
 */
import('Tokenizer.Token.Operator.Assign');

/**
 * Class Hoa_Tokenizer_Token_Function_Argument.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Function_Argument
 */

class Hoa_Tokenizer_Token_Function_Argument implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Whether argument is passed by reference.
     *
     * @var Hoa_Tokenizer_Token_Function_Argument bool
     */
    protected $_isReferenced = false;

    /**
     * Type (Array or class name).
     *
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_type         = null;

    /**
     * Name.
     *
     * @var Hoa_Tokenizer_Token_Variable object
     */
    protected $_name         = null;

    /**
     * Operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Assign object
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
     * @param   Hoa_Tokenizer_Token_Variable  $name    Argument name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_Variable $name ) {

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
     * @param   Hoa_Tokenizer_Token_String  $type    Argument type.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setType ( Hoa_Tokenizer_Token_String $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Set name.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Variable  $name    Argument name.
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function setName ( Hoa_Tokenizer_Token_Variable $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Set operator.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Operator_Assign
     */
    protected function setOperator ( ) {

        $old             = $this->_operator;
        $this->_operator = new Hoa_Tokenizer_Token_Operator_Assign('=');

        return $old;
    }

    /**
     * Set default value.
     *
     * @access  public
     * @param   mixed   $default    Default argument.
     * @return  mixed
     */
    public function setDefaultValue ( $default ) {

        switch(get_class($default)) {

            case 'Hoa_Tokenizer_Token_Array':
                if(false === $default->isScalar())
                    throw new Hoa_Tokenizer_Token_Util_Exception(
                        'Default value must be scalar.', 0);
              break;

            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_String_EncapsedString':
            case 'Hoa_Tokenizer_Token_Call_ClassConstant':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Default value must be scalar.', 1);
        }

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
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get operator.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Operator_Assign
     */
    protected function getOperator ( ) {

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
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context    Context.
     * @return  array
     */
    public function toArray ( $context = Hoa_Tokenizer::CONTEXT_STANDARD ) {

        return array_merge(
            (
             true === $this->isTyped()
                 ? (
                    strtolower($this->getType()->getString()) == 'array'
                        ? array(array(
                              0 => Hoa_Tokenizer::_ARRAY,
                              1 => $this->getType()->getString(),
                              2 => -1
                          ))
                        : $this->getType()->toArray()
                   )
                 : array(array())
            ),
            (
             true === $this->isReferenced()
                 ? array(array(
                       0 => Hoa_Tokenizer::_REFERENCE,
                       1 => '&',
                       2 => -1
                   ))
                 : array(array())
            ),
            $this->getName()->toArray(),
            (
             true === $this->hasDefaultValue()
                 ? array(array_merge(
                       $this->getOperator()->toArray(),
                       $this->getDefaultValue()->toArray()
                   ))
                 : array(array())
            )
        );
    }
}
