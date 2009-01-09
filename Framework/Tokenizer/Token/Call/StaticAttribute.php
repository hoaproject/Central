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
 * @subpackage  Hoa_Tokenizer_Token_Call_StaticAttribute
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
 * Class Hoa_Tokenizer_Token_Call_StaticAttribute.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Call_StaticAttribute
 */

class Hoa_Tokenizer_Token_Call_StaticAttribute extends    Hoa_Tokenizer_Token_Call
                                               implements Hoa_Tokenizer_Token_Util_Interface_SuperScalar {

    /**
     * Class name.
     *
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_class     = null;

    /**
     * Attribute name.
     *
     * @var Hoa_Tokenizer_Token_Variable object
     */
    protected $_attribute = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $class    Class name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $class ) {

        $this->setClass($class);

        return;
    }

    /**
     * Set class name.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $class    Class name.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setClass ( Hoa_Tokenizer_Token_String $class ) {

        $old          = $this->_class;
        $this->_class = $class;

        return $old;
    }

    /**
     * Set attribute name.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Variable  $attribute    Attribute name.
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function setAttribute ( Hoa_Tokenizer_Token_Variable $attribute ) {

        $old              = $this->_attribute;
        $this->_attribute = $attribute;

        return $old;
    }

    /**
     * Get class name.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getClass ( ) {

        return $this->_class;
    }

    /**
     * Get attribute name.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getAttribute ( ) {

        return $this->_attribute;
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

        return array_merge(
            $this->getClass()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_DOUBLE_COLON,
                1 => '::',
                2 => -1
            )),
            $this->getAttribute()->tokenize()
        );
    }
}
