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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_ControlStructure
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
 * Hoa_Pom_Token_ControlStructure_Break
 */
import('Pom.Token.ControlStructure.Break');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_If
 */
import('Pom.Token.ControlStructure.Conditional.If');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_If_If
 */
import('Pom.Token.ControlStructure.Conditional.If.If');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_If_Else
 */
import('Pom.Token.ControlStructure.Conditional.If.Else');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_If_Elseif
 */
import('Pom.Token.ControlStructure.Conditional.If.Elseif');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_Switch
 */
import('Pom.Token.ControlStructure.Conditional.Switch');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_Switch_Case
 */
import('Pom.Token.ControlStructure.Conditional.Switch.Case');

/**
 * Hoa_Pom_Token_ControlStructure_Conditional_Switch_Default
 */
import('Pom.Token.ControlStructure.Conditional.Switch.Default');

/**
 * Hoa_Pom_Token_ControlStructure_Continue
 */
import('Pom.Token.ControlStructure.Continue');

/**
 * Hoa_Pom_Token_ControlStructure_Import_Include
 */
import('Pom.Token.ControlStructure.Import.Include');

/**
 * Hoa_Pom_Token_ControlStructure_Import_IncludeOnce
 */
import('Pom.Token.ControlStructure.Import.IncludeOnce');

/**
 * Hoa_Pom_Token_ControlStructure_Import_Require
 */
import('Pom.Token.ControlStructure.Import.Require');

/**
 * Hoa_Pom_Token_ControlStructure_Import_RequireOnce
 */
import('Pom.Token.ControlStructure.Import.RequireOnce');

/**
 * Hoa_Pom_Token_ControlStructure_Loop_DoWhile
 */
import('Pom.Token.ControlStructure.Loop.DoWhile');

/**
 * Hoa_Pom_Token_ControlStructure_Loop_For
 */
import('Pom.Token.ControlStructure.Loop.For');

/**
 * Hoa_Pom_Token_ControlStructure_Loop_Foreach
 */
import('Pom.Token.ControlStructure.Loop.Foreach');

/**
 * Hoa_Pom_Token_ControlStructure_Loop_While
 */
import('Pom.Token.ControlStructure.Loop.While');

/**
 * Hoa_Pom_Token_ControlStructure_Return
 */
import('Pom.Token.ControlStructure.Return');

/**
 * Hoa_Pom_Token_ControlStructure_TryCatch_Catch
 */
import('Pom.Token.ControlStructure.TryCatch.Catch');

/**
 * Hoa_Pom_Token_ControlStructure_TryCatch_Try
 */
import('Pom.Token.ControlStructure.TryCatch.Try');

/**
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_ControlStructure.
 *
 * Visit a controlstructure.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_ControlStructure
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_ControlStructure extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit a controlstructure break.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureBreak ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_BREAK,
                1 => 'break',
                2 => -1
            )),
            (true === $element->hasLevel()
                 ? $element->getLevel()->accept($this->getVisitor(), $handle)
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }

    /**
     * Visit a controlstructure conditional if.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalIf ( Hoa_Visitor_Element $element, &$handle = null ) {

        $if     = $element->getIf()->accept($this->getVisitor(), $handle);
        $elseif = array();

        foreach($elemen->getElseifs() as $i => $ei)
            foreach($ei->accept($this->getVisitor(), $handle) as $key => $value)
                $elseif[] = $value;

        $else   = true === $element->hasElse()
                      ? $element->getElse()->accept($this->getVisitor(), $handle)
                      : array();

        return array_merge(
            $if,
            $elseif,
            $else
        );
    }

    /**
     * Visit a controlstructure conditional if if.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalIfIf ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_IF,
                1 => 'if',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $element->getExpression()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure conditional if else.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalIfElse ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_ELSE,
                1 => 'else',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure conditional if elseif.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalIfElseif ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_ELSEIF,
                1 => 'elseif',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $element->getExpression()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure conditional switch.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalSwitch ( Hoa_Visitor_Element $element, &$handle = null ) {

        $expression  = $element->getExpression()->accept($this->getVisitor(), $handle);
        $cases       = array();

        foreach($element->getCases() as $i => $case)
            foreach($case->accept($this->getVisitor(), $handle) as $key => $value)
                $cases[] = $value;

        $default     = true === $element->hasDefault()
                           ? $element->getDefault()->accept($this->getVisitor(), $handle)
                           : array();

        return array_merge(
            array(array(
                0 => Hoa_Pom::_SWITCH,
                1 => 'switch',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $expression,
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
            $cases,
            $default,
            array(array(
                0 => Hoa_Pom::_CLOSE_BRACE,
                1 => '}',
                2 => -1
            ))
        );
    }

    /**
     * Visit a controlstructure conditional switch case.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalSwitchCase ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_CASE,
                1 => 'case',
                2 => -1
            )),
            $element->getExpression()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_COLON,
                1 => ':',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure conditional switch default.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureConditionalSwitchDefault ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_DEFAULT,
                1 => 'default',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_COLON,
                1 => ':',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure continue.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureContinue ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_CONTINUE,
                1 => 'continue',
                2 => -1
            )),
            (true === $element->hasLevel()
                 ? $element->getLevel()->accept($this->getVisitor(), $handle)
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }

    /**
     * Visit a controlstructure import include.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureImportInclude ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_INCLUDE,
                1 => 'include',
                2 => -1
            )),
            $element->getValue()->accept($this->getVisitor(), $handle)
        );
    }

    /**
     * Visit a controlstructure import include_once.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureImportIncludeOnce ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_INCLUDE_ONCE,
                1 => 'include_once',
                2 => -1
            )),
            $element->getValue()->accept($this->getVisitor(), $handle)
        );
    }

    /**
     * Visit a controlstructure import require.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureImportRequire ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_REQUIRE,
                1 => 'require',
                2 => -1
            )),
            $element->getValue()->accept($this->getVisitor(), $handle)
        );
    }

    /**
     * Visit a controlstructure import require_once.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureImportRequireOnce ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_REQUIRE_ONCE,
                1 => 'require_once',
                2 => -1
            )),
            $element->getValue()->accept($this->getVisitor(), $handle)
        );
    }

    /**
     * Visit a controlstructure loop do/while.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureLoopDoWhile ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_DO,
                1 => 'do',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle),
            array(array(
                0 => Hoa_Pom::_WHILE,
                1 => 'while',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $element->getExpression()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }

    /**
     * Visit a controlstructure loop for.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureLoopFor ( Hoa_Visitor_Element $element, &$handle = null ) {

        $fi   = false;
        $fn   = false;
        $ini  = array();
        $next = array();

        foreach($element->getIniExpressions()  as $i => $iniExpression) {

            if($fi === true)
                $ini[] = array(
                             0 => Hoa_Pom::_COMMA,
                             1 => ',',
                             2 => -1
                         );
            else
                $fi = true;

            foreach($iniExpression->accept($this->getVisitor(), $handle) as $key => $value)
                $ini[] = $value;
        }

        foreach($element->getNextExpressions() as $i => $nextExpression) {

            if($fn === true)
                $next[] = array(
                              0 => Hoa_Pom::_COMMA,
                              1 => ',',
                              2 => -1
                          );
            else
                $fn = true;

            foreach($nextExpression->accept($this->getVisitor(), $handle) as $key => $value)
                $next[] = $value;
        }

        $cond = true === $element->hasCondExpression()
                    ? $element->getCondExpression()
                    : array();

        return array_merge(
            array(array(
                0 => Hoa_Pom::_FOR,
                1 => 'for',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $ini,
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            )),
            $cond,
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            )),
            $next,
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure loop foreach.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureLoopForeach ( Hoa_Visitor_Element $element, &$handle = null ) {

        if(false === $element->valueExists())
            throw new Hoa_Pom_Token_Util_Exception(
                'A foreach loop must have a value variable.', 1);

        return array_merge(
            array(array(
                0 => Hoa_Pom::_FOREACH,
                1 => 'foreach',
                2 => -1,
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $element->getArrayExpression()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_AS,
                1 => 'as',
                2 => -1
            )),
            (true === $element->keyExists()
                 ? array_merge(
                       $element->getKey()->accept($this->getVisitor(), $handle),
                       array(array(
                           0 => Hoa_Pom::_DOUBLE_ARROW,
                           1 => '=>',
                           2 => -1
                       ))
                   )
                 : array()
            ),
            $element->getValue()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure loop while.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureLoopWhile ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_WHILE,
                1 => 'while',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $element->getExpression()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure return.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureReturn ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_RETURN,
                1 => 'return',
                2 => -1
            )),
            (true === $element->hasValue()
                 ? $element->getValue()->accept($this->getVisitor(), $handle)
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_SEMI_COLON,
                1 => ';',
                2 => -1
            ))
        );
    }

    /**
     * Visit a controlstructure try/catch catch.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureTryCatchCatch ( Hoa_Visitor_Element $element, &$handle = null ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_CATCH,
                1 => 'catch',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $element->getType()->accept($this->getVisitor(), $handle),
            $element->getVariable()->accept($this->getVisitor(), $handle),
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle)
        );
    }

    /**
     * Visit a controlstructure try/catch try.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @return  array
     */
    public function visitControlStructureTryCatchTry ( Hoa_Visitor_Element $element, &$handle = null ) {

        if(false === $element->hasCatch())
            throw new Hoa_Pom_Token_Util_Exception(
                'A try structure must be coupled with one catch block at ' .
                'least.', 1);

        $catchs = array();

        foreach($element->getCatchs() as $i => $catch)
            foreach($element->accept($this->getVisitor(), $handle) as $key => $value)
                $catchs[] = $value;

        return array_merge(
            array(array(
                0 => Hoa_Pom::_TRY,
                1 => 'try',
                2 => -1
            )),
            $this->getVisitor()
                 ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle),
            $catchs
        );
    }
}
