<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Visitor\Registry
 */
-> import('Visitor.Registry');

}

namespace Hoa\Test\Praspel\Visitor {

/**
 * Class \Hoa\Test\Praspel\Visitor\Php.
 *
 * .
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Php extends \Hoa\Visitor\Registry {

    /**
     * Initialize aggregated visitor.
     *
     * @access  access
     * @return  void
     */
    public function __construct ( ) {

        $this->addEntry(
            'Hoa\Test\Praspel\Contract',
            array($this, 'visitContract')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Clause\Ensures',
            array($this, 'visitClauseContract')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Clause\Invariant',
            array($this, 'visitClauseContract')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Clause\Requires',
            array($this, 'visitClauseContract')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Clause\Throwable',
            array($this, 'visitClauseThrowable')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Variable',
            array($this, 'visitDomainDisjunction')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Constructor\Old',
            array($this, 'visitConstructorOld')
        );
        $this->addEntry(
            'Hoa\Test\Praspel\Constructor\Result',
            array($this, 'visitDomainDisjunction')
        );

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitContract ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitClauseContract ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitClauseThrowable ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitDomainDisjunction ( \Hoa\Visitor\Element $element,
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
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                &$handle    Handle (reference).
     * @param   mixed                $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function visitConstructorOld ( \Hoa\Visitor\Element $element,
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
                        if(true === $f)
                            $out[] = $spaces . '    ->belongsTo(\'' .
                                         $argument->getName() .
                                     '\')' . "\n" .
                                     $spaces . '      ->with(' .
                                     ($argument->getConstantValue()
                                         ? 'true'
                                         : 'false') .
                                     ')' . "\n" .
                                     $spaces . '      ->_ok()' . "\n";
                        else
                            $out[] = $spaces . '    ->with(' .
                                     ($argument->getConstantValue()
                                         ? 'true'
                                         : 'false') .
                                     ')' . "\n";
                      break;

                    case 'constfloat':
                    case 'constinteger':
                        if(true === $f)
                            $out[] = $spaces . '    ->belongsTo(\'' .
                                         $argument->getName() .
                                     '\')' . "\n" .
                                     $spaces . '      ->with(' .
                                     (string) $argument->getConstantValue() .
                                     ')' . "\n" .
                                     $spaces . '      ->_ok()' . "\n";
                        else
                            $out[] = $spaces . '    ->with(' .
                                     (string) $argument->getConstantValue() .
                                     ')' . "\n";
                      break;

                    case 'conststring':
                        if(true === $f)
                            $out[] = $spaces . '    ->belongsTo(\'' .
                                         $argument->getName() .
                                     '\')' . "\n" .
                                     $spaces . '      ->with(\'' .
                                     str_replace(
                                         "'",
                                         "\'",
                                         $argument->getConstantValue()
                                     ) .
                                     '\')' . "\n" .
                                     $spaces . '      ->_ok()' . "\n";
                        else
                            $out[] = $spaces . '    ->with(\'' .
                                     str_replace(
                                         "'",
                                         "\'",
                                         $argument->getConstantValue()
                                     ) .
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

}
