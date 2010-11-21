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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Instruction
 *
 */

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
 * Hoa_Pom_Token_Instruction_Block
 */
import('Pom.Token.Instruction.Block');

/**
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_Instruction.
 *
 * Visit an instruction.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Instruction
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_Instruction extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit an instruction.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitInstruction ( Hoa_Visitor_Element $element,
                                       &$handle = null,
                                        $eldnah = null ) {

        return array_merge(
            $element->getInstruction()->accept($this->getVisitor(), $handle, $eldnah),
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }

    /**
     * Visit an instruction block.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitInstructionBlock ( Hoa_Visitor_Element $element,
                                            &$handle = null,
                                             $eldnah = null ) {

        $array = array();

        foreach($element->getInstructions() as $i => $instruction)
            foreach($instruction->accept($this->getVisitor(), $handle, $eldnah) as $key => $value)
                $array[] = $value;

        $braces = true;
        $scolon = false;

        switch($element->getBracesMode()) {

            case Hoa_Pom_Token_Instruction_Block::FORCE_BRACES:
                $braces = true;
                $scolon = false;
              break;

            case Hoa_Pom_Token_Instruction_Block::SKIP_BRACES:
                $braces = false;
                $scolon = false;
              break;

            case Hoa_Pom_Token_Instruction_Block::DETERMINE_BRACES:

                $handle = count($array);

                if($handle == 0)
                    switch($element->getEmptyMode()) {

                        case Hoa_Pom_Token_Instruction_Block::SEMI_COLON_EMPTY:
                            $braces = false;
                            $scolon = true;
                          break;

                        case Hoa_Pom_Token_Instruction_Block::BRACE_EMPTY:
                            $braces = true;
                            $scolon = false;
                          break;

                        case Hoa_Pom_Token_Instruction_Block::NOTHING_EMPTY:
                            $braces = false;
                            $scolon = false;
                    }
                else
                    $braces = $handle > 1;

              break;
        }

        return array_merge(
            (true === $braces
                 ? array(array(
                       0 => Hoa_Pom::_OPEN_BRACE,
                       1 => '{',
                       2 => -1
                       ))
                 : array()
            ),
            $array,
            (true === $braces
                 ? array(array(
                       0 => Hoa_Pom::_CLOSE_BRACE,
                       1 => '}',
                       2 => -1
                   ))
                 : array()
            ),
            (true === $scolon
                 ? array(array(
                       0 => Hoa_Pom::_SEMI_COLON,
                       1 => ';',
                       2 => -1
                   ))
                 : array()
            )
        );
    }
}
