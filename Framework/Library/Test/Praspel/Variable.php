<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
