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
 * \Hoa\Praspel\Model\Clause
 */
-> import('Praspel.Model.Clause')

/**
 * \Hoa\Praspel\Model\Variable
 */
-> import('Praspel.Model.Variable');

}

namespace Hoa\Praspel\Model {

/**
 * Class \Hoa\Praspel\Model\Declaration.
 *
 * Represent a declaration.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Declaration
    extends    Clause
    implements \ArrayAccess,
               \IteratorAggregate,
               \Countable {

    /**
     * Declared variables.
     *
     * @var \Hoa\Praspel\Model\Declaration array
     */
    protected $_variables  = array();

    /**
     * Predicates.
     *
     * @var \Hoa\Praspel\Model\Declaration array
     */
    protected $_predicates = array();



    /**
     * Check if a variable exists.
     *
     * @access  public
     * @param   string  $offset    Variable name.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return isset($this->_variables[$offset]);
    }

    /**
     * Get or create a variable.
     *
     * @access  public
     * @param   string  $offset    Variable name.
     * @return  \Hoa\Prasel\Model\Variable
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset))
            return $this->_variables[$offset] = new Variable($offset, $this);

        return $this->_variables[$offset];
    }

    /**
     * Set a value to a variable.
     *
     * @access  public
     * @param   string  $offset    Variable name.
     * @param   mixed   $value     Variable value.
     * @return  mixed
     */
    public function offsetSet ( $offset, $value ) {

        $variable = $this->offsetGet($offset);
        $old      = $variable->getValue();
        $variable->setValue($value);

        return $old;
    }

    /**
     * Delete a variable.
     *
     * @access  public
     * @param   string  $offset    Variable name.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        unset($this->_variables[$offset]);

        return;
    }

    /**
     * Iterator over local variables.
     *
     * @access  public
     * @return  \ArrayObject
     */
    public function getIterator ( ) {

        return new \ArrayObject($this->getLocalVariables());
    }

    /**
     * Count number of variables.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_variables);
    }

    /**
     * Get local variables.
     *
     * @access  public
     * @return  array
     */
    public function &getLocalVariables ( ) {

        return $this->_variables;
    }

    /**
     * Get in-scope variables.
     *
     * @access  public
     * @return  array
     */
    public function getInScopeVariables ( ) {

        $out     = array();
        $clause  = $this->getName();
        $current = $this;

        while(null !== $current = $current->getParent()) {

            if(false === $current->clauseExists($clause))
                continue;

            $localVariables = &$current->getClause($clause)->getLocalVariables();

            foreach($localVariables as $name => &$variables)
                $out[$name] = &$variables;
        }

        return $out;
    }

    /**
     * Add a predicate.
     *
     * @access  public
     * @param   string  $predicate    Predicate.
     * @return  \Hoa\Praspel\Model\Declaration
     */
    public function predicate ( $predicate ) {

        $this->_predicates[] = $predicate;

        return $this;
    }

    /**
     * Get all predicates.
     *
     * @access  public
     * @return  array
     */
    public function getPredicates ( ) {

        return $this->_predicates;
    }
}

}
