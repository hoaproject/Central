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
 * @subpackage  Hoa_Tokenizer_Token_String_Encapsed
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
 * Hoa_Tokenizer_Token_Util_Interface_Tokenizable
 */
import('Tokenizer.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Class Hoa_Tokenizer_Token_String_Encapsed.
 *
 * Represent an encapsed string.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_String_Encapsed
 */

class Hoa_Tokenizer_Token_String_Encapsed implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Sequence of elements that constitute an encapsed string.
     *
     * @var Hoa_Tokenizer_Token_String_Encapsed array
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
     * @param   mixed   $element    Element to add.
     * @return  array
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function addElement ( $element ) {

        switch(get_class($element)) {

            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_String': // Boolean, Constant, Null,
                                               // EncapsedConstant.
            case 'Hoa_Tokenizer_Token_String_Encapsed':
            case 'Hoa_Tokenizer_Token_Number': // DNumber, LNumber.
            case 'Hoa_Tokenizer_Token_Variable':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Operation':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'A constant encapsed string cannot accept a class that ' .
                    'is an instance of %s.', 0, get_class($element));
        }

        return $this->_sequence[] = $element;
    }

    /**
     * Get the complete sequence.
     *
     * @access  protected
     * @return  array
     */
    protected function getSequence ( ) {

        return $this->_sequence;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        $array = array();
        $set   = false;

        foreach($this->getSequence() as $i => $element) {

            if(   true === $set
               && !($element instanceof Hoa_Tokenizer_Token_Comment)) {

                $array[] = array(array(
                    0 => Hoa_Tokenizer::_POINT,
                    1 => '.',
                    2 => -1
                ));
            }
            else
                $set = true;

            $array[] = $element->tokenize();
        }

        return array_merge($array);
    }
}
