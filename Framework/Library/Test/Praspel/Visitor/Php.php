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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Visitor_Php
 *
 */

/**
 * Hoa_Visitor_Registry
 */
import('Visitor.Registry') and load();

/**
 * Class Hoa_Test_Praspel_Visitor_Php.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Visitor_Php
 */

class Hoa_Test_Praspel_Visitor_Php extends Hoa_Visitor_Registry {

    /**
     * Initialize aggregated visitor.
     *
     * @access  access
     * @return  void
     */
    public function __construct ( ) {

        $this->addEntry(
            'Hoa_Test_Praspel_Contract',
            array($this, 'visitContract')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Clause_Ensures',
            array($this, 'visitClauseContract')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Clause_Invariant',
            array($this, 'visitClauseContract')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Clause_Requires',
            array($this, 'visitClauseContract')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Clause_Throwable',
            array($this, 'visitClauseThrowable')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Variable',
            array($this, 'visitDomainDisjunction')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Constructor_Old',
            array($this, 'visitConstructorOld')
        );
        $this->addEntry(
            'Hoa_Test_Praspel_Constructor_Result',
            array($this, 'visitDomainDisjunction')
        );

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitContract ( Hoa_Visitor_Element $element,
                                    &$handle = null, $eldnah = null ) {

        $out = '$contract  = new ' . get_class($element) . '(' . "\n" .
               '    $class,' . "\n" .
               '    $method,' . "\n" .
               '    $file,' . "\n" .
               '    $startLine,' . "\n" .
               '    $endLine' . "\n" .
               ');' . "\n\n";

        foreach($element->getClauses() as $i => $clause)
            $out .= $clause->accept($this, $handle, $eldnah) . "\n";

        return $out;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitClauseContract ( Hoa_Visitor_Element $element,
                                          &$handle = null, $eldnah = null ) {

        $gc     = get_class($element);
        $out    = '$contract' . "\n" .
                  '    ->clause(\'' .
                       strtolower(substr($gc, strrpos($gc, '\\') + 1)) .
                      '\')' . "\n";
        $handle = array();

        foreach($element->getVariables() as $i => $variable)
            $handle[] = $variable->accept($this, $handle, $eldnah);

        return $out . implode('    ->_and' . "\n", $handle) . ';';
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitClauseThrowable ( Hoa_Visitor_Element $element,
                                           &$handle = null, $eldnah = null ) {

        return '$contract' . "\n" .
               '    ->clause(\'throwable\')'  . "\n" .
               '    ->couldThrow(\'' .
               implode(
                   '\')' . "\n" . '    ->_comma' . "\n" . '    ->couldThrow(\'',
                   $element->getList()
               ) . '\')' . "\n;";
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitDomainDisjunction ( Hoa_Visitor_Element $element,
                                             &$handle = null, $eldnah = null ) {

        return '    ->variable(\'' . $element->getName() . '\')' . "\n" .
               implode(
                   '        ->_or' . "\n",
                   $this->formatArguments($element->getDomains(), true)
               );
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitConstructorOld ( Hoa_Visitor_Element $element,
                                          &$handle = null, $eldnah = null ) {

        return '    ->variable(\'\old(' . $element->getName() . '\'))' . "\n" .
               implode(
                   '        ->_or' . "\n",
                   $this->formatArguments($element->getDomains(), true)
               );
    }

    /**
     * Format arguments to produce a string.
     *
     * @access  protected
     * @param   array      $arguments    Arguments to format.
     * @return  array
     */
    protected function formatArguments ( Array $arguments, $f ) {

        static $d = 1;

        $out    = array();
        $spaces = str_repeat('    ', $d);

        foreach($arguments as $i => $argument)
            if(is_array($argument)) {

                $handle = null;

                foreach($argument as $e => $domran) {

                    $d += 2;

                    if(!empty($domran[0]))
                        $handle .= $spaces . '        ->from()' . "\n" .
                                   implode(
                                       $spaces . '            ->_or' . "\n",
                                       $this->formatArguments($domran[0], true)
                                   );
                    else
                        $handle .= $spaces . '        ->from()' . "\n";

                    if(!empty($domran[1]))
                        $handle .= $spaces . '        ->to()' . "\n" .
                                   implode(
                                       $spaces . '            ->_or' . "\n",
                                       $this->formatArguments($domran[1], true)
                                   );

                    $d -= 2;
                }

                $out[] = $spaces . '    ->withArray()' . "\n" .
                         $handle .
                         $spaces . '            ->end()' . "\n";
            }
            elseif(is_object($argument)) {

                $d++;

                switch(strtolower($argument->getName())) {

                    case 'constboolean':
                        $out[] = $spaces . '    ->with(' .
                                 ($argument->getValue() ? 'true' : 'false') .
                                 ')' . "\n";
                      break;

                    case 'constfloat':
                    case 'constinteger':
                        $out[] = $spaces . '    ->with(' .
                                 (string) $argument->getValue() .
                                 ')' . "\n";
                      break;

                    case 'conststring':
                        $out[] = $spaces . '    ->with(\'' .
                                 str_replace("'", "\'", $argument->getValue()) .
                                 '\')' . "\n";
                      break;

                    default:
                        $out[] = $spaces . '    ->' .
                                 (true === $f
                                     ? 'belongsTo'
                                     : 'withDomain'
                                 ) . '(\'' . $argument->getName() . '\')' . "\n" .
                                 implode(
                                    $spaces . '        ->_comma' . "\n",
                                    $this->formatArguments(
                                        $argument->getArguments(),
                                        false
                                    )
                                 ) .
                                 $spaces . '        ->_ok()' . "\n";
                }

                $d--;
            }

        return $out;
    }
}
