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

namespace Hoa\Praspel\Iterator\Coverage;

use Hoa\Iterator;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Iterator\Coverage\Structural.
 *
 * Structural coverage.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Structural implements Iterator\Recursive
{
    /**
     * State of the iterator: will compute a @requires clause.
     *
     * @const int
     */
    const STATE_REQUIRES  = 0;

    /**
     * State of the iterator: will compute a @behavior clause.
     *
     * @const int
     */
    const STATE_BEHAVIOR  = 1;

    /**
     * State of the iterator: will compute an @ensures clause.
     *
     * @const int
     */
    const STATE_ENSURES   = 2;

    /**
     * State of the iterator: will compute a @throwable clause.
     *
     * @const int
     */
    const STATE_THROWABLE = 3;

    /**
     * Specification to cover.
     *
     * @var \Hoa\Praspel\Model\Specification
     */
    protected $_specification = null;

    /**
     * Coverage criteria.
     *
     * @var int
     */
    protected $_criteria      = 3; //   Coverage::CRITERIA_NORMAL
                                   // | Coverage::CRITERIA_EXCEPTIONAL

    /**
     * Path.
     *
     * @var \SplQueue
     */
    protected $_path          = null;

    /**
     * Stack (to manage backtracks, yields, etc.)
     *
     * @var \SplStack
     */
    protected $_stack         = null;

    /**
     * Pop queue (when we should pop an element from the path).
     *
     * @var \SplQueue
     */
    protected $_pop           = null;

    /**
     * Key.
     *
     * @var int
     */
    protected $_key           = -1;

    /**
     * Current (with two indexes: pre and post, with SplStack
     * associated).
     *
     * @var array
     */
    protected $_current       = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     */
    public function __construct(Praspel\Model\Specification $specification)
    {
        $this->_specification = $specification;
        $this->setCriteria(
            Coverage::CRITERIA_NORMAL | Coverage::CRITERIA_EXCEPTIONAL
        );

        return;
    }

    /**
     * Get the current value.
     *
     * @return  array
     */
    public function current()
    {
        $out = ['pre' => [], 'post' => []];

        foreach ($this->_path as $element) {
            if ($element instanceof Praspel\Model\Requires) {
                $out['pre'][] = $element;
            } else {
                $out['post'][] = $element;
            }
        }

        return $out;
    }

    /**
     * Get the current key.
     *
     * @return  int
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Advance the internal collection pointer, and return the current value.
     *
     * @return  void
     */
    public function next()
    {
        $this->_current = null;

        if (0 === count($this->_stack)) {
            return;
        }

        while (0 === $this->_pop->top()) {
            $this->_pop->pop();
            $this->_path->pop();
            $this->_pop->push($this->_pop->pop() - 1);
        }

        list($behavior, $state) = array_values($this->_stack->pop());

        switch ($state) {
            case static::STATE_REQUIRES:
                ++$this->_key;

                if (true === $behavior->clauseExists('requires')) {
                    $this->_current = $behavior->getClause('requires');
                    $this->_path->push($this->_current);
                } else {
                    $this->_current = true;
                    $this->_path->push(null);
                }

                if (true === $behavior->clauseExists('behavior')) {
                    $behaviors = $behavior->getClause('behavior')->getIterator();
                    $this->_stack->push([
                        'behavior' => $behavior,
                        'state'    => static::STATE_BEHAVIOR
                    ]);
                    $this->_stack->push([
                        'behavior' => $behaviors,
                        'state'    => static::STATE_BEHAVIOR
                    ]);

                    $this->_pop->push(
                        count($behaviors)
                      + (2 * $behavior->clauseExists('default'))
                    );
                } else {
                    $this->_stack->push([
                        'behavior' => $behavior,
                        'state'    => static::STATE_ENSURES
                    ]);
                    $this->_pop->push(2);
                    $this->next();
                }

                break;

            case static::STATE_BEHAVIOR:
                if (true === $behavior->valid()) {
                    $this->_stack->push([
                        'behavior' => $behavior,
                        'state'    => static::STATE_BEHAVIOR
                    ]);
                    $this->_stack->push([
                        'behavior' => $behavior->current(),
                        'state'    => static::STATE_REQUIRES
                    ]);
                    $behavior->next();
                    $this->next();

                    break;
                }

                list($parentBehavior, ) = array_values($this->_stack->pop());

                if (true === $parentBehavior->clauseExists('default')) {
                    $this->_stack->push([
                        'behavior' => $parentBehavior->getClause('default'),
                        'state'    => static::STATE_ENSURES
                    ]);
                }

                $this->next();

                break;

            case static::STATE_ENSURES:
                $this->_stack->push([
                    'behavior' => $behavior,
                    'state'    => static::STATE_THROWABLE
                ]);

                if (false === $behavior->clauseExists('ensures') ||
                    0     === (Coverage::CRITERIA_NORMAL & $this->getCriteria())) {
                    $this->_pop->push($this->_pop->pop() - 1);
                    $this->next();

                    break;
                }

                ++$this->_key;
                $this->_current = $behavior->getClause('ensures');
                $this->_path->push($this->_current);
                $this->_pop->push(0);

                break;

            case static::STATE_THROWABLE:
                if (false === $behavior->clauseExists('throwable') ||
                    0     === (Coverage::CRITERIA_EXCEPTIONAL & $this->getCriteria())) {
                    $this->_pop->push($this->_pop->pop() - 1);
                    $this->next();

                    break;
                }

                ++$this->_key;
                $this->_current = $behavior->getClause('throwable');

                $this->_path->push($this->_current);
                $this->_pop->push(0);

                break;
        }

        return;
    }

    /**
     * Rewind the internal collection pointer, and return the first collection.
     *
     * @return  array
     */
    public function rewind()
    {
        $this->_key = -1;

        unset($this->_path);
        $this->_path = new Praspel\Iterator\WeakStack();

        unset($this->_stack);
        $this->_stack = new \SplStack();
        $this->_stack->push([
            'behavior' => $this->_specification,
            'state'    => static::STATE_REQUIRES
        ]);

        unset($this->_pop);
        $this->_pop = new \SplQueue();
        $this->_pop->push(-1);

        $this->next();

        return $this->current();
    }

    /**
     * Check if there is a current element after calls to the rewind() or the
     * next() methods.
     *
     * @return  bool
     */
    public function valid()
    {
        return null !== $this->_current;
    }

    /**
     * Set coverage criteria.
     *
     * @param   int  $criteria    Criteria (please, see Coverage::CRITERIA_*
     *                            constants).
     * @return  int
     */
    public function setCriteria($criteria)
    {
        if (0 !== (Coverage::CRITERIA_DOMAIN & $criteria) &&
            0 !== (Coverage::CRITERIA_EXCEPTIONAL & $criteria)) {
            throw new Praspel\Exception\Generic(
                'Mixing CRITERIA_EXCEPTIONAL and CRITERIA_DOMAIN is not ' .
                'supported yet. Sorry.',
                0
            );
        }

        $old             = $this->_criteria;
        $this->_criteria = $criteria;

        return $old;
    }

    /**
     * get coverage criteria.
     *
     * @return  int
     */
    public function getCriteria()
    {
        return $this->_criteria;
    }

    /**
     * Check if we can go deeper (structural to domain coverage).
     *
     * @return  bool
     */
    public function hasChildren()
    {
        return 0 !== (Coverage::CRITERIA_DOMAIN & $this->getCriteria());
    }

    /**
     * Get the domain coverage iterator from the current variables from pre and
     * post clauses.
     *
     * @return  \Hoa\Iterator\Recursive
     */
    public function getChildren()
    {
        $variables = [];
        $current   = $this->current();

        foreach ($current['pre'] as $clause) {
            foreach ($clause->getLocalVariables() as $variable) {
                $variables[] = $variable;
            }
        }

        foreach ($current['post'] as $clause) {
            foreach ($clause->getLocalVariables() as $variable) {
                $variables[] = $variable;
            }
        }

        return new Iterator\Recursive\Mock(
            new Iterator\Demultiplexer(
                new Domain($variables),
                function ($current) {
                    $out = ['pre' => [], 'post' => []];

                    foreach ($current as $name => $variable) {
                        $clause = $variable->getHolder()->getClause();

                        if ($clause instanceof Praspel\Model\Requires) {
                            $out['pre'][$name]  = $variable;
                        } else {
                            $out['post'][$name] = $variable;
                        }
                    }

                    return $out;
                }
            )
        );
    }
}
