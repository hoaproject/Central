
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
 * @subpackage  Hoa_Tokenizer_Token_Number_LNumber
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
 * Hoa_Tokenizer_Token_Number
 */
import('Tokenizer.Token.Number');

/**
 * Class Hoa_Tokenizer_Token_Number_LNumber.
 *
 * Represent a lnumber : integer, hexadecimal etc., i.e. ℤ.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Number_LNumber
 */

class Hoa_Tokenizer_Token_Number_LNumber extends Hoa_Tokenizer_Token_Number {

    /**
     * Value.
     *
     * @var Hoa_Tokenizer_Token_Number_LNumber int
     */
    protected $_value = 0;



    /**
     * Set number.
     *
     * @access  public
     * @param   mixed   $number    Number. Could be a string or a number.
     * @return  int
     */
    public function setNumber ( $number ) {

        $number  = (int) $number;
        $pattern = Hoa_Tokenizer_Token_Number::L_INT;

        if(0 === preg_match('#' . $pattern . '#', (string) $number))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'LNumber %d is not well-formed.', 0, $number);

        return parent::setNumber($number);
    }

    /**
     * Get number.
     *
     * @access  public
     * @return  int
     */
    public function getNumber ( ) {

        return (int) $this->_value;
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
            0 => Hoa_Tokenizer::_LNUMBER,
            1 => $this->getNumber(),
            2 => -1
        ));
    }
}
