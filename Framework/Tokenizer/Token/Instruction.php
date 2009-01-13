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
 * @subpackage  Hoa_Tokenizer_Token_Instruction
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
 * Class Hoa_Tokenizer_Token_Instruction.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Instruction
 */

class Hoa_Tokenizer_Token_Instruction implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Instruction.
     *
     * @var mixed object
     */
    protected $_instruction = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $instruction    Instruction.
     * @return  void
     */
    public function __construct ( $instruction ) {

        $this->setInstruction($instruction);

        return;
    }

    /**
     * Set instruction.
     *
     * @access  public
     * @param   mixed   $instruction    Instruction.
     * @return  mixed
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setInstruction ( $instruction ) {

        switch(get_class($instruction)) {

            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_ControlStructure_Conditional_If':
            case 'Hoa_Tokenizer_Token_ControlStructure_Conditional_Switch':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'An instruction cannot accept a class that is an ' .
                    'instance of %s.', 0, get_class($instruction));
        }

        $old                = $this->_instruction;
        $this->_instruction = $instruction;

        return $old;
    }

    /**
     * Get instruction.
     *
     * @access  public
     * @return  mixed
     */
    public function getInstruction ( ) {

        return $this->_instruction;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            $this->getInstruction()->tokenize(),
            array(array(
                0 => Hoa_Tokenizer::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }
}
