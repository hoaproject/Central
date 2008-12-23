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
 * @subpackage  Hoa_Tokenizer_Token_String
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
 * Class Hoa_Tokenizer_Token_String.
 *
 * Represent a string (not a constant encapsed string !).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_String
 */

class Hoa_Tokenizer_Token_String implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Value.
     *
     * @var Hoa_Tokenizer_Token_String string
     */
    protected $_value = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  void
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function __construct ( $string ) {

        $this->setString($string);

        return;
    }

    /**
     * Set string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  string
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setString ( $string ) {

        if(0 === preg_match('#[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*#', $string))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'String %s is not well-formed.', 0, $string);

        $old          = $this->_value;
        $this->_value = $string;

        return $old;
    }

    /**
     * Get string.
     *
     * @access  public
     * @return  string
     */
    public function getString ( ) {

        return $this->_value;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context     Context.
     * @return  array
     */
    public function toArray ( $context = Hoa_Tokenizer::CONTEXT_STANDARD ) {

        if(0 === preg_match('#[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*#', $this->getString()))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'String %s is not well-formed, cannot return an array.', 1,
                $this->getString());

        return array(array(
            0 => Hoa_Tokenizer::_STRING,
            1 => $this->getString(),
            2 => -1
        ));
    }
}
