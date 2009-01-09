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
 * Hoa_Tokenizer_Token_Util_Interface_Tokenizable
 */
import('Tokenizer.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Tokenizer_Token_Util_Interface_SuperScalar
 */
import('Tokenizer.Token.Util.Interface.SuperScalar');

/**
 * Hoa_Tokenizer_Token_Util_Interface_Type
 */
import('Tokenizer.Token.Util.Interface.Type');

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

class Hoa_Tokenizer_Token_Array implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable,
                                           Hoa_Tokenizer_Token_Util_Interface_SuperScalar,
                                           Hoa_Tokenizer_Token_Util_Interface_Type {

    /**
     * Represent a key of an array.
     *
     * @const int
     */
    const KEY   = 0;

    /**
     * Represent a value of an array.
     *
     * @const int
     */
    const VALUE = 0;

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
            $this->addElement($element[self::KEY], $element[self::VALUE]);

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

        switch(get_class($key)) {

            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Variable':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Operation':
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
     * Empty this array.
     *
     * @access  public
     * @return  array
     */
    public function emptyMe ( ) {

        $old          = $this->_array;
        $this->_array = array();

        return $old;
    }

    /**
     * Check if this array is empty or not.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty ( ) {

        return $this->getArray() == array();
    }

    /**
     * Check if a data is an uniform super-scalar or not.
     *
     * @access  public
     * @return  bool
     */
    public function isUniformSuperScalar ( ) {

        $old     = null;
        $current = null;

        foreach($this->getArray() as $i => $entry) {

            if($entry instanceof Hoa_Tokenizer_Token_Util_Interface_Scalar)
                if($entry->isUniformSuperScalar())
                    continue;
                else
                    return false;

            if(!($entry instanceof Hoa_Tokenizer_Token_Util_Interface_Scalar))
                return false;

            if(null === $old) {

                $old = get_class($entry);
                continue;
            }

            $current = get_class($entry);

            if($current != $old)
                return false;

            $old = $current;
        }

        return true;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array(array(

        ));
    }
}
