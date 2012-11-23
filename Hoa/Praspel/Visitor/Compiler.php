<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Visitor\Visit
 */
-> import('Visitor.Visit');

}

namespace Hoa\Praspel\Visitor {

/**
 * Class \Hoa\Praspel\Visitor\Compiler.
 *
 * Compile the model to PHP code.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Compiler implements \Hoa\Visitor\Visit {

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function visit ( \Hoa\Visitor\Element $element,
                            &$handle = null, $eldnah = null ) {

        $out    = null;
        $parent = '$praspel';

        if(null !== $eldnah)
            $parent = '$' . substr($eldnah, 0, -1);

        if($element instanceof \Hoa\Praspel\Model\Specification) {

            $out     = $parent . ' = new \Hoa\Praspel\Model\Specification();' .  "\n";
            $clauses = array(
                'is',
                'invariant',
                'requires',
                'ensures',
                'behavior',
                'throwable',
                'forexample'
            );

            foreach($clauses as $clause)
                if(true === $element->clauseExists($clause))
                    $out .= $element->getClause($clause)->accept(
                        $this,
                        $handle,
                        $eldnah
                    );
        }
        elseif($element instanceof \Hoa\Praspel\Model\Is) {

            $out = "\n" .
                   $parent . '->getClause(\'is\')->setProperty(' .
                   $element->getProperty() .
                   ');' . "\n";
        }
        elseif($element instanceof \Hoa\Praspel\Model\Declaration) {

            $clause   = $element->getName();
            $variable = '$' . $eldnah . $clause;
            $out      = "\n" .
                        $variable . ' = ' . $parent .
                        '->getClause(\'' . $clause . '\');' . "\n";

            foreach($element as $name => $var) {

                $start  = $variable . '[\'' . $name . '\']';
                $out   .= $start;

                if(null === $alias = $var->getAlias())
                    $out .= '->in = ' . $var->getDomains() . ';' . "\n";
                else
                    $out .= '->domainof(\'' . $alias . '\');' . "\n";

                $constraints = $var->getConstraints();

                if(isset($constraints['is']))
                    $out .= $start . '->is(\'' .
                            implode('\', \'', $constraints['is']) . '\');' .
                            "\n";

                if(isset($constraints['contains']))
                    foreach($constraints['contains'] as $contains)
                        $out .= $start . '->contains(' . $contains . ');' . "\n";

                if(isset($constraints['key']))
                    foreach($constraints['key'] as $pairs)
                        $out .= $start . '->key(' . $pairs[0] . ')->in = ' .
                                $pairs[1] . ';' . "\n";
            }

            foreach($element->getPredicates() as $predicate)
                $out .= $variable . '->predicate(\'' . $predicate . '\');' . "\n";
        }
        elseif($element instanceof \Hoa\Praspel\Model\Throwable) {

            $variable = '$' . $eldnah . 'throwable';
            $out       = "\n" .
                         $variable . ' = ' . $parent .
                         '->getClause(\'throwable\');' . "\n";

            foreach($element->getExceptions() as $class)
                $out .= $variable . '->exception(\'' . $class . '\');' . "\n";
        }
        elseif($element instanceof \Hoa\Praspel\Model\Behavior) {

            $identifier = $element->getIdentifier();
            $variable   = '$' . $eldnah . 'behavior_' . $identifier;
            $out        = "\n" .
                          $variable . ' = ' . $parent .
                          '->getClause(\'behavior\');' . "\n" .
                          $variable . '->setIdentifier(\'' . $identifier . '\');' . "\n";
            $eldnah     = $eldnah . 'behavior_' . $identifier . '_';
            $clauses    = array(
                'invariant',
                'requires',
                'ensures',
                'behavior',
                'throwable'
            );

            foreach($clauses as $clause)
                if(true === $element->clauseExists($clause))
                    $out .= $element->getClause($clause)->accept(
                        $this,
                        $handle,
                        $eldnah
                    );
        }
        elseif($element instanceof \Hoa\Praspel\Model\Forexample) {

            $variable = '$' . $eldnah . 'forexample';
            $out      = "\n" .
                        $variable . ' = ' . $parent .
                        '->getClause(\'forexample\');' . "\n";

            foreach($element as $example)
                $out .= $variable . '[] = \'' .
                        preg_replace('#(?<!\\\)\'#', '\\\'', $example) .
                        '\';' . "\n";
        }
        else
            throw new \Hoa\Core\Exception(
                '%s is not yet implemented.', 0, get_class($element));

        return $out;
    }
}

}
