<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_ControlStructure_Loop_DoWhile
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
 * Hoa_Pom_Token_Instruction_Block
 */
import('Pom.Token.Instruction.Block');

/**
 * Class Hoa_Pom_Token_ControlStructure_Loop_DoWhile.
 *
 * Represent a do/while loop.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Loop_DoWhile
 */

class       Hoa_Pom_Token_ControlStructure_Loop_DoWhile
    extends Hoa_Pom_Token_Instruction_Block {

    /**
     * Expression.
     *
     * @var mixed object
     */
    protected $_expression  = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $expression    Expression.
     * @return  void
     */
    public function __construct ( $expression ) {

        parent::setBracesMode(parent::FORCE_BRACES);
        $this->setExpression($expression);

        return;
    }

    /**
     * Set expression.
     *
     * @access  public
     * @param   mixed   $expression    Expression.
     * @return  mixed
     */
    public function setExpression ( $expression) {

        switch(get_class($expression)) {

            case 'Hoa_Pom_Token_Array':
            case 'Hoa_Pom_Token_Call':
            case 'Hoa_Pom_Token_Cast':
            case 'Hoa_Pom_Token_Clone':
            case 'Hoa_Pom_Token_Comment':
            case 'Hoa_Pom_Token_New':
            case 'Hoa_Pom_Token_Number':
            case 'Hoa_Pom_Token_Operation':
            case 'Hoa_Pom_Token_String':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'An expression cannot be constitued by a class that ' .
                    'is an instance of %s.', 0, get_class($expression));
        }

        $old               = $old;
        $this->_expression = $expression;

        return $old;
    }

    /**
     * Get expression.
     *
     * @access  public
     * @return  mixed
     */
    public function getExpression ( ) {

        return $this->_expression;
    }
}
