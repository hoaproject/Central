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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize
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
 * Hoa_Visitor_Registry
 */
import('Visitor.Registry');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Array
 */
import('Pom.Token.Util.Visitor.Tokenize.Array');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Call
 */
import('Pom.Token.Util.Visitor.Tokenize.Call');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Cast
 */
import('Pom.Token.Util.Visitor.Tokenize.Cast');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Class
 */
import('Pom.Token.Util.Visitor.Tokenize.Class');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Clone
 */
import('Pom.Token.Util.Visitor.Tokenize.Clone');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Comment
 */
import('Pom.Token.Util.Visitor.Tokenize.Comment');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_ControlStructure
 */
import('Pom.Token.Util.Visitor.Tokenize.ControlStructure');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Exception
 */
import('Pom.Token.Util.Visitor.Tokenize.Exception');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Function
 */
import('Pom.Token.Util.Visitor.Tokenize.Function');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Instruction
 */
import('Pom.Token.Util.Visitor.Tokenize.Instruction');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Interface
 */
import('Pom.Token.Util.Visitor.Tokenize.Interface');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_LateParsing
 */
import('Pom.Token.Util.Visitor.Tokenize.LateParsing');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_New
 */
import('Pom.Token.Util.Visitor.Tokenize.New');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Number
 */
import('Pom.Token.Util.Visitor.Tokenize.Number');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Operation
 */
import('Pom.Token.Util.Visitor.Tokenize.Operation');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Operator
 */
import('Pom.Token.Util.Visitor.Tokenize.Operator');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_OutterPhp
 */
import('Pom.Token.Util.Visitor.Tokenize.OutterPhp');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Php
 */
import('Pom.Token.Util.Visitor.Tokenize.Php');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Root
 */
import('Pom.Token.Util.Visitor.Tokenize.Root');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_String
 */
import('Pom.Token.Util.Visitor.Tokenize.String');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Variable
 */
import('Pom.Token.Util.Visitor.Tokenize.Variable');

/**
 * Hoa_Pom_Token_Util_Visitor_Tokenize_Whitespace
 */
import('Pom.Token.Util.Visitor.Tokenize.Whitespace');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize.
 *
 * Visitor to tokenize the object model..
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize extends Hoa_Visitor_Registry {

    /**
     * Write the registry.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $_ = 'Hoa_Pom_Token_';

        $this->addEntry(
            $_ . 'Array',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Array($this), 'visitArray')
        );

        $call = new Hoa_Pom_Token_Util_Visitor_Tokenize_Call($this);
        $this->addEntry(
            $_ . 'Call_Attribute',
            array($call, 'visitCallAttribute')
        );
        $this->addEntry(
            $_ . 'Call_ClassConstant',
            array($call, 'visitCallClassConstant')
        );
        $this->addEntry(
            $_ . 'Call_Function',
            array($call, 'visitCallFunction')
        );
        $this->addEntry(
            $_ . 'Call_Method',
            array($call, 'visitCallMethod')
        );
        $this->addEntry(
            $_ . 'Call_StaticAttribute',
            array($call, 'visitCallStaticAttribute')
        );
        $this->addEntry(
            $_ . 'Call_StaticMethod',
            array($call, 'visitCallStaticMethod')
        );

        $this->addEntry(
            $_ . 'Cast',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Cast($this), 'visitCast')
        );

        $class = new Hoa_Pom_Token_Util_Visitor_Tokenize_Class($this);
        $this->addEntry(
            $_ . 'Class',
            array($class, 'visitClass')
        );
        $this->addEntry(
            $_ . 'Class_Access',
            array($class, 'visitClassAccess')
        );
        $this->addEntry(
            $_ . 'Class_Attribute',
            array($class, 'visitClassAttribute')
        );
        $this->addEntry(
            $_ . 'Class_Constant',
            array($class, 'visitClassConstant')
        );
        $this->addEntry(
            $_ . 'Class_Method',
            array($class, 'visitClassMethod')
        );

        $this->addEntry(
            $_ . 'Clone',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Clone($this), 'visitClone')
        );

        $this->addEntry(
            $_ . 'Comment',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Comment($this), 'visitComment')
        );
        
        $controlStructure = new Hoa_Pom_Token_Util_Visitor_Tokenize_ControlStructure($this);

        $this->addEntry(
            $_ . 'ControlStructure_Break',
            array($controlStructure, 'visitControlStructureBreak')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_If',
            array($controlStructure, 'visitControlStructureConditionalIf')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_If_If',
            array($controlStructure, 'visitControlStructureConditionalIfIf')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_If_Else',
            array($controlStructure, 'visitControlStructureConditionalIfElse')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_If_Elseif',
            array($controlStructure, 'visitControlStructureConditionalIfElseif')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_Switch',
            array($controlStructure, 'visitControlStructureConditionalSwitch')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_Switch_Case',
            array($controlStructure, 'visitControlStructureConditionalSwitchCase')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Conditional_Switch_Default',
            array($controlStructure, 'visitControlStructureConditionalSwitchDefault')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Continue',
            array($controlStructure, 'visitControlStructureContinue')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Import_Include',
            array($controlStructure, 'visitControlStructureImportInclude')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Import_IncludeOnce',
            array($controlStructure, 'visitControlStructureImportIncludeOnce')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Import_Require',
            array($controlStructure, 'visitControlStructureImportRequire')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Import_RequireOnce',
            array($controlStructure, 'visitControlStructureImportRequireOnce')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Loop_DoWhile',
            array($controlStructure, 'visitControlStructureLoopDoWhile')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Loop_For',
            array($controlStructure, 'visitControlStructureLoopFor')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Loop_Foreach',
            array($controlStructure, 'visitControlStructureLoopForeach')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Loop_While',
            array($controlStructure, 'visitControlStructureLoopWhile')
        );
        $this->addEntry(
            $_ . 'ControlStructure_Return',
            array($controlStructure, 'visitControlStructureReturn')
        );
        $this->addEntry(
            $_ . 'ControlStructure_TryCatch_Catch',
            array($controlStructure, 'visitControlStructureTryCatchCatch')
        );
        $this->addEntry(
            $_ . 'ControlStructure_TryCatch_Try',
            array($controlStructure, 'visitControlStructureTryCatchTry')
        );

        $this->addEntry(
            $_ . 'Exception',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Exception($this), 'visitException')
        );

        $function = new Hoa_Pom_Token_Util_Visitor_Tokenize_Function($this);
        $this->addEntry(
            $_ . 'Function_Argument',
            array($function, 'visitFunctionArgument')
        );
        $this->addEntry(
            $_ . 'Function_Named',
            array($function, 'visitFunctionNamed')
        );

        $instruction = new Hoa_Pom_Token_Util_Visitor_Tokenize_Instruction($this);
        $this->addEntry(
            $_ . 'Instruction',
            array($instruction, 'visitInstruction')
        );
        $this->addEntry(
            $_ . 'Instruction_Block',
            array($instruction, 'visitInstructionBlock')
        );

        $this->addEntry(
            $_ . 'Interface',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Interface($this), 'visitInterface')
        );

        $this->addEntry(
            $_ . 'LateParsing',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_LateParsing($this), 'visitLateParsing')
        );

        $this->addEntry(
            $_ . 'New',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_New($this), 'visitNew')
        );

        $number = new Hoa_Pom_Token_Util_Visitor_Tokenize_Number($this);
        $this->addEntry(
            $_ . 'Number_DNumber',
            array($number, 'visitNumberDNumber')
        );
        $this->addEntry(
            $_ . 'Number_LNumber',
            array($number, 'visitNumberLNumber')
        );

        $this->addEntry(
            $_ . 'Operation',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Operation($this), 'visitOperation')
        );

        $operator = new Hoa_Pom_Token_Util_Visitor_Tokenize_Operator($this);
        $this->addEntry(
            $_ . 'Operator_Arithmetical',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_Assignement',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_Bitwise',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_Comparison',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_ErrorControl',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_Execution',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_InDeCrementing',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_Logical',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_String',
            array($operator, 'visitOperators')
        );
        $this->addEntry(
            $_ . 'Operator_Type',
            array($operator, 'visitOperators')
        );

        $this->addEntry(
            $_ . 'OutterPhp',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_OutterPhp($this), 'visitOutterPhp')
        );

        $this->addEntry(
            $_ . 'Php',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Php($this), 'visitPhp')
        );

        $this->addEntry(
            $_ . 'Root',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Root($this), 'visitRoot')
        );

        $string = new Hoa_Pom_Token_Util_Visitor_Tokenize_String($this);
        $this->addEntry(
            $_ . 'String',
            array($string, 'visitString')
        );
        $this->addEntry(
            $_ . 'String_Boolean',
            array($string, 'visitString')
        );
        $this->addEntry(
            $_ . 'String_Constant',
            array($string, 'visitString')
        );
        $this->addEntry(
            $_ . 'String_EncapsedConstant',
            array($string, 'visitStringEncapsedConstant')
        );
        $this->addEntry(
            $_ . 'String_Null',
            array($string, 'visitString')
        );

        $this->addEntry(
            $_ . 'Variable',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Variable($this), 'visitVariable')
        );

        $this->addEntry(
            $_ . 'Whitespace',
            array(new Hoa_Pom_Token_Util_Visitor_Tokenize_Whitespace($this), 'visitWhitespace')
        );

        return;
    }
}
