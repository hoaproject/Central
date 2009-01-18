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
 * @subpackage  Hoa_Tokenizer_Token_Call_Function
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
 * Hoa_Tokenizer_Token_Util_Interface_SuperScalar
 */
import('Tokenizer.Token.Util.Interface.SuperScalar');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Call
 */
import('Tokenizer.Token.Call');

/**
 * Class Hoa_Tokenizer_Token_Call_Function.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Call_Function
 */

class Hoa_Tokenizer_Token_Call_Function extends    Hoa_Tokenizer_Token_Call
                                        implements Hoa_Tokenizer_Token_Util_Interface_SuperScalar {

    /**
     * Function name.
     *
     * @var mixed object
     */
    protected $_name      = null;

    /**
     * List of arguments.
     *
     * @var Hoa_Tokenizer_Token_Call_Function array
     */
    protected $_arguments = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $name    Function name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set function name.
     *
     * @access  public
     * @param   mixed   $name    Function name.
     * @return  mixed
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setName ( Hoa_Tokenizer_Token_String $name ) {

        switch(get_class($method)) {

            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_Variable':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'A static method should only be called by a string or a ' .
                    'variable. Given %s.', 0, $method);
        }

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Add many arguments.
     *
     * @access  public
     * @param   array   $arguments    Arguments to add.
     * @return  array
     */
    public function addArguments ( Array $arguments ) {

        foreach($arguments as $i => $argument)
            $this->addArgument($argument);

        return $this->_arguments;
    }

    /**
     * Add an argument.
     *
     * @access  public
     * @param   mixed   $argument    Argument to add.
     * @return  array
     */
    public function addArgument ( $argument ) {

        switch(get_class($argument)) {

            case 'Hoa_Tokenizer_Token_Array':
            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_String_Encapsed':
            case 'Hoa_Tokenizer_Token_Variable_Valued':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Cannot call a function with a %s in argument', 0,
                    get_class($argument));
        }

        $this->_arguments[] = $argument;

        return $this->_arguments;
    }

    /**
     * Remove the n-th argument.
     *
     * @access  public
     * @param   int     $n    Argument number to remove.
     * @return  void
     */
    public function removeArgument ( $n ) {

        unset($this->_arguments[$n]);

        return;
    }

    /**
     * Get name.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get arguments.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
    }

    /**
     * Check if a data is an uniform super-scalar or not.
     *
     * @access  public
     * @return  bool
     */
    public function isUniformSuperScalar ( ) {

        return false;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        $arguments = array();
        $argSet    = false;

        foreach($this->getArguments() as $i => $argument) {

            if(true === $argSet) {

                $arguments[] = array(
                    0 => Hoa_Tokenizer::_COMMA,
                    1 => ',',
                    2 => -1
                );
            }
            else
                $argSet = true;

            foreach($argument->tokenize() as $key => $value)
                $arguments[] = $value;
        }

        return array_merge(
            $this->getName()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $arguments,
            array(array(
                0 => Hoa_Tokenizer::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            ))
        );
    }
}
