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
 * @subpackage  Hoa_Tokenizer_Token_String_Null
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
 * Hoa_Tokenizer_Token_String
 */
import('Tokenizer.Token.String');

/**
 * Class Hoa_Tokenizer_Token_String_Null.
 *
 * Represent a null value.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_String_Null
 */

class Hoa_Tokenizer_Token_String_Null extends Hoa_Tokenizer_Token_String {

    /**
     * Name.
     *
     * @var Hoa_Tokenizer_Token_String_Null string
     */
    protected $_name = 'null';



    /**
     * Set string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  string
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setString ( $string ) {

        if(empty($string))
            $string = 'null';

        if(strtolower($string) != 'null')
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Null cannot be different of null. Given %s.', 0, $string);

        return 'null';
    }
}
