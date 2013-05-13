<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Praspel\Iterator\WeakStack
 */
-> import('Praspel.Iterator.WeakStack')

/**
 * \Hoa\Praspel\Iterator\Coverage
 */
-> import('Praspel.Iterator.Coverage.~')

/**
 * \Hoa\Praspel\Iterator\Coverage\Domain
 */
-> import('Praspel.Iterator.Coverage.Domain')

/**
 * \Hoa\Iterator\Recursive
 */
-> import('Iterator.Recursive.~')

/**
 * \Hoa\Iterator\Multiple
 */
-> import('Iterator.Multiple')

/**
 * \Hoa\Iterator\Recursive\Mock
 */
-> import('Iterator.Recursive.Mock');

}

namespace Hoa\Praspel\Iterator\Coverage {

/**
 * Class \Hoa\Praspel\Iterator\Coverage\Structural.
 *
 * Structural coverage.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Structural implements \Hoa\Iterator\Recursive {

    /**
     * Specification to cover.
     *
     * @var \Hoa\Praspel\Model\Specification object
     */
    protected $_specification = null;

    /**
     * Coverage criteria.
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Structural int
     */
    protected $_criteria      = 3; //   Coverage::CRITERIA_NORMAL
                                   // | Coverage::CRITERIA_EXCEPTIONAL

    /**
     * Stack (to manage backtracks, yields, etc.)
     *
     * @var \SplStack object
     */
    protected $_stack         = null;

    /**
     * Key.
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Structural int
     */
    protected $_key           = -1;

    /**
     * Current (with two indexes: pre and post, with SplStack
     * associated).
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Structural array
     */
    protected $_current       = null;

    /**
     * Whether the algorithm is backtracking or not.
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Structural bool
     */
    protected $_up            = false;

    /**
     * Post-condition clause: ensure or throwable.
     *
     * @var \Hoa\Praspel\Iterator\Coverage\Structural string
     */
    protected $_post          = 'ensures';



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @return  void
     */
    public function __construct ( \Hoa\Praspel\Model\Specification $specification ) {

        $this->_specification = $specification;
        $this->setCriteria(
            Coverage::CRITERIA_NORMAL | Coverage::CRITERIA_EXCEPTIONAL
        );

        return;
    }

    /**
     * Get the current value.
     *
     * @access  public
     * @return  array
     */
    public function current ( ) {

        return $this->_current;
    }

    /**
     * Get the current key.
     *
     * @access  public
     * @return  int
     */
    public function key ( ) {

        return $this->_key;
    }

    /**
     * Advance the internal collection pointer, and return the current value.
     *
     * @access  public
     * @return  array
     */
    public function next ( ) {

        $collection = $this->_stack->top();

        if($collection instanceof \Hoa\Praspel\Model\Specification) {

            if(true === $this->_up) {

                if(   'ensures' === $this->_post
                   && 0 !== (Coverage::CRITERIA_EXCEPTIONAL & $this->getCriteria())) {

                    $this->_up   = false;
                    $this->_post = 'throwable';
                    $this->_rewindCurrent();

                    return $this->current();
                }

                unset($this->_current);
                $this->_current = null;

                return $this->current();
            }

            if(true === $collection->clauseExists('behavior'))
                return $this->_next($collection);

            $this->_up = true;

            return $this->next();
        }

        $handle = current($collection);

        if(   false === $this->_up
           && true  === $handle->clauseExists('behavior'))
            return $this->_next($handle);

        $this->_current['pre']->pop();
        $this->_current['post']->pop();

        next($collection);
        $handle = current($collection);

        if(false === $handle) {

            $this->_stack->pop();
            $this->_up = true;

            return $this->next();
        }

        $this->_up = false;
        $this->pushCurrent($handle);

        ++$this->_key;

        return $this->current();
    }

    /**
     * Common (and inline) parts of the $this->next() method.
     *
     * @access  private
     * @param   \Hoa\Praspel\Model\Behavior  $handle    Handle.
     * @return  array
     */
    private function _next ( \Hoa\Praspel\Model\Behavior $handle ) {

        $iterator = $handle->getClause('behavior')->getIterator();
        $this->_stack->push($iterator);
        $current  = current($iterator);

        if(false === $current) {

            $this->_up = true;

            return $this->next();
        }

        $this->pushCurrent($current);
        ++$this->_key;

        return $this->current();
    }

    /**
     * Rewind the internal collection pointer, and return the first collection.
     *
     * @access  public
     * @return  array
     */
    public function rewind ( ) {

        $this->_up  = false;
        $this->_key = 0;

        unset($this->_stack);
        $this->_stack = new \SplStack();
        $this->_stack->push($this->_specification);

        $this->_post = 0 !== (Coverage::CRITERIA_NORMAL & $this->getCriteria())
                           ? 'ensures'
                           : 'throwable';

        $this->_rewindCurrent();

        return $this->current();
    }

    /**
     * Rewind $this->_current.
     *
     * @access  protected
     * @return  void
     */
    protected function _rewindCurrent ( ) {

        $stack = new \Hoa\Praspel\Iterator\WeakStack();
        $stack->setIteratorMode(
            \SplDoublyLinkedList::IT_MODE_LIFO
          | \SplDoublyLinkedList::IT_MODE_KEEP
        );
        unset($this->_current);
        $this->_current = array(
            'pre'  =>       $stack,
            'post' => clone $stack
        );
        $this->pushCurrent($this->_stack->top());

        return;
    }

    /**
     * Push pre and post clauses in $this->_current.
     *
     * @access  protected
     * @param   \Hoa\Praspel\Model\Behavior  $current    Current.
     * @return  void
     */
    protected function pushCurrent ( \Hoa\Praspel\Model\Behavior $current ) {

        $pre  = null;
        $post = null;

        if(true === $current->clauseExists('requires'))
            $pre = $current->getClause('requires');

        if(true === $current->clauseExists($this->_post))
            $post = $current->getClause($this->_post);

        $this->_current['pre']->push($pre);
        $this->_current['post']->push($post);

        return;
    }

    /**
     * Check if there is a current element after calls to the rewind() or the
     * next() methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        return null !== $this->_current;
    }

    /**
     * Set coverage criteria.
     *
     * @access  public
     * @param   int  $criteria    Criteria (please, see Coverage::CRITERIA_*
     *                            constants).
     * @return  int
     */
    public function setCriteria ( $criteria ) {

        $old             = $this->_criteria;
        $this->_criteria = $criteria;

        return $old;
    }

    /**
     * get coverage criteria.
     *
     * @access  public
     * @return  int
     */
    public function getCriteria ( ) {

        return $this->_criteria;
    }

    /**
     * Check if we can go deeper (structural to domain coverage).
     *
     * @access  public
     * @return  bool
     */
    public function hasChildren ( ) {

        return 0 !== (Coverage::CRITERIA_DOMAIN & $this->getCriteria());
    }

    /**
     * Get the domain coverage iterator from the current variables from pre and
     * post clauses.
     *
     * @access  public
     * @return  \Hoa\Iterator\Recursive
     */
    public function getChildren ( ) {

        $iterator = new \Hoa\Iterator\Multiple(
            \Hoa\Iterator\Multiple::MIT_NEED_ALL
          | \Hoa\Iterator\Multiple::MIT_KEYS_ASSOC
        );
        $pre      = array();
        $post     = array();

        foreach($this->_current['pre'] as $clause)
            foreach($clause->getLocalVariables() as $variable)
                $pre[] = $variable;

        foreach($this->_current['post'] as $clause)
            foreach($clause->getLocalVariables() as $variable)
                $post[] = $variable;

        $iterator->attachIterator(new Domain($pre), 'pre');
        $iterator->attachIterator(new Domain($post), 'post');

        return new \Hoa\Iterator\Recursive\Mock($iterator);
    }
}

}
