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
 * @subpackage  Hoa_Tokenizer_Token_Cast
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
 * Class Hoa_Tokenizer_Token_Cast.
 *
 * Represent an array (aïe, not easy …).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Cast
 */

class Hoa_Tokenizer_Token_Cast implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Cast value.
     *
     * @var Hoa_Tokenizer_Token_Cast string
     */
    protected $_value = null;

    /**
     * Cast token.
     *
     * @var Hoa_Tokenizer_Token_Cast int
     */
    protected $_token = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $type    Cast value/type name.
     * @return  void
     */
    public function __construct ( $type ) {

        $this->castTo($type);

        return;
    }

    /**
     * Set the cast value/type name.
     *
     * @access  public
     * @param   string  $type    Cast value/type name.
     * @return  string
     */
    public function castTo ( $type ) {

        $old = $this->_value;

        switch($type) {

            case 'array':
                $this->_value = 'array';
                $this->_token = Hoa_Tokenizer::_ARRAY_CAST;
              break;

            case 'bool':
            case 'boolean':
                $this->_value = 'bool';
                $this->_token = Hoa_Tokenizer::_BOOL_CAST;
              break;

            case 'double':
            case 'real':
            case 'float':
                $this->_value = 'double';
                $this->_token = Hoa_Tokenizer::_DOUBLE_CAST;
              break;

            case 'int':
                $this->_value = 'int';
                $this->_token = Hoa_Tokenizer::_INT_CAST;
              break;

            case 'object':
                $this->_value = 'object';
                $this->_token = Hoa_Tokenizer::_OBJECT_CAST;
              break;

            case 'string':
                $this->_value = 'string';
                $this->_token = Hoa_Tokenizer::_STRING_CAST;
              break;

            case 'unset':
                $this->_value = 'unset';
                $this->_token = Hoa_Tokenizer::_UNSET_CAST;
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Cast %s does not exist.', 0, $type);
        }

        return $old;
    }

    /**
     * Get the cast value/type name.
     *
     * @access  public
     * @return  string
     */
    public function getType ( ) {

        return $this->_value;
    }

    /**
     * Get the cast token.
     *
     * @access  public
     * @return  int
     */
    public function getToken ( ) {

        return $this->_token;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context    Context.
     * @return  array
     */
    public function toArray ( $context = Hoa_Tokenizer::CONTEXT_STANDARD ) {

        return array(
            array(
                0 => Hoa_Tokenizer::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            ),
            array(
                0 => $this->getToken(),
                1 => $this->getType(),
                2 => -1
            ),
            array(
                0 => Hoa_Tokenizer::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            ),
        );
    }
}
