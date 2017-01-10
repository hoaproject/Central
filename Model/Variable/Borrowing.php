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

use Hoa\Math;
use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Model\Variable\Borrowing.
 *
 * Represent a borrowing variable.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Borrowing extends Variable
{
    /**
     * Type: \old(e).
     *
     * @const int
     */
    const TYPE_OLD      = 0;

    /**
     * Type: external (e.g. this->foo->bar).
     *
     * @const int
     */
    const TYPE_EXTERNAL = 1;

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
        parent::__construct($name, $local, $clause);
        $this->determineType();

        return;
    }

    /**
     * Determine type of the variable.
     *
     * @return  void
     * @throws  \Hoa\Praspel\Exception\Model
     */
    protected function determineType()
    {
        $name = $this->getName();

        if ('\old(' === substr($name, 0, 5)) {
            $this->computeOld(substr($name, 5, -1));
        } elseif (false !== strpos($name, '>', 2)) {
            $this->computeDynamicResolution($name);
        } else {
            throw new Praspel\Exception\Model(
                'Variable %s would be a borrowing one, but its type cannot ' .
                'be determined.',
                0,
                $name
            );
        }

        return;
    }

    /**
     * Compute \old(…).
     *
     * @param   string  $name    Name.
     * @return  void
     * @throws  \Hoa\Praspel\Exception\Model
     */
    protected function computeOld($name)
    {
        $clause      = $this->getClause();
        $this->_type = static::TYPE_OLD;
        $parent      = $clause->getParent();

        while (
            false === $parent->clauseExists('requires') &&
            null  !== $parent = $parent->getParent()
        );

        if (null === $parent ||
            false === $parent->clauseExists('requires')) {
            throw new Praspel\Exception\Model('No parent or no requires.', 1);
        }

        $requires         = $parent->getClause('requires');
        $inScopeVariables = $requires->getInScopeVariables();

        if (!isset($inScopeVariables[$name])) {
            throw new Praspel\Exception\Model(
                'Variable %s does not exist, cannot get its old value ' .
                '(in @%s).',
                2,
                [$name, $clause->getName()]
            );
        }

        $this->_variable = &$inScopeVariables[$name];

        return;
    }

    /**
     * Compute dynamic resolution.
     *
     * @param   string  $name    Name.
     * @return  void
     * @throws  \Hoa\Praspel\Exception\Model
     */
    protected function computeDynamicResolution($name)
    {
        $this->_type = static::TYPE_EXTERNAL;

        $clause = $this->getClause();
        $parts  = explode('->', $name);
        $head   = array_shift($parts);

        if ('this' !== $head) {
            throw new Praspel\Exception\Model('Not yet implemented!');
        }

        $registry    = Praspel::getRegistry();
        $root        = $clause->getRoot();
        $bindedClass = $root->getBindedClass();

        if (null === $bindedClass) {
            throw new Praspel\Exception\Model(
                'Cannot resolve the dynamic identifier %s; ' .
                '%s::getBindedClass returned null.',
                3,
                [$name, get_class($root)]
            );
        }

        $attribute = array_shift($parts);
        $id        = $bindedClass . '::$' . $attribute;

        if (!isset($registry[$id])) {
            throw new Praspel\Exception\Model(
                'The contract identifier %s does not exist in the registry.',
                4,
                $name
            );
        }

        $entry = $registry[$id];

        if (false === $entry->clauseExists('invariant')) {
            throw new Praspel\Exception\Model(
                '%s is not declared with an @invariant clause.',
                5,
                $id
            );
        }

        $targetedClause = $entry->getClause('invariant');

        if (!isset($targetedClause[$attribute])) {
            throw new Praspel\Exception\Model(
                'The identifier %s does not exist.',
                6,
                $attribute
            );
        }

        $variable        = $targetedClause[$attribute];
        $this->_variable = $variable;

        return;
    }

    /**
     * Get type.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the borrowed variable.
     *
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function getBorrowedVariable()
    {
        return $this->_variable;
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
        return $this->getBorrowedVariable()->__set($name, $value);
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
        return $this->getBorrowedVariable()->predicate($q);
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
        return $this->getBorrowedVariable()->sample($sampler);
    }

    /**
     * Call the reset() method on realistic domains.
     *
     * @return  void
     */
    public function reset()
    {
        return $this->getBorrowedVariable()->reset();
    }

    /**
     * Define a “key” constraint. Use $variable->key(…)->in = …;
     *
     * @param   mixed  $scalar    Value.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function key($scalar)
    {
        return $this->getBorrowedVariable()->key($scalar);
    }

    /**
     * Define a “contains” constraint.
     *
     * @param   mixed  $scalar    Value.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function contains($scalar)
    {
        return $this->getBorrowedVariable()->contains($scalar);
    }

    /**
     * Add an “is” constraint.
     *
     * @param   string  ...    Keywords.
     * @return  \Hoa\Praspel\Model\Variable
     */
    public function is()
    {
        throw new Praspel\Exception\Model('TODO');
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
        return $this->getBorrowedVariable()->domainof($variable);
    }

    /**
     * Get domains.
     *
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getDomains()
    {
        return $this->getBorrowedVariable()->getDomains();
    }

    /**
     * Get held realdoms.
     *
     * @return  \Hoa\Realdom\Disjunction
     */
    public function &getHeld()
    {
        return $this->getBorrowedVariable()->getHeld();
    }

    /**
     * Check if the variable is local (let) or not.
     *
     * @return  bool
     */
    public function isLocal()
    {
        return $this->getBorrowedVariable()->isLocal();
    }

    /**
     * Get constraints.
     *
     * @return  array
     */
    public function getConstraints()
    {
        return $this->getBorrowedVariable()->getConstraints();
    }
}
