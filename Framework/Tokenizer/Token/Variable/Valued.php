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
 * @subpackage  Hoa_Tokenizer_Token_Variable_Valued
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
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Variable
 */
import('Tokenizer.Token.Variable');

/**
 * Class Hoa_Tokenizer_Token_Variable_Valued.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Variable_Valued
 */

class Hoa_Tokenizer_Token_Variable_Valued extends Hoa_Tokenizer_Token_Variable {

    /**
     * Operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Assignement object
     */
    protected $_operator = null;

    /**
     * Value.
     *
     * @var mixed object
     */
    protected $_value    = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $name    Variable name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

        $this->setOperator(new Hoa_Tokenizer_Token_Operator_Assignement('='));

        return parent::__construct($name);
    }

    /**
     * Set operator.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Operator_Assignement  $operator    Operator.
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    public function setOperator ( Hoa_Tokenizer_Token_Operator_Assignement $operator ) {

        $old             = $this->_operator;
        $this->_operator = $operator;

        return $old;
    }

    /**
     * Set value.
     *
     * @access  public
     * @param   mixed   $value    Variable's value.
     * @return  mixed
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setValue ( $value ) {

        switch(get_class($value)) {

            case 'Hoa_Tokenizer_Token_Array':
            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Cast':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_Variable':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'A variable cannot accept in value a class that is %s.', 0,
                    get_class($value));
        }

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get operator.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    public function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            parent::tokenize(),
            $this->getOperator(),
            $this->getValue()
        );
    }
}
