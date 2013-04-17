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

class Structural implements \Iterator {

    /**
     * Specification to cover.
     *
     * @var \Hoa\Praspel\Model\Specification object
     */
    protected $_specification = null;

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
     * Current (with two indexes: requires and ensures, with SplStack
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
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Specification  $specification    Specification.
     * @return  void
     */
    public function __construct ( \Hoa\Praspel\Model\Specification $specification ) {

        $this->_specification = $specification;

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

        $countRequires = count($this->_current['requires']);
        $countEnsures  = count($this->_current['ensures']);

        if($countRequires >= $countEnsures)
            $this->_current['requires']->pop();

        if($countRequires <= $countEnsures)
            $this->_current['ensures']->pop();

        next($collection);
        $handle = current($collection);

        if(false === $handle) {

            $this->_stack->pop();
            $this->_up = true;

            return $this->next();
        }

        $this->_up = false;

        if(true === $handle->clauseExists('requires'))
            $this->_current['requires']->push(
                $handle->getClause('requires')
            );

        if(true === $handle->clauseExists('ensures'))
            $this->_current['ensures']->push(
                $handle->getClause('ensures')
            );

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

        if(true === $current->clauseExists('requires'))
            $this->_current['requires']->push(
                $current->getClause('requires')
            );

        if(true === $current->clauseExists('ensures'))
            $this->_current['ensures']->push(
                $current->getClause('ensures')
            );

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

        $iterator = new \SplStack();
        $iterator->setIteratorMode(
            \SplDoublyLinkedList::IT_MODE_LIFO
          | \SplDoublyLinkedList::IT_MODE_KEEP
        );

        $this->_up      = false;
        $this->_key     = 0;
        unset($this->_current);
        $this->_current = array(
            'requires' => $iterator,
            'ensures'  => clone $iterator
        );

        unset($this->_stack);
        $this->_stack   = new \SplStack();
        $this->_stack->push($this->_specification);
        $handle         = &$this->_specification;

        if(true === $handle->clauseExists('requires'))
            $this->_current['requires']->push(
                $handle->getClause('requires')
            );

        if(true === $handle->clauseExists('ensures'))
            $this->_current['ensures']->push(
                $handle->getClause('ensures')
            );

        return $this->current();
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
}

}
