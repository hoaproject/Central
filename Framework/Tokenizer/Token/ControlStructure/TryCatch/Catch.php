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
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_TryCatch_Catch
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
 * Hoa_Tokenizer_Token_Instruction_Block
 */
import('Tokenizer.Token.Instruction.Block');

/**
 * Class Hoa_Tokenizer_Token_ControlStructure_TryCatch_Catch.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_TryCatch_Catch
 */

class          Hoa_Tokenizer_Token_ControlStructure_TryCatch_Catch
    extends    Hoa_Tokenizer_Token_Instruction_Block
    implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Type of exception.
     *
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_type     = null;

    /**
     * Variable that will receive the thrown exception.
     *
     * @var Hoa_Tokenizer_Token_Variable object
     */
    protected $_variable = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String    $type        Type of exception.
     * @param   Hoa_Tokenizer_Token_Variable  $variable    Variable.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String   $type,
                                  Hoa_Tokenizer_Token_Variable $variable ) {

        parent::setBracesMode(parent::FORCE_BRACES);

        $this->setType($type);
        $this->setVariable($type);

        return;
    }

    /**
     * Set type of exception.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $type    Type of exception.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setType ( Hoa_Tokenizer_Token_String $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Set variable that will receive the exception.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Variable  $variable    Variable.
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function setVariable ( Hoa_Tokenizer_Token_Variable $variable ) {

        $old             = $this->_variable;
        $this->_variable = $variable;

        return $old;
    }

    /**
     * Get type.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Get variable.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function getVariable ( ) {

        return $this->_variable;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            array(array(
                0 => Hoa_Tokenizer::_CATCH,
                1 => 'catch',
                2 => -1
            )),
            array(array(
                0 => Hoa_Tokenizer::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $this->getType()->tokenize(),
            $this->getVariable()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            parent::tokenize()
        );
    }
}
