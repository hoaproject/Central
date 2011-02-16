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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint
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
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Array
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Array');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Call
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Call');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Cast
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Cast');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Class
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Class');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Clone
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Clone');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Comment
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Comment');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_ControlStructure
 */
import('Pom.Token.Util.Visitor.PrettyPrint.ControlStructure');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Exception
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Exception');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Function
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Function');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Instruction
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Instruction');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Interface
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Interface');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_LateParsing
 */
import('Pom.Token.Util.Visitor.PrettyPrint.LateParsing');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_New
 */
import('Pom.Token.Util.Visitor.PrettyPrint.New');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Number
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Number');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operation
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Operation');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operator
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Operator');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_OutterPhp
 */
import('Pom.Token.Util.Visitor.PrettyPrint.OutterPhp');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Php
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Php');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Root
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Root');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_String
 */
import('Pom.Token.Util.Visitor.PrettyPrint.String');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Variable
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Variable');

/**
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Whitespace
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Whitespace');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint.
 *
 * Visitor to tokenize the object model..
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint extends Hoa_Visitor_Registry {

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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Array($this), 'visitArray')
        );

        $call = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Call($this);
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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Cast($this), 'visitCast')
        );

        $class = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Class($this);
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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Clone($this), 'visitClone')
        );

        $this->addEntry(
            $_ . 'Comment',
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Comment($this), 'visitComment')
        );
        
        $controlStructure = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_ControlStructure($this);

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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Exception($this), 'visitException')
        );

        $function = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Function($this);
        $this->addEntry(
            $_ . 'Function_Argument',
            array($function, 'visitFunctionArgument')
        );
        $this->addEntry(
            $_ . 'Function_Named',
            array($function, 'visitFunctionNamed')
        );

        $instruction = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Instruction($this);
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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Interface($this), 'visitInterface')
        );

        $this->addEntry(
            $_ . 'LateParsing',
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_LateParsing($this), 'visitLateParsing')
        );

        $this->addEntry(
            $_ . 'New',
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_New($this), 'visitNew')
        );

        $number = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Number($this);
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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operation($this), 'visitOperation')
        );

        $operator = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Operator($this);
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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_OutterPhp($this), 'visitOutterPhp')
        );

        $this->addEntry(
            $_ . 'Php',
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Php($this), 'visitPhp')
        );

        $this->addEntry(
            $_ . 'Root',
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Root($this), 'visitRoot')
        );

        $string = new Hoa_Pom_Token_Util_Visitor_PrettyPrint_String($this);
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
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Variable($this), 'visitVariable')
        );

        $this->addEntry(
            $_ . 'Whitespace',
            array(new Hoa_Pom_Token_Util_Visitor_PrettyPrint_Whitespace($this), 'visitWhitespace')
        );

        return;
    }
}
