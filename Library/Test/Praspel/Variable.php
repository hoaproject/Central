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
 * \Hoa\Test\Praspel\Exception
 */
-> import('Test.Praspel.Exception')

/**
 * \Hoa\Test\Praspel\DomainDisjunction
 */
-> import('Test.Praspel.DomainDisjunction');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel\Variable.
 *
 * Represents a variable.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Variable extends DomainDisjunction {

    /**
     * Parent (here: clause).
     *
     * @var \Hoa\Test\Praspel\Clause object
     */
    protected $_parent  = null;

    /**
     * Variable name.
     *
     * @var \Hoa\Test\Praspel\Variable string
     */
    protected $_name    = null;

    /**
     * Choosen domain.
     *
     * @var \Hoa\Realdom object
     */
    protected $_choosen = null;

    /**
     * Old value.
     *
     * @var \Hoa\Test\Praspel\Variable mixed
     */
    private $_oldValue  = null;

    /**
     * New value.
     *
     * @var \Hoa\Test\Praspel\Variable mixed
     */
    private $_newValue  = null;

    /**
     * Make a conjunction between two variables.
     *
     * @var \Hoa\Test\Praspel\Clause object
     */
    public $_and        = null;



    /**
     * Set the variable name.
     *
     * @access  public
     * @param   \Hoa\Test\Praspel\Clause  $parent    Parent (here: the clause).
     * @param   string                    $name      Variable name.
     * @return  void
     */
    public function __construct ( Clause $parent, $name ) {

        parent::__construct();

        $this->setParent($parent);
        $this->setName($name);
        $this->_and = $this->getParent();

        return;
    }

    /**
     * Select a domain (e.g. from a \Hoa\Test\Selector object).
     *
     * @access  pulic
     * @param   mixed  $selection    From variables to domains, or domain.
     * @return  \Hoa\Realdom
     * @throw   \Hoa\Test\Praspel\Exception
     */
    public function selectDomain ( $selection ) {

        if($selection instanceof \Hoa\Realdom)
            return $this->_choosen = $selection;

        $name = $this->getName();

        if(!isset($selection[$name]))
            throw new Exception(
                'Cannot choose a domain (from a selection) for the variable %s.',
                0, $name);

        return $this->_choosen = $selection[$name];
    }

    /**
     * Declare a dependence.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  \Hoa\Test\Praspel\Variable
     * @throws  \Hoa\Test\Praspel\Exception
     */
    public function hasTheSameDomainAs ( $name ) {

        $context = $this->getParent();

        if($this->getParent() instanceof Clause\Requires) {

            if($name[0] == '\\')
                throw new Exception(
                    'Constructors are not allowed in a @requires clause, given %s.',
                    0, $name);

            $context = $this->getParent();
        }
        elseif($this->getParent() instanceof Clause\Ensures) {

            if($name == '\result')
                throw new Exception(
                    'The operator “domainof” is not commutative. ' .
                    '\result must be in the left position.', 1);

            if(0 !== preg_match('#\\\old\(\s*(\w+)\s*\)#i', $name, $matches)) {

                $context = $this->getParent()->getParent();

                if(false === $context->clauseExists('requires'))
                    throw new Exception(
                        'Foobar %s',
                        2, $name);

                $name    = $matches[1];
                $context = $context->getClause('requires');
            }
        }

        if(false === $context->variableExists($name))
            throw new Exception(
                'Cannot ensure a property on the non-existing variable %s.',
                3, $name);

        $domain = $context->getVariable($name)->getChoosenDomain();

        if(null === $domain)
            return $this;

        if(false === $this->isBelongingTo($domain->getName()))
            $this->_domains[$domain->getName()] = $domain;

        return $this;
    }

    /**
     * Shortcut to predicate all domains.
     *
     * @access  public
     * @param   mixed   $value    Value.
     * @return  bool
     */
    public function predicate ( $value ) {

        $out = false;

        foreach($this->getDomains() as $domain)
            if(true === $out = $domain->predicate($value))
                break;

        return $out;
    }

    /**
     * Set the variable name.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  string
     */
    protected function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the variable name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Whether a domain has been choosen.
     *
     * @access  public
     * @return  bool
     */
    public function hasChoosenDomain ( ) {

        return null !== $this->_choosen;
    }

    /**
     * Get choosen domain.
     *
     * @access  public
     * @return  \Hoa\Realdom
     * @throw   \Hoa\Test\Praspel\Exception
     */
    public function getChoosenDomain ( ) {

        if(false === $this->hasChoosenDomain())
            throw new Exception(
                'No domain has been choosen for the variable %s.',
                2, $this->getName());

        return $this->_choosen;
    }

    /**
     * Set old value.
     *
     * @access  public
     * @return  mixed
     */
    public function setOldValue ( $value ) {

        $old             = $this->_oldValue;
        $this->_oldValue = $value;

        return $old;
    }

    /**
     * Get old value.
     *
     * @access  public
     * @return  mixed
     */
    public function getOldValue ( ) {

        return $this->_oldValue;
    }

    /**
     * Set new value.
     *
     * @access  public
     * @return  mixed
     */
    public function setNewValue ( $value ) {

        $old             = $this->_newValue;
        $this->_newValue = $value;

        return $old;
    }

    /**
     * Get new value.
     *
     * @access  public
     * @return  mixed
     */
    public function getNewValue ( ) {

        return $this->_newValue;
    }

    /**
     * Set the parent (here: the clause).
     *
     * @access  protected
     * @param   \Hoa\Test\Praspel\Clause  $parent    Parent (here: the clause).
     * @return  \Hoa\Test\Praspel\Clause
     */
    protected function setParent ( Clause $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Reset the contract for a new runtime.
     *
     * @access  public
     * @return  void
     */
    public function reset ( ) {

        $this->_choosen  = null;
        $this->_oldValue = null;
        $this->_newValue = null;

        return;
    }

    /**
     * Get the parent (here: the clause).
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\Clause
     */
    public function getParent ( ) {

        return $this->_parent;
    }
}

}
