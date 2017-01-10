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

namespace Hoa\Praspel\Model\Variable;

use Hoa\Consistency;
use Hoa\Math;
use Hoa\Praspel;
use Hoa\Realdom;
use Hoa\Visitor;

/**
 * Class \Hoa\Praspel\Model\Variable.
 *
 * Represent a variable.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          Variable
    implements Visitor\Element,
               Realdom\IRealdom\Holder
{
    /**
     * Variable name.
     *
     * @var string
     */
    protected $_name                = null;

    /**
     * Local (let) or not.
     *
     * @var bool
     */
    protected $_local               = false;

    /**
     * Clause that contains this variable.
     *
     * @var \Hoa\Praspel\Model\Clause
     */
    protected $_clause              = null;

    /**
     * Variable value.
     *
     * @var mixed
     */
    protected $_value               = null;

    /**
     * Domains that describe the variable.
     *
     * @var \Hoa\Realdom\Disjunction
     */
    protected $_domains             = null;

    /**
     * References domains.
     *
     * @var \Hoa\Realdom\Disjunction
     */
    protected $_refDomains          = null;

    /**
     * Alias variable (please, see “domainof”).
     *
     * @var \Hoa\Praspel\Model\Variable
     */
    protected $_alias               = null;

    /**
     * Constraints.
     *
     * @var array
     */
    protected $_constraints         = [];

    /**
     * Temporary constraints type.
     * Useful when propagate new constraints.
     *
     * @var string
     */
    protected $_tmpConstraintsType  = null;

    /**
     * Temporary constraints index.
     * Useful when propagate new constraints.
     *
     * @var string
     */
    protected $_tmpConstraintsIndex = null;



    /**
     * Build a variable.
     *
     * @param   string                     $name      Name.
     * @param   bool                       $local     Local.
     * @param   \Hoa\Praspel\Model\Clause  $clause    Clause.
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function __construct(
        $name,
        $local,
        Praspel\Model\Clause $clause = null
    ) {
        if (('\old'    === substr($name, 0, 4) ||
             '\result' === $name) &&
             !($clause instanceof Praspel\Model\Ensures)) {
            throw new Praspel\Exception\Model(
                '\old(…) and \result are only allowed in @ensures, ' .
                'given %s in @%s.',
                0,
                [$name, $clause->getName()]
            );
        }

        $this->_name       = $name;
        $this->_local      = $local;
        $this->_clause     = $clause;
        $this->_refDomains = &$this->_domains;

        return;
    }

    /**
     * Set a value to the variable.
     *
     * @param   mixed  $value    Value.
     * @return  mixed
     */
    public function setValue($value)
    {
        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get value of the variable.
     *
     * @return  mixed
     */
    public function &getValue()
    {
        return $this->_value;
    }

    /**
     * Allow to write $variable->in = … to define domains (if $name is not equal
     * to "in", then it is a normal behavior).
     *
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  void
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function __set($name, $value)
    {
        if ('in' !== $name) {
            $this->$name = $value;

            return;
        }

        $onDomains = $this->_domains === $this->_refDomains;

        if (true === $onDomains &&
            !empty($this->_domains)) {
            throw new Praspel\Exception\Model(
                'Variable $%s has already declared domains.',
                1,
                $this->getName()
            );
        }

        if (!($value instanceof Realdom\Disjunction)) {
            $value = realdom()->const($value);
        }

        $this->_refDomains = $value;

        if (false === $onDomains) {
            $this->_domains->propagateConstraints(
                $this->_tmpConstraintsType,
                $this->_tmpConstraintsIndex
            );

            $this->_tmpConstraintsType  = null;
            $this->_tmpConstraintsIndex = null;
        }

        unset($this->_refDomains);
        $this->_refDomains = &$this->_domains;

        $this->_domains->setHolder($this);

        foreach ($this->_domains as $domain) {
            $domain->setConstraints($this->_constraints);
        }

        return;
    }

    /*
     * Call the predicate() method on realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate($q = null)
    {
        if (null === $q) {
            $q = $this->getValue();
        }

        return $this->getDomains()->predicate($q);
    }

    /**
     * Call the sample() method on realistic domains.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     * @throws  \Hoa\Realdom\Exception
     */
    public function sample(Math\Sampler $sampler = null)
    {
        return $this->getDomains()->sample($sampler);
    }

    /**
     * Call the reset() method on realistic domains.
     *
     * @return  void
     */
    public function reset()
    {
        return $this->getDomains()->reset();
    }

    /**
     * Define a “key” constraint. Use $variable->key(…)->in = …;
     *
     * @param   mixed  $scalar    Value.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function key($scalar)
    {
        if (!isset($this->_constraints['key'])) {
            $this->_constraints['key'] = [];
        }

        unset($this->_refDomains);
        $handle            = &$this->_constraints['key'][];
        $handle[0]         = realdom()->const($scalar);
        $this->_refDomains = &$handle[1];

        end($this->_constraints['key']);
        $this->_tmpConstraintsType  = 'key';
        $this->_tmpConstraintsIndex = key($this->_constraints['key']);

        return $this;
    }

    /**
     * Define a “contains” constraint.
     *
     * @param   mixed  $scalar    Value.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function contains($scalar)
    {
        if (!isset($this->_constraints['contains'])) {
            $this->_constraints['contains'] = [];
        }

        $this->_constraints['contains'][] = realdom()->const($scalar);

        return $this;
    }

    /**
     * Add an “is” constraint.
     *
     * @param   string  ...    Keywords.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function is()
    {
        if (!isset($this->_constraints['is'])) {
            $this->_constraints['is'] = [];
        }

        $this->_constraints['is'] = array_merge(
            $this->_constraints['is'],
            func_get_args()
        );

        return $this;
    }

    /**
     * Declare a “domainof” (alias).
     *
     * @param   \Hoa\Praspel\Model\Variable  $variable    Variable.
     * @return  \Hoa\Praspel\Model\Variable
     * @throws  \Hoa\Realdom\Exception
     */
    public function domainof($variable)
    {
        $variables = $this->getClause()->getLocalVariables();

        if (!isset($variables[$variable])) {
            throw new Praspel\Exception\Model(
                'Variable $%s does not exist, cannot alias domains to $%s.',
                2,
                [$variable, $this->getName()]
            );
        }

        if (!empty($this->_domains)) {
            throw new Praspel\Exception\Model(
                'Variable $%s already has domains, cannot alias new domains ' .
                'from $%s.',
                3,
                [$this->getName(), $variable]
            );
        }

        $this->_alias   = $variable;
        $this->_domains = &$variables[$variable]->getDomains();

        return $this;
    }

    /**
     * Get domains.
     *
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getDomains()
    {
        return $this->_domains;
    }

    /**
     * Get held realdoms.
     *
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getHeld()
    {
        return $this->getDomains();
    }

    /**
     * Get variable name.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Check if the variable is local (let) or not.
     *
     * @return  bool
     */
    public function isLocal()
    {
        return $this->_local;
    }

    /**
     * Get constraints.
     *
     * @return  array
     */
    public function getConstraints()
    {
        return $this->_constraints;
    }

    /**
     * Get alias.
     *
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * Get parent clause.
     *
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function getClause()
    {
        return $this->_clause;
    }

    /**
     * Accept a visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept(
        Visitor\Visit $visitor,
        &$handle = null,
        $eldnah  = null
    ) {
        return $visitor->visit($this, $handle, $eldnah);
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Praspel\Model\Variable\Variable');
