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
 * @subpackage  Hoa_Tokenizer_Token_Array
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
 * Class Hoa_Tokenizer_Token_Array.
 *
 * Represent an array (aïe, not easy …).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Array
 */

class Hoa_Tokenizer_Token_Array implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Set of key/value that constitute an array.
     *
     * @var Hoa_Tokenizer_Token_Array array
     */
    protected $_array = array();



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
     * @param   array   $elements    Add many elements to the array.
     * @return  array
     */
    public function addElements ( Array $elements = array() ) {

        foreach($elements as $i => $element)
            $this->addElement($element[0], $element[1]);

        return $this->getArray();
    }

    /**
     * Add an element.
     *
     * @access  public
     * @param   mixed   $key      Key to add.
     * @param   mixed   $value    Value to add.
     * @return  array
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function addElement ( $key, $value ) {

        // to be completed.

        switch(get_class($key)) {

            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_String': // Boolean, Constant, Null,
                                               // EncapsedConstant.
            //case 'Hoa_Tokenizer_Token_Array':
            case 'Hoa_Tokenizer_Token_Number': // DNumber, LNumber.
            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Variable':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Operator':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'A constant encapsed string cannot accept a class that ' .
                    'is an instance of %s.', 0, $element);
        }

        return $this->_array[] = $element;
    }

    /**
     * Get the complete array.
     *
     * @access  protected
     * @return  array
     */
    protected function getArray ( ) {

        return $this->_array;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context    Context.
     * @return  array
     */
    public function toArray ( $context = Hoa_Tokenizer::CONTEXT_STANDARD ) {

        return array(array(

        ));
    }
}
