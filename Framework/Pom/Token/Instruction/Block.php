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
 * @subpackage  Hoa_Pom_Token_Instruction_Block
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
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Instruction
 */
import('Pom.Token.Instruction');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Instruction_Block.
 *
 * Represent a collection/block of instructions.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Instruction_Block
 */

class Hoa_Pom_Token_Instruction_Block implements Hoa_Visitor_Element {

    /**
     * Force to write braces.
     *
     * @const int
     */
    const FORCE_BRACES     = 0;

    /**
     * Do Not write braces.
     *
     * @const int
     */
    const SKIP_BRACES      = 1;

    /**
     * Determine if braces are needed or not.
     *
     * @const int
     */
    const DETERMINE_BRACES = 2;

    /**
     * If no instruction is given, write a semi-colon.
     *
     * @const int
     */
    const SEMI_COLON_EMPTY = 4;

    /**
     * If no instruction is given, write an open and a close brace.
     *
     * @const int
     */
    const BRACE_EMPTY      = 8;

    /**
     * If no instruction is given, write nothing particular.
     *
     * @const int
     */
    const NOTHING_EMPTY    = 16;

    /**
     * Collection of instructions.
     *
     * @var Hoa_Pom_Token_Instruction_Block array
     */
    protected $_instructions = array();

    /**
     * Braces mode. Given by constants self::*_BRACES.
     *
     * @var Hoa_Pom_Token_Instruction_Block int
     */
    protected $_braces       = self::DETERMINE_BRACES;

    /**
     * Empty mode. Given by constants self::*_EMPTY.
     *
     * @var Hoa_Pom_Token_Instruction_Block int
     */
    protected $_empty        = self::SEMI_COLON_EMPTY;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $instructions    One or more instructions.
     * @return  void
     */
    public function __construct ( $instructions = array() ) {

        $this->addInstructions((array) $instructions);

        return;
    }

    /**
     * Add many instructions.
     *
     * @access  public
     * @param   array   $instructions    Many instructions to add.
     * @return  array
     */
    public function addInstructions ( Array $instructions = array() ) {

        foreach($instructions as $i => $instruction)
            $this->addInstruction($instruction);

        return $this->_instructions;
    }

    /**
     * Add an instruction.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Instruction  $instruction    Instruction to
     *                                                     add.
     * @return  Hoa_Pom_Token_Instruction
     */
    public function addInstruction ( Hoa_Pom_Token_Instruction $instruction ) {

        return $this->_instructions[] = $instruction;
    }

    /**
     * Remove an instruction.
     *
     * @access  public
     * @param   int     $i    Instruction number.
     * @return  array
     */
    public function removeInstruction ( $i ) {

        unset($this->_instructions[$i]);

        return $this->_instructions;
    }

    /**
     * Remove all instructions.
     *
     * @access  public
     * @return  array
     */
    public function removeInstructions ( ) {

        $old                 = $this->_instructions;

        foreach($this->_instructions as $i => $instruction)
            unset($this->_instructions[$i]);

        $this->_instructions = array();

        return $old;
    }

    /**
     * Set braces mode.
     *
     * @access  public
     * @param   int     $mode    Given by constants self::*_BRACES.
     * @return  int
     */
    public function setBracesMode ( $mode = self::DETERMINE_BRACES ) {

        $old           = $this->_braces;
        $this->_braces = $mode;

        return $old;
    }

    /**
     * Set empty mode (when no instruction is given).
     *
     * @access  public
     * @param   int     $mode    Given by constants self::*_EMPTY.
     * @return  int
     */
    public function setEmptyMode ( $mode = self::SEMI_COLON_EMTPY ) {

        $old          = $this->_empty;
        $this->_empty = $mode;

        return $old;
    }

    /**
     * Get an instruction.
     *
     * @access  public
     * @param   int     $i     Instruction number.
     * @return  Hoa_Pom_Token_Instruction
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function getInstruction ( $i ) {

        if(!isset($this->_instructions[$i]))
            throw new Hoa_Pom_Token_Util_Exception(
                'Instruction number %d does not exist.', 0, $i);

        return $this->_instructions[$i];
    }

    /**
     * Get all instructions.
     *
     * @access  public
     * @return  array
     */
    public function getInstructions ( ) {

        return $this->_instructions;
    }

    /**
     * Get braces mode.
     *
     * @access  public
     * @return  int
     */
    public function getBracesMode ( ) {

        return $this->_braces;
    }

    /**
     * Get empty mode.
     *
     * @access  public
     * @return  int
     */
    public function getEmptyMode ( ) {

        return $this->_empty;
    }
 
    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              $handle     Handle (reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor, &$handle = null ) {

        return $visitor->visit($this);
    }
}
