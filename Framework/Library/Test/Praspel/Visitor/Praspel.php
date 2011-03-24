<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * Class \Hoa\Test\Praspel\Visitor\Praspel.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Praspel extends \Hoa\Visitor\Registry {

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
            array($this, 'visitDomainDisjunction')
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

        $out = null;

        foreach($element->getClauses() as $i => $clause)
            $out .= $clause->accept($this, $handle, $eldnah);

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
        $out    = sprintf(
                      '%-11s',
                      '@' . strtolower(substr($gc, strrpos($gc, '\\') + 1))
                  );
        $handle = array();

        foreach($element->getVariables() as $i => $variable)
            $handle[] = $variable->accept($this, $handle, $eldnah);

        return $out . implode("\n" . '       and ', $handle) . ";\n";
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

        return '@throwable ' . implode(', ', $element->getList()) . ";\n";
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

        return $element->getName() . ': ' .
               implode(' or ', $this->formatArguments($element->getDomains()));
    }

    /**
     * Format arguments to produce a Praspel string.
     *
     * @access  protected
     * @param   array      $arguments    Arguments to format.
     * @return  array
     */
    protected function formatArguments ( Array $arguments ) {

        $out = array();

        foreach($arguments as $i => $argument)
            if(is_array($argument)) {

                $handle = null;

                foreach($argument as $e => $domran) {

                    if(null !== $handle)
                        $handle .= ',';

                    if(!empty($domran[0]))
                        $handle .= "\n" . '               from ' .
                                   implode(
                                       ' or ',
                                       $this->formatArguments($domran[0])
                                   ) . ' ';

                    if(!empty($domran[1]))
                        $handle .= "\n" . '               to ' .
                                   implode(
                                       ' or ',
                                       $this->formatArguments($domran[1])
                                   );
                }

                $out[] = '[' . $handle . "\n" . '           ]';
            }
            elseif(is_object($argument)) {

                switch(strtolower($argument->getName())) {

                    case 'constboolean':
                        $out[] = $argument->getValue()
                                   ? 'true'
                                   : 'false';
                      break;

                    case 'constfloat':
                    case 'constinteger':
                        $out[] = (string) $argument->getValue();
                      break;

                    case 'conststring':
                        $out[] = '\'' .
                                 str_replace("'", "\'", $argument->getValue()) .
                                 '\'';
                      break;

                    default:
                        $out[] = $argument->getName() . '(' .
                                 implode(
                                    ', ',
                                    $this->formatArguments(
                                        $argument->getArguments()
                                    )
                                 ) . ')';
                }
            }

        return $out;
    }
}

}
