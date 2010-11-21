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
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_ControlStructure
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
 * Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate
 */
import('Pom.Token.Util.Visitor.PrettyPrint.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_PrettyPrint_ControlStructure.
 *
 * Visit a controlstructure.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_PrettyPrint_ControlStructure
 */

class Hoa_Pom_Token_Util_Visitor_PrettyPrint_ControlStructure extends Hoa_Pom_Token_Util_Visitor_PrettyPrint_Aggregate {

    /**
     * Visit a controlstructure break.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureBreak ( Hoa_Visitor_Element $element,
                                                 &$handle = null,
                                                  $eldnah = null ) {

        return 'break' .
               (true === $element->hasLevel()
                    ? ' ' . $element->getLevel()->accept($this->getVisitor(), $handle, $eldnah)
                    : ''
               ) .
               ';';
    }

    /**
     * Visit a controlstructure conditional if.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalIf ( Hoa_Visitor_Element $element,
                                                         &$handle = null,
                                                          $eldnah = null ) {

        $if     = $element->getIf()->accept($this->getVisitor(), $handle, $eldnah);
        $elseif = null;

        foreach($elemen->getElseifs() as $i => $ei)
            $elseif .= $ei->accept($this->getVisitor(), $handle, $eldnah);

        $else   = true === $element->hasElse()
                      ? $element->getElse()->accept($this->getVisitor(), $handle, $eldnah)
                      : '';

        return $if     . "\n" .
               $elseif . "\n" .
               $else;
    }

    /**
     * Visit a controlstructure conditional if if.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalIfIf ( Hoa_Visitor_Element $element,
                                                           &$handle = null,
                                                            $eldnah = null ) {

        return 'if(' .
               $element->getExpression()->accept($this->getVisitor(), $handle, $eldnah) .
               ')' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure conditional if else.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalIfElse ( Hoa_Visitor_Element $element,
                                                             &$handle = null,
                                                              $eldnah = null ) {

        return 'else' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure conditional if elseif.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalIfElseif ( Hoa_Visitor_Element $element,
                                                               &$handle = null,
                                                                $eldnah = null ) {

        return 'elseif(' .
               $element->getExpression()->accept($this->getVisitor(), $handle, $eldnah) .
               ')' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure conditional switch.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalSwitch ( Hoa_Visitor_Element $element,
                                                             &$handle = null,
                                                              $eldnah = null ) {

        $expression  = $element->getExpression()->accept($this->getVisitor(), $handle, $eldnah);
        $cases       = null;

        foreach($element->getCases() as $i => $case)
            $cases .= $case->accept($this->getVisitor(), $handl, $eldnahe) . "\n";

        $default    = true === $element->hasDefault()
                          ? $element->getDefault()->accept($this->getVisitor(), $handle, $eldnah) . "\n"
                          : '';

        return 'switch(' .
              $expression .
              ') {' . "\n" .
              $cases . "\n" .
              $default . "\n" .
              '}';
    }

    /**
     * Visit a controlstructure conditional switch case.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalSwitchCase ( Hoa_Visitor_Element $element,
                                                                 &$handle = null,
                                                                  $eldnah = null ) {

        return 'case ' .
               $element->getExpression()->accept($this->getVisitor(), $handle, $eldnah) .
               ':' . "\n" .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure conditional switch default.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureConditionalSwitchDefault ( Hoa_Visitor_Element $element,
                                                                    &$handle = null,
                                                                     $eldnah = null ) {

        return 'default:' . "\n" .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure continue.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureContinue ( Hoa_Visitor_Element $element,
                                                    &$handle = null,
                                                     $eldnah = null ) {

        return 'continue' .
               (true === $element->hasLevel()
                    ? ' ' . $element->getLevel()->accept($this->getVisitor(), $handle, $eldnah)
                    : ''
               );
    }

    /**
     * Visit a controlstructure import include.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureImportInclude ( Hoa_Visitor_Element $element,
                                                         &$handle = null,
                                                          $eldnah = null ) {

        return 'include ' .
               $element->getValue()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a controlstructure import include_once.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureImportIncludeOnce ( Hoa_Visitor_Element $element,
                                                             &$handle = null,
                                                              $eldnah = null ) {

        return 'include_once ' .
               $element->getValue()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a controlstructure import require.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureImportRequire ( Hoa_Visitor_Element $element,
                                                         &$handle = null,
                                                          $eldnah = null ) {

        return 'require ' .
               $element->getValue()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a controlstructure import require_once.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureImportRequireOnce ( Hoa_Visitor_Element $element,
                                                             &$handle = null,
                                                              $eldnah = null ) {

        return 'require_once ' .
               $element->getValue()->accept($this->getVisitor(), $handle, $eldnah);
    }

    /**
     * Visit a controlstructure loop do/while.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureLoopDoWhile ( Hoa_Visitor_Element $element,
                                                       &$handle = null,
                                                        $eldnah = null ) {

        return 'do' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah) .
               'while(' .
               $element->getExpression()->accept($this->getVisitor(), $handle, $eldnah) .
               ');';
    }

    /**
     * Visit a controlstructure loop for.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureLoopFor ( Hoa_Visitor_Element $element,
                                                   &$handle = null,
                                                    $eldnah = null ) {

        $fi   = false;
        $fn   = false;
        $ini  = null;
        $next = null;

        foreach($element->getIniExpressions()  as $i => $iniExpression) {

            if($fi === true)
                $ini .= ', ';
            else
                $fi   = true;

            $ini .= $iniExpression->accept($this->getVisitor(), $handle, $eldnah);
        }

        foreach($element->getNextExpressions() as $i => $nextExpression) {

            if($fn === true)
                $next .= ', ';
            else
                $fn    = true;

            $next .= $nextExpression->accept($this->getVisitor(), $handle, $eldnah);
        }

        $cond = true === $element->hasCondExpression()
                    ? $element->getCondExpression()
                    : '';

        return 'for(' .
               $ini  . '; ' .
               $cond . '; ' .
               $next . ')'  .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure loop foreach.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureLoopForeach ( Hoa_Visitor_Element $element,
                                                       &$handle = null,
                                                        $eldnah = null ) {

        if(false === $element->valueExists())
            throw new Hoa_Pom_Token_Util_Exception(
                'A foreach loop must have a value variable.', 1);

        return 'foreach(' .
               $element->getArrayExpression()->accept($this->getVisitor(), $handle, $eldnah) .
               ' as ' .
               (true === $element->keyExists()
                    ? $element->getKey()->accept($this->getVisitor(), $handle, $eldnah) .
                      ' => '
                    : ''
               ) .
               $element->getValue()->accept($this->getVisitor(), $handle, $eldnah) .
               ')' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure loop while.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureLoopWhile ( Hoa_Visitor_Element $element,
                                                     &$handle = null,
                                                      $eldnah = null ) {

        return 'while(' . 
               $element->getExpression()->accept($this->getVisitor(), $handle, $eldnah) .
               ')' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure return.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureReturn ( Hoa_Visitor_Element $element,
                                                  &$handle = null,
                                                   $eldnah = null ) {

        return 'return' .
               (true === $element->hasValue()
                    ? ' ' . $element->getValue()->accept($this->getVisitor(), $handle, $eldnah)
                    : ''
               ) . ';';
    }

    /**
     * Visit a controlstructure try/catch catch.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureTryCatchCatch ( Hoa_Visitor_Element $element,
                                                         &$handle = null,
                                                          $eldnah = null ) {

        return 'catch ( ' .
               $element->getType()->accept($this->getVisitor(), $handle, $eldnah) .
               ' ' .
               $element->getVariable()->accept($this->getVisitor(), $handle, $eldnah) .
               ' )' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah);
    }

    /**
     * Visit a controlstructure try/catch try.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  string
     */
    public function visitControlStructureTryCatchTry ( Hoa_Visitor_Element $element,
                                                       &$handle = null,
                                                        $eldnah = null ) {

        if(false === $element->hasCatch())
            throw new Hoa_Pom_Token_Util_Exception(
                'A try structure must be coupled with one catch block at ' .
                'least.', 1);

        $catchs = null;

        foreach($element->getCatchs() as $i => $catch)
            $catchs .= $element->accept($this->getVisitor(), $handle, $eldnah);

        return 'try' .
               $this->getVisitor()
                    ->visitEntry('Hoa_Pom_Token_Instruction_Block', $element, $handle, $eldnah) .
               $catchs;
    }
}
