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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Function_Named
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom_Token_Util_Interface_Tokenizable
 */
import('Pom.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Function
 */
import('Pom.Token.Function');

/**
 * Class Hoa_Pom_Token_Function_Named.
 *
 * Represent a named function.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Function_Named
 */

class Hoa_Pom_Token_Function_Named extends    Hoa_Pom_Token_Function
                                   implements Hoa_Pom_Token_Util_Interface_Tokenizable {

    /**
     * Whether comment is enabled.
     *
     * @var Hoa_Pom_Token_Function_Named bool
     */
    protected $_commentEnabled = true;



    /**
     * Enable comment.
     *
     * @access  protected
     * @param   bool       $enable    Enable comment or not.
     * @return  bool
     */
    protected function enableComment ( $enable ) {

        $old                   = $this->_commentEnabled;
        $this->_commentEnabled = $enable;

        return $old;
    }

    /**
     * Check if comment is enabled or not.
     *
     * @access  protected
     * @return  bool
     */
    protected function isCommentEnabled ( ) {

        return $this->_commentEnabled;
    }

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

        foreach($this->getArguments() as $i => $argument) {

            if(true === $argSet)
                $arguments[] = array(
                    0 => Hoa_Pom::_COMMA,
                    1 => ',',
                    2 => -1
                );
            else
                $argSet      = true;

            foreach($argument->tokenize() as $key => $value)
                $arguments[] = $value;
        }

        foreach($this->getBody() as $i => $b)
            foreach($b->tokenize() as $key => $value)
                $body[] = $value;

        return array_merge(
            (true === $this->hasComment() && true === $this->isCommentEnabled()
                 ? $this->getComment()->tokenize()
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_FUNCTION,
                1 => 'function',
                2 => -1
            )),
            (true === $this->isReferenced()
                 ? array(array(
                       0 => Hoa_Pom::_REFERENCE,
                       1 => '&',
                       3 => -1
                   ))
                 : array()
            ),
            $this->getName()->tokenize(),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $arguments,
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_BRACE,
                1 => '{',
                2 => -1
            )),
            $body,
            array(array(
                0 => Hoa_Pom::_CLOSE_BRACE,
                1 => '}',
                2 => -1
            ))
        );
    }
}
