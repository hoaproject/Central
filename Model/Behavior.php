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

namespace Hoa\Praspel\Model;

use Hoa\Praspel;

/**
 * Class \Hoa\Praspel\Model\Behavior.
 *
 * Represent the @behavior clause.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Behavior extends Clause
{
    /**
     * Name.
     *
     * @const string
     */
    const NAME = 'behavior';

    /**
     * Allowed clauses.
     *
     * @var array
     */
    protected static $_allowedClauses = [
        'requires',
        'behavior',
        'default',
        'ensures',
        'throwable',
        'description'
    ];

    /**
     * Clauses.
     *
     * @var array
     */
    protected $_clauses               = [];

    /**
     * Identifier (@behavior <identifier> { … }).
     *
     * @var string
     */
    protected $_identifier            = null;



    /**
     * Get or create a specific clause.
     *
     * @param   string  $clause    Clause (without leading arobase).
     * @return  \Hoa\Praspel\Model\Clause
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function getClause($clause)
    {
        if (isset($this->_clauses[$clause])) {
            return $this->_clauses[$clause];
        }

        $handle = null;

        if (false === in_array($clause, static::getAllowedClauses())) {
            throw new Praspel\Exception\Model(
                'Clause @%s is not allowed in @%s.',
                0,
                [$clause, $this->getId()]
            );
        }

        switch ($clause) {
            case 'is':
                $handle = new Is($this);

                break;

            case 'requires':
                $handle = new Requires($this);

                break;

            case 'ensures':
                if (true === $this->clauseExists('behavior')) {
                    throw new Praspel\Exception\Model(
                        'Cannot add the @ensures clause, since a @behavior ' .
                        'clause exists at the same level.',
                        1
                    );
                }

                $handle = new Ensures($this);

                break;

            case 'throwable':
                if (true === $this->clauseExists('behavior')) {
                    throw new Praspel\Exception\Model(
                        'Cannot add the @throwable clause, since a @behavior ' .
                        'clause exists at the same level.',
                        2
                    );
                }

                $handle = new Throwable($this);

                break;

            case 'invariant':
                $handle = new Invariant($this);

                break;

            case 'behavior':
                if (true === $this->clauseExists('ensures') ||
                    true === $this->clauseExists('throwable')) {
                    throw new Praspel\Exception\Model(
                        'Cannot add the @behavior clause, since an @ensures ' .
                        'or a @throwable clause exists at the same level.',
                        3
                    );
                }

                $handle = new Collection(
                    new self($this),
                    function (Behavior $clause, $identifier) {
                        $clause->setIdentifier($identifier);

                        return;
                    }
                );

                break;

            case 'default':
                if (true === $this->clauseExists('ensures') ||
                    true === $this->clauseExists('throwable')) {
                    throw new Praspel\Exception\Model(
                        'Cannot add the @default clause, since an @ensures ' .
                        'or a @throwable clause exists at the same level.',
                        4
                    );
                }

                if (false === $this->clauseExists('behavior')) {
                    throw new Praspel\Exception\Model(
                        'Cannot add a @default clause if at least one ' .
                        '@behavior clause has not been declared.',
                        5
                    );
                }

                $handle = new DefaultBehavior($this);

                break;

            case 'description':
                $handle = new Description($this);

                break;

            default:
                throw new Praspel\Exception\Model(
                    'Clause @%s is unknown.',
                    6,
                    [$clause, $this->getName()]
                );
        }

        return $this->_clauses[$clause] = $handle;
    }

    /**
     * Add a clause.
     *
     * @param   \Hoa\Praspel\Model\Clause  $clause    Clause.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function addClause(Clause $clause)
    {
        $name = $clause->getName();

        if (false === in_array($name, static::getAllowedClauses())) {
            throw new Praspel\Exception\Model(
                'Clause @%s is not allowed in @%s.',
                7,
                [$name, $this->getId()]
            );
        }

        $clause->setParent($this);

        return $this->_clauses[$name] = $clause;
    }

    /**
     * Check if a clause already exists, i.e. has been declared.
     *
     * @param   string  $clause    Clause (without leading arobase).
     * @return  bool
     */
    public function clauseExists($clause)
    {
        return isset($this->_clauses[$clause]);
    }

    /**
     * Get allowed clauses.
     *
     * @return  array
     */
    public static function getAllowedClauses()
    {
        return static::$_allowedClauses;
    }

    /**
     * Set identifier.
     *
     * @return  string
     */
    public function setIdentifier($identifier)
    {
        $old               = $this->_identifier;
        $this->_identifier = $identifier;

        return $old;
    }

    /**
     * Get identifier.
     *
     * @return  string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Get identifier (fallback).
     *
     * @return  string
     */
    protected function _getId()
    {
        return $this->getName() . '_' . $this->getIdentifier();
    }
}
