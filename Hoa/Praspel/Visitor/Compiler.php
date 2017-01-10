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

use Hoa\Praspel;
use Hoa\Realdom;
use Hoa\Visitor;

/**
 * Class \Hoa\Praspel\Visitor\Compiler.
 *
 * Compile the model to PHP code.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Compiler implements Visitor\Visit
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

        if ($element instanceof Praspel\Model\Specification) {
            $variable = '$' . $element->getId();
            $out      = $variable . ' = new \Hoa\Praspel\Model\Specification();' . "\n";

            foreach ($element::getAllowedClauses() as $clause) {
                if (true === $element->clauseExists($clause)) {
                    $out .= $element->getClause($clause)->accept(
                        $this,
                        $handle,
                        $eldnah
                    );
                }
            }
        } elseif ($element instanceof Praspel\Model\Is) {
            $variable = '$' . $element->getParent()->getId();
            $out      =
                "\n" .
                $variable . '->getClause(\'is\')->setProperty(' .
                $element->getProperty() .
                ');' . "\n";
        } elseif ($element instanceof Praspel\Model\Declaration) {
            $variable = '$' . ($eldnah ?: $element->getId());
            $out      =
                "\n" .
                $variable . ' = $' . $element->getParent()->getId() .
                '->getClause(\'' . $element->getName() . '\');' . "\n";

            foreach ($element->getLocalVariables() as $var) {
                $out .= $var->accept($this, $handle, $eldnah);
            }

            foreach ($element->getPredicates() as $predicate) {
                $out .= $variable . '->predicate(\'' . $predicate . '\');' . "\n";
            }
        } elseif ($element instanceof Praspel\Model\Variable) {
            $variable = '$' . ($eldnah ?: $element->getClause()->getId());
            $name     = $element->getName();
            $start    = $variable . '[\'' . $name . '\']';

            if (true === $element->isLocal()) {
                $out .= $variable . '->let[\'' . $name . '\']';
            } else {
                $out .= $start;
            }

            if (null !== $alias = $element->getAlias()) {
                $out .= '->domainof(\'' . $alias . '\');' . "\n";
            } else {
                $out .=
                    '->in = ' .
                    $element->getDomains()->accept($this, $handle, $eldnah) .
                    ';' . "\n";
            }

            $constraints = $element->getConstraints();

            if (isset($constraints['is'])) {
                $out .=
                    $start . '->is(\'' .
                    implode('\', \'', $constraints['is']) . '\');' .
                    "\n";
            }

            if (isset($constraints['contains'])) {
                foreach ($constraints['contains'] as $contains) {
                    $out .= $start . '->contains(' . $contains . ');' . "\n";
                }
            }

            if (isset($constraints['key'])) {
                foreach ($constraints['key'] as $pairs) {
                    $out .=
                        $start . '->key(' . $pairs[0] . ')->in = ' .
                        $pairs[1] . ';' . "\n";
                }
            }
        } elseif ($element instanceof Praspel\Model\Throwable) {
            $parent    = '$' . $element->getParent()->getId();
            $_variable = $element->getId();
            $variable  = '$' . $_variable;
            $out       =
                "\n" .
                $variable . ' = ' . $parent .
                '->getClause(\'throwable\');' . "\n";

            foreach ($element as $identifier) {
                $exception  = $element[$identifier];
                $start      = $variable . '[\'' . $identifier . '\']';
                $out       .= $start . ' = \'' . $exception->getInstanceName() . '\';' . "\n";

                if (false === $element->isDisjointed()) {
                    if (null !== $with = $element->getWith()) {
                        $temp = $_variable . '_' . $identifier . '_with';
                        $out .=
                            '$' . $temp . ' = ' .
                            $variable . '->newWith();' . "\n";

                        foreach ($with->getLocalVariables() as $var) {
                            $out .= $var->accept($this, $handle, $temp);
                        }

                        foreach ($with->getPredicates() as $predicate) {
                            $out .=
                                '$' . $temp . '->predicate(\'' . $predicate .
                                '\');' . "\n";
                        }

                        $out .= $start . '->setWith($' . $temp . ');' . "\n";
                    }
                } else {
                    $out .=
                        $start . '->disjunctionWith(\'' .
                        $exception->getDisjunction() . '\');' . "\n";
                }
            }
        } elseif ($element instanceof Praspel\Model\DefaultBehavior) {
            $out =
                "\n" .
                '$' . $element->getId() . ' = $' .
                $element->getParent()->getId() .
                '->getClause(\'default\')' . "\n";

            foreach ($element::getAllowedClauses() as $clause) {
                if (true === $element->clauseExists($clause)) {
                    $out .= $element->getClause($clause)->accept(
                        $this,
                        $handle,
                        $eldnah
                    );
                }
            }
        } elseif ($element instanceof Praspel\Model\Behavior) {
            $out =
                "\n" .
                '$' . $element->getId() . ' = $' .
                $element->getParent()->getId() .
                '->getClause(\'behavior\')' .
                '->get(\'' . $element->getIdentifier() . '\');' . "\n";

            foreach ($element::getAllowedClauses() as $clause) {
                if (true === $element->clauseExists($clause)) {
                    $out .= $element->getClause($clause)->accept(
                        $this,
                        $handle,
                        $eldnah
                    );
                }
            }
        } elseif ($element instanceof Praspel\Model\Description) {
            $parent   = '$' . $element->getParent()->getId();
            $variable = '$' . $element->getId();
            $out      =
                "\n" .
                $variable . ' = ' . $parent .
                '->getClause(\'description\');' . "\n";

            foreach ($element as $example) {
                $out .=
                    $variable . '[] = \'' .
                    preg_replace('#(?<!\\\)\'#', '\\\'', $example) .
                    '\';' . "\n";
            }
        } elseif ($element instanceof Praspel\Model\Collection) {
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
                    if ($realdom instanceof Realdom\IRealdom\Constant) {
                        $oout[] =
                            'const(' .
                            $realdom->accept($this, $handle, $eldnah) .
                            ')';
                    } else {
                        $oout[] = $realdom->accept($this, $handle, $eldnah);
                    }
                }

                $out .= 'realdom()->' . implode('->or->', $oout);
            }
        } elseif ($element instanceof Realdom) {
            if ($element instanceof Realdom\IRealdom\Constant) {
                if ($element instanceof Realdom\_Array) {
                    $oout = [];

                    foreach ($element['pairs'] as $pair) {
                        $_oout = null;

                        foreach ($pair as $_pair) {
                            if (null !== $_oout) {
                                $_oout .= ', ';
                            }

                            $_oout .= $_pair->accept($this, $handle, $eldnah);
                        }

                        $oout[] = 'array(' . $_oout . ')';
                    }

                    $out .= 'array(' . implode(', ', $oout) . ')';
                } else {
                    $out .= $element->getConstantRepresentation();
                }
            } else {
                $oout = [];

                foreach ($element->getArguments() as $argument) {
                    $oout[] = $argument->accept($this, $handle, $eldnah);
                }

                $out .=
                    $element->getName() .
                    '(' . implode(', ', $oout) . ')';
            }
        } elseif ($element instanceof Realdom\Crate\Constant) {
            $holder  = $element->getHolder();
            $praspel = $element->getPraspelRepresentation();
            $out    .=
                '$' . $element->getDeclaration()->getId() .
                '[\'' . $praspel() . '\']';
        } elseif ($element instanceof Realdom\Crate\Variable) {
            $holder = $element->getVariable();

            if ($holder instanceof Praspel\Model\Variable\Implicit) {
                $out .=
                    'variable($' . $holder->getClause()->getId() .
                    '->getImplicitVariable(\'' . $holder->getName() .
                    '\'))';
            } else {
                $out .=
                    'variable($' . $holder->getClause()->getId() .
                    '->getVariable(\'' . $holder->getName() . '\', true))';
            }
        } else {
            throw new Praspel\Exception\Compiler(
                '%s is not yet implemented.',
                0,
                get_class($element)
            );
        }

        return $out;
    }
}
