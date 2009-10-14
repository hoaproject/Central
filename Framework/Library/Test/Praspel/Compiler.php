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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Compiler
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Class Hoa_Test_Praspel_Compiler.
 *
 * Compile Praspel to PHP.
 *
 * Contract grammar is :
 *
 *         annotation ::= clause*
 *             clause ::= (requires-clause | ensures-clause   |
 *                    ::=  throws-clause   | predicate-clause |
 *                    ::=  invariant-clause) <;;>
 *    requires-clause ::= <@requires>  expressions
 *     ensures-clause ::= <@ensures>   expressions
 *      throws-clause ::= <@throws>    lists
 *   predicate-clause ::= <@predicate> extended
 *   invariant-clause ::= <@invariant> expressions
 *        expressions ::= expression (<∧> expressions)*
 *         expression ::= (free-variable <:> types) | dependence
 *      free-variable ::= constructors | identifier
 *         dependence ::= identifier <⟺> identifier
 *              types ::= type (<∨> type)*
 *               type ::= identifier <(> arguments <)>
 *          arguments ::= argument (<,> argument)*
 *           argument ::= number | string | type | array | constructors
 *              array ::= <[> pairs | values <]>
 *              pairs ::= types <→> types (<;> pairs | values)*
 *             values ::= types (<;> pairs | values)*
 *              lists ::= identifier (<∨> identifier)*
 *           extended ::= identifier <←> identifier <:> php
 *       constructors ::= <\old(> identifier <)> | <\result>
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Compiler
 */

class Hoa_Test_Praspel_Compiler {

    /**
     * Buffer stack.
     *
     * @var Hoa_Test_Praspel_Compiler array
     */
    protected $buffer = array();



    /**
     * Constructor. Start the grammar.
     *
     * @access  private
     * @param   string  $praspel    Praspel.
     * @return  void
     */
    private function __construct ( $praspel ) {

        $this->axiom($praspel);
    }

    /**
     * Annotation.
     *
     * @access  protected
     * @param   string     $clauses    Many clauses.
     * @return  void
     */
    protected function axiom ( $clauses ) {

        preg_match_all('#@(.*?);;#s', $clauses, $matches, PREG_PATTERN_ORDER);
        $matches = $matches[1];

        foreach($matches as $i => $clause)
            $this->clause($clause);
    }

    /**
     * Clause.
     *
     * @access  protected
     * @param   string     $clause    A clause.
     * @return  void
     */
    protected function clause ( $clause ) {

        preg_match('#([a-z]+)\s+(.*)#si', $clause, $matches);
        list(, $name, $expressions) = $matches;

        $result = array();

        switch($name) {

            case 'requires':
            case 'ensures':
            case 'invariant':
                $result = $this->expressions($expressions);
              break;

            case 'throws':
                $result = $this->lists($expressions);
              break;

            case 'predicate':
                $result = $this->extended($expressions);
              break;

            default:
                throw new Exception('Error : ' . $name);
        }

        $this->buffer[$name] = $result;
    }

    /**
     * Expressions.
     *
     * @access  protected
     * @param   string     $expressions    Many expressions.
     * @return  array
     */
    protected function expressions ( $expressions ) {

        $expressions = explode('∧', $expressions);
        $expressions = array_map('trim', $expressions);
        $result      = array();

        foreach($expressions as $e => $expression)
            $result = array_merge($result, $this->expression($expression));

        return $result;
    }

    /**
     * Expression.
     *
     * @access  protected
     * @param   string     $expression    An expression.
     * @return  array
     */
    protected function expression ( $expression ) {

        $a = preg_match_all(
            '#(\\\?[a-z]+(?:\s*\(\s*[a-z]+\s*\))?)\s*:\s?(.*)#i',
            $expression,
            $matches,
            PREG_SET_ORDER
        );

        if(0 !== $a) {

            list(, $fv, $types) = $matches[0];

            return array($fv => $this->types($types));
        }

        preg_match(
            '#(\\\?[a-z]+)\s*⟺\s*(\\\?[a-z]+)#iu',
            $expression,
            $matches
        );
        list(, $left, $right) = $matches;

        return array($left => $right);
    }

    /**
     * Types.
     *
     * @access  protected
     * @param   string     $types    Many types.
     * @return  array
     */
    protected function types ( $types ) {

        preg_match_all(
            '#([^∨]+)(?:\s*∨\s*(.*))?#msu',
            $types,
            $matches,
            PREG_SET_ORDER
        );
        $matches = $matches[0];
        array_shift($matches);
        $matches = array_map('trim', $matches);
        $result  = array();

        foreach($matches as $i => $type)
            $result[] = $this->type($type);

        return $result;
    }

    /**
     * Type.
     *
     * @access  protected
     * @param   string     $type    Type.
     * @return  array
     */
    protected function type ( $type ) {

        preg_match('#^([a-z]+)\((.*)?\)$#si', $type, $matches);
        list(, $type, $arguments) = $matches;

        return array($type => $arguments);
    }

    /**
     * Lists.
     *
     * @access  protected
     * @param   string     $list    A list.
     * @return  array
     */
    protected function lists ( $list ) {

        $result = explode('∨', $lists);
        $result = array_map('trim', $result);

        return $result;
    }

    /**
     * Extended.
     *
     * @access  protected
     * @param   string     $extended    An extended expression.
     * @return  array
     */
    protected function extended ( $extended ) {

        preg_match(
            '#([a-z]+)\s*←\s*([a-z]+)\s*:\s?(.*)#usi',
            $extended,
            $matches
        );
        list(, $name, $extends, $predicate) = $matches;

        return array($name => array($extends, $predicate . ';'));
    }

    /**
     * Array.
     *
     * @access  protected
     * @param   string     $array    An array.
     * @return  string
     */
    protected function arr ( $array ) {

        if(empty($array[0]))
            return '';

        $array  = preg_replace_callback('#(\[(.*)\])?#', array($this, 'arr'), $array[2]);
        $hop    = explode(';', $array);
        $out    = 'array(';
        $handle = array();

        foreach($hop as $i => $entry)
            if(preg_match('#(.*)\s*→\s*(.*)#s', $entry, $matches)) {

                list(, $key, $value) = array_map('trim', $matches);
                $handle[] = 'array(' . $key . ', ' . $value . ')';
            }
            else
                $handle[] = 'array(' . trim($entry) . ')';

        $out .= implode(', ' . "\n", $handle) . ')';
        $out  = str_replace('***', '', $out);

        return $out;
    }

    /**
     * Argument.
     *
     * @access  protected
     * @param   string     $argument    An argument.
     * @return  string
     */
    protected function arg ( $argument ) {

        $argument[2] = preg_replace_callback('#([a-z]+)\s*\(([^\)]*)#is',
                                             array($this, 'arg'),
                                             $argument[2]);

        if(empty($argument[0]))
            return '';

        if(trim($argument[2]) == '')
            return '$praspel->type(\'' . $argument[1] . '\')';

        return '$praspel->type(\'' . $argument[1] . '\', ' . $argument[2] . '***';
    }

    /**
     * Return the buffer.
     *
     * @access  public
     * @return  array
     */
    public function getBuffer ( ) {

        return $this->buffer;
    }

    /**
     * Compile.
     *
     * @access  public
     * @param   string  $praspel    Praspel.
     * @return  string
     */
    public static function compile ( $praspel ) {

        $compiler = new self($praspel);
        $out      = '$praspel = new Hoa_Test_Praspel();' . "\n\n";

        foreach($compiler->getBuffer() as $clauseName => $expressions) {

            $oout = '$praspel->clause(\'' . $clauseName . '\')' . "\n"; 

            switch($clauseName) {

                case 'requires':
                case 'ensures':
                case 'invariant':
                    foreach($expressions as $freeVariable => $types) {

                        $ooout = $oout .
                                 '        ->declareFreeVariable(\'' . $freeVariable . '\')' . "\n";

                        if(!is_array($types)) {

                            $out .= $ooout .
                                    '        ->depends(\'' . $types . '\');' . "\n\n";

                            continue;
                        }

                        foreach($types as $i => $type) {

                            $t = key($type);
                            $a = current($type);

                            $a = preg_replace_callback('#([a-z]+)\s*\(([^\)]*)\)#',
                                                       array($compiler, 'arg'),
                                                       $a);
                            $a = preg_replace_callback('#(\[(.*)\])?#', array($compiler, 'arr'), $a);
                            $a = str_replace('***', ')', $a);

                            $out .= $ooout .
                                    '        ->hasType(\'' . $t . '\'' .
                                    (!empty($a)
                                        ? ', ' . $a
                                        : ''
                                    ) .');' . "\n\n";
                        }
                    }
                  break;

                case 'throws':
                    $out .= $oout .
                            '        ->lists(array(\'' .
                            implode('\', \'', $expressions) .
                            '\'));' . "\n\n";
                  break;

                case 'predicate':

                    $n = key($expressions);
                    $f = $expressions[$n][0];
                    $d = stripslashes($expressions[$n][1]);

                    $out .= $oout .
                            '        ->new(\'' . $n . '\')' . "\n" .
                            '        ->from(\'' . $f . '\')' . "\n" .
                            '        ->defines(\'' . $d . '\');' . "\n\n";
                  break;
            }
        }

        return $out;
    }
}
