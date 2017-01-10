<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Praspel\Visitor;

use Hoa\Praspel as HoaPraspel;
use Hoa\Realdom;
use Hoa\Visitor;

/**
 * Class \Hoa\Praspel\Visitor\Praspel.
 *
 * Compile the model to Praspel code.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Praspel implements Visitor\Visit
{
    /**
     * Visit an element.
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function visit(
        Visitor\Element $element,
        &$handle = null,
        $eldnah  = null
    ) {
        $out = null;

        // Hoa\Praspel.

        if ($element instanceof HoaPraspel\Model\Specification) {
            $oout = [];

            foreach ($element::getAllowedClauses() as $clause) {
                if (true === $element->clauseExists($clause)) {
                    $oout[] = $element->getClause($clause)->accept(
                        $this,
                        $handle,
                        $eldnah
                    );
                }
            }

            $out = implode("\n", $oout);
        } elseif ($element instanceof HoaPraspel\Model\Is) {
            $out = '@is ' . $element->getPropertyName() . ';';
        } elseif ($element instanceof HoaPraspel\Model\Declaration) {
            $clause = $element->getName();
            $out    = '@' . $clause;
            $oout   = [];

            foreach ($element->getLocalVariables() as $name => $var) {
                $oout[] = ' ' . $var->accept($this, $handle, $eldnah);
            }

            foreach ($element->getPredicates() as $predicate) {
                $oout[] = ' \pred(\'' . $predicate . '\')';
            }

            $out .= implode(' and', $oout) . ';';
        } elseif ($element instanceof HoaPraspel\Model\Variable) {
            $name = $element->getName();

            if (true === $element->isLocal()) {
                $out = 'let ';
            }

            $out .= $name;

            if (null === $alias = $element->getAlias()) {
                $out .=
                    ': ' .
                    $element->getDomains()->accept($this, $handle, $eldnah);
            } else {
                $out .= ' domainof ' . $alias;
            }

            $constraints = $element->getConstraints();

            if (isset($constraints['is'])) {
                $out .=
                    ' and ' . $name . ' is ' .
                    implode(', ', $constraints['is']);
            }

            if (isset($constraints['contains'])) {
                foreach ($constraints['contains'] as $contains) {
                    $out .=
                        ' and ' . $name . ' contains ' .
                        $contains->accept($this, $handle, $eldnah);
                }
            }

            if (isset($constraints['key'])) {
                foreach ($constraints['key'] as $pairs) {
                    $out .=
                        ' and ' . $name . '[' .
                        $pairs[0]->accept($this, $handle, $eldnah) .
                        ']: ' . $pairs[1]->accept($this, $handle, $eldnah);
                }
            }
        } elseif ($element instanceof HoaPraspel\Model\Throwable) {
            $oout = [];

            foreach ($element as $identifier) {
                $exception = $element[$identifier];

                if (true === $exception->isDisjointed()) {
                    continue;
                }

                $line =
                    ' ' . $exception->getInstanceName() . ' ' .
                    $identifier;

                foreach ((array) $exception->getDisjunction() as $_identifier) {
                    $_exception = $element[$_identifier];
                    $line      .=
                        ' or ' . $_exception->getInstanceName() . ' ' .
                        $_identifier;
                }

                if (null !== $with = $exception->getWith()) {
                    $line .= ' with ';
                    $liine = [];

                    foreach ($with as $var) {
                        $liine[] = $var->accept($this, $handle, $eldnah);
                    }

                    foreach ($with->getPredicates() as $predicate) {
                        $liine[] = '\pred(\'' . $predicate . '\')';
                    }

                    $line .= implode(' and ', $liine);
                }

                $oout[] = $line;
            }

            $out = '@throwable' . implode(' or', $oout) . ';';
        } elseif ($element instanceof HoaPraspel\Model\DefaultBehavior) {
            $out  = '@default {' . "\n";
            $oout = [];

            foreach ($element::getAllowedClauses() as $clause) {
                if (true === $element->clauseExists($clause)) {
                    $oout[] = '    ' . str_replace(
                        "\n",
                        "\n" . '    ',
                        $element->getClause($clause)->accept(
                            $this,
                            $handle,
                            $eldnah
                        )
                    );
                }
            }

            $out .= implode("\n", $oout) . "\n" . '}';
        } elseif ($element instanceof HoaPraspel\Model\Behavior) {
            $out  = '@behavior ' . $element->getIdentifier() . ' {' . "\n";
            $oout = [];

            foreach ($element::getAllowedClauses() as $clause) {
                if (true === $element->clauseExists($clause)) {
                    $oout[] = '    ' . str_replace(
                        "\n",
                        "\n" . '    ',
                        $element->getClause($clause)->accept(
                            $this,
                            $handle,
                            $eldnah
                        )
                    );
                }
            }

            $out .= implode("\n", $oout) . "\n" . '}';
        } elseif ($element instanceof HoaPraspel\Model\Description) {
            $oout = [];

            foreach ($element as $example) {
                $oout[] =
                    '@description \'' .
                    preg_replace('#(?<!\\\)\'#', '\\\'', $example) .
                    '\';';
            }

            $out = implode("\n", $oout);
        } elseif ($element instanceof HoaPraspel\Model\Collection) {
            foreach ($element as $el) {
                $out .= $el->accept($this, $handle, $eldnah);
            }
        }

        // Hoa\Realdom.

        elseif ($element instanceof Realdom\Disjunction) {
            $realdoms = $element->getUnflattenedRealdoms();

            if (!empty($realdoms)) {
                $oout = [];

                foreach ($realdoms as $realdom) {
                    $oout[] = $realdom->accept($this, $handle, $eldnah);
                }

                $out .= implode(' or ', $oout);
            }
        } elseif ($element instanceof Realdom) {
            if ($element instanceof Realdom\IRealdom\Constant) {
                $out .= $element->getConstantRepresentation();
            } else {
                $oout = [];

                foreach ($element->getArguments() as $argument) {
                    $oout[] = $argument->accept($this, $handle, $eldnah);
                }

                $out .= $element->getName() . '(' . implode(', ', $oout) . ')';
            }
        } elseif ($element instanceof Realdom\Crate\Constant) {
            $praspel  = $element->getPraspelRepresentation();
            $out     .= $praspel();
        } elseif ($element instanceof Realdom\Crate\Variable) {
            $out .= $element->getVariable()->getName();
        } else {
            throw new HoaPraspel\Exception\Compiler(
                '%s is not yet implemented.',
                0,
                get_class($element)
            );
        }

        return $out;
    }
}
