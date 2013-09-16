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
 * \Hoa\Praspel\Exception\Model
 */
-> import('Praspel.Exception.Model')

/**
 * \Hoa\Praspel\Model\Variable
 */
-> import('Praspel.Model.Variable.~');

}

namespace Hoa\Praspel\Model\Variable {

/**
 * Class \Hoa\Praspel\Model\Variable\Borrowing.
 *
 * Represent a borrowing variable.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Borrowing extends Variable {

    /**
     * Type: \old(e).
     *
     * @const int
     */
    const TYPE_OLD     = 1;

    /**
     * Type.
     *
     * @var \Hoa\Praspel\Model\Variable\Borrowing
     */
    protected $_type     = null;

    /**
     * Borrowed variable.
     *
     * @var \Hoa\Praspel\Model\Variable
     */
    protected $_variable = null;



    /**
     * Build a variable.
     *
     * @access  public
     * @param   string                     $name      Name.
     * @param   bool                       $local     Local.
     * @param   \Hoa\Praspel\Model\Clause  $clause    Clause.
     * @return  void
     * @throw   \Hoa\Praspel\Exception\Model
     */
    public function __construct ( $name, $local,
                                  \Hoa\Praspel\Model\Clause $clause = null ) {

        parent::__construct($name, $local, $clause);
        $this->determineType();

        return;
    }

    /**
     * Determine type of the variable.
     *
     * @access  protected
     * @return  void
     * @throw   \Hoa\Praspel\Exception\Model
     */
    protected function determineType ( ) {

        $name   = $this->getName();
        $clause = $this->getClause();

        if('\old(' === substr($name, 0, 5)) {

            $this->_type = static::TYPE_OLD;
            $old         = substr($name, 5, -1);
            $parent      = $clause->getParent();

            while(   false === $parent->clauseExists('requires')
                  && null  !== $parent = $parent->getParent());

            if(   null === $parent
               || false === $parent->clauseExists('requires'))
                throw new \Hoa\Praspel\Exception\Model(
                    'No parent or no requires.', 0);

            $requires         = $parent->getClause('requires');
            $inScopeVariables = $requires->getInScopeVariables();

            if(!isset($inScopeVariables[$old]))
                throw new \Hoa\Praspel\Exception\Model(
                    'Variable %s does not exist, cannot get its old value ' .
                    '(in @%s).',
                    1, array($old, $clause->getName()));

            $this->_variable = &$inScopeVariables[$old];
        }

        return;
    }

    /**
     * Get type.
     *
     * @access  public
     * @return  int
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Get the borrowed variable.
     *
     * @access  public
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function getBorrowedVariable ( ) {

        return $this->_variable;
    }

    /**
     * Allow to write $variable->in = … to define domains (if $name is not equal
     * to "in", then it is a normal behavior).
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  void
     * @throw   \Hoa\Praspel\Exception\Model
     */
    public function __set ( $name, $value ) {

        return $this->getBorrowedVariable()->__set($name, $value);
    }

    /*
     * Call the predicate() method on realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q = null ) {

        return $this->getBorrowedVariable()->predicate($q);
    }

    /**
     * Call the sample() method on realistic domains.
     *
     * @access  public
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     * @throw   \Hoa\Realdom\Exception
     */
    public function sample ( \Hoa\Math\Sampler $sampler = null ) {

        return $this->getBorrowedVariable()->sample($sampler);
    }

    /**
     * Call the reset() method on realistic domains.
     *
     * @access  public
     * @return  void
     */
    public function reset ( ) {

        return $this->getBorrowedVariable()->reset();
    }

    /**
     * Define a “key” constraint. Use $variable->key(…)->in = …;
     *
     * @access  public
     * @param   mixed  $scalar    Value.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function key ( $scalar ) {

        return $this->getBorrowedVariable()->key($scalar);
    }

    /**
     * Define a “contains” constraint.
     *
     * @access  public
     * @param   mixed  $scalar    Value.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function contains ( $scalar ) {

        return $this->getBorrowedVariable()->contains($scalar);
    }

    /**
     * Add an “is” constraint.
     *
     * @access  public
     * @param   string  ...    Keywords.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function is ( ) {

        throw new \Hoa\Praspel\Exception\Model('TODO');
    }

    /**
     * Declare a “domainof” (alias).
     *
     * @access  public
     * @param   \Hoa\Praspel\Model\Variable  $variable    Variable.
     * @return  \Hoa\Praspel\Model\Variable
     * @throw   \Hoa\Realdom\Exception
     */
    public function domainof ( $variable ) {

        return $this->getBorrowedVariable()->domainof($variable);
    }

    /**
     * Get domains.
     *
     * @access  public
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getDomains ( ) {

        return $this->getBorrowedVariable()->getDomains();
    }

    /**
     * Get held realdoms.
     *
     * @access  public
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getHeld ( ) {

        return $this->getBorrowedVariable()->getHeld();
    }

    /**
     * Check if the variable is local (let) or not.
     *
     * @access  public
     * @return  bool
     */
    public function isLocal ( ) {

        return $this->getBorrowedVariable()->isLocal();
    }

    /**
     * Get constraints.
     *
     * @access  public
     * @return  array
     */
    public function getConstraints ( ) {

        return $this->getBorrowedVariable()->getConstraints();
    }
}

}
