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
 * @subpackage  Hoa_Tokenizer_Token_Number
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
 * Hoa_Tokenizer_Token_Util_Interface_Scalar
 */
import('Tokenizer.Token.Util.Interface.Scalar');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Class Hoa_Tokenizer_Token_Number.
 *
 * Represent a number.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Number
 */

abstract class Hoa_Tokenizer_Token_Number implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable,
                                                     Hoa_Tokenizer_Token_Util_Interface_Scalar {

    /**
     * Pattern of a {DEC} : ([1-9][0-9]*) | 0.
     *
     * @const string
     */
    const L_DEC          = '([1-9][0-9]*)|0';

    /**
     * Pattern of a {HEXA} : 0[xX][0-9a-fA-F]+.
     *
     * @const string
     */
    const L_HEXA         = '0[xX][0-9a-fA-F]+';

    /**
     * Pattern of an {OCTA} : 0[0-7]+.
     *
     * @const string
     */
    const L_OCTA         = '0[0-7]+';

    /**
     * Pattern of an {INT} : ([+-]?{DEC}) | ([+-]?{HEXA}) | ([+-]?{OCTA}).
     *
     * @const string
     */
    const L_INT          = '([+-]?([1-9][0-9]*|0))|([+-]?0[xX][0-9a-fA-F]+)|([+-]?0[0-7]+)';

    /**
     * Pattern of a {LNUM} : [0-9]+.
     *
     * @const string
     */
    const D_LNUM         = '[0-9]+';

    /**
     * Pattern of a {DNUM} : ([0-9]*[\.]{LNUM}) | ({LNUM}[\.][0-9]*).
     *
     * @const string
     */
    const D_DNUM          = '([0-9]*[\.][0-9]+)|([0-9]+[\.][0-9]*)';

    /**
     * Pattern of an EXPONENT_DNUM : (({LNUM} | {DNUM}) [eE][+-]? {LNUM}).
     *
     * @const string
     */
    const D_EXPONENT_DNUM = '(([0-9]+|([0-9]*[\.][0-9]+)|([0-9]+[\.][0-9]*))[eE][+-]?[0-9]+)';

    /**
     * Value.
     *
     * @var Hoa_Tokenizer_Token_String mixed
     */
    protected $_value     = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $number    Number
     * @return  void
     */
    public function __construct ( $number ) {

        $this->setNumber($number);

        return;
    }

    /**
     * Set number.
     *
     * @access  public
     * @param   mixed   $number    Number.
     * @return  mixed
     */
    public function setNumber ( $number ) {

        $old          = $this->_value;
        $this->_value = $number;

        return $old;
    }

    /**
     * Get number.
     *
     * @access  public
     * @return  mixed
     */
    public function getNumber ( ) {

        return $this->_value;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context    Context.
     * @return  array
     */
    abstract public function tokenize ( );
}
