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
 * @subpackage  Hoa_Tokenizer_Token_Function_Named
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
 * Hoa_Tokenizer_Token_Function
 */
import('Tokenizer.Token.Function');

/**
 * Class Hoa_Tokenizer_Token_Function_Named.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Function_Named
 */

class Hoa_Tokenizer_Token_Function_Named extends Hoa_Tokenizer_Token_Function {

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context    Context.
     * @return  array
     */
    public function tokenize ( ) {

        $argSet    = false;
        $arguments = array();
        $body      = array();

        foreach($this->getArguments as $i => $argument) {

            if(true === $argSet) {

                $arguments[] = array(array(
                    0 => Hoa_Tokenizer::_COMMA,
                    1 => ',',
                    2 => -1
                ));
            }
            else
                $argSet      = true;

            $arguments[]     = $argument->tokenize();
        }

        foreach($this->getBody as $i => $b)
            $body[] = $b->tokenize();

        return array_merge(
            $this->getComment()->tokenize(),
            (
             true === $this->_isReferenced
                 ? array(array(
                       0 => Hoa_Tokenizer::_REFERENCE,
                       1 => '&',
                       3 => -1
                   ))
                 : array(array())
            ),
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
            )),
            array(array(
                0 => Hoa_Tokenizer::_OPEN_BRACE,
                1 => '{',
                2 => -1
            )),
            $body,
            array(array(
                0 => Hoa_Tokenizer::_CLOSE_BRACE,
                1 => '}',
                2 => -1
            ))
        );
    }
}
