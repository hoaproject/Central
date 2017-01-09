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

namespace Hoa\Database\Query;

/**
 * Class \Hoa\Database\Query\Select.
 *
 * Build a SELECT query.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Select extends SelectCore implements Dml
{
    /**
     * “Core” selects (whether we have union, unionAll, intersect or except).
     *
     * @var array
     */
    protected $_select  = [];

    /**
     * Ordering terms.
     *
     * @var array
     */
    protected $_orderBy = [];

    /**
     * Limit expressions.
     *
     * @var array
     */
    protected $_limit   = [];

    /**
     * Offset expression.
     *
     * @var string
     */
    protected $_offset  = null;



    /**
     * Start a new SELECT query which is an union of the previous one.
     *
     * @return  \Hoa\Database\Query\Select
     */
    public function union()
    {
        return $this->compose('UNION');
    }

    /**
     * Start a new SELECT query which is an unionAll of the previous one.
     *
     * @return  \Hoa\Database\Query\Select
     */
    public function unionAll()
    {
        return $this->compose('UNION ALL');
    }

    /**
     * Start a new SELECT query which is an intersection of the previous one.
     *
     * @return  \Hoa\Database\Query\Select
     */
    public function intersect()
    {
        return $this->compose('INTERSECT');
    }

    /**
     * Start a new SELECT query which is an exception of the previous one.
     *
     * @return  \Hoa\Database\Query\Select
     */
    public function except()
    {
        return $this->compose('EXCEPT');
    }

    /**
     * Compose SELECT queries.
     *
     * @param   string  $operator    Composition operator.
     * @return  \Hoa\Database\Query\Select
     */
    protected function compose($operator)
    {
        $this->_select[] = parent::__toString() . ' ' . $operator;
        $this->reset();

        return $this;
    }

    /**
     * Add ordering terms.
     *
     * @param   string  $term    Term.
     * @param   ...     ...
     * @return  \Hoa\Database\Query\Select
     */
    public function orderBy($term)
    {
        foreach (func_get_args() as $term) {
            $this->_orderBy[] = $term;
        }

        return $this;
    }

    /**
     * Add limit expressions.
     *
     * @param   string  $expression    Expression.
     * @param   ...     ...
     * @return  \Hoa\Database\Query\Select
     */
    public function limit($expression)
    {
        foreach (func_get_args() as $expression) {
            $this->_limit[] = $expression;
        }

        return $this;
    }

    /**
     * Add offset expression.
     *
     * @param   string  $expression    Expression.
     * @return  \Hoa\Database\Query\Select
     */
    public function offset($expression)
    {
        $this->_offset = $expression;

        return $this;
    }

    /**
     * Generate the query.
     *
     * @return  string
     */
    public function __toString()
    {
        $out    = null;
        $select = implode(' ', $this->_select);

        if (!empty($select)) {
            $out .= $select . ' ';
        }

        $out .= parent::__toString();

        if (!empty($this->_orderBy)) {
            $out .=
                ' ORDER BY ' .
                implode(', ', $this->enclose($this->_orderBy));
        }

        if (!empty($this->_limit)) {
            $out .= ' LIMIT';

            if (null !== $this->_offset) {
                $out .= ' ' . $this->_limit[0] .
                        ' OFFSET ' . $this->_offset;
            } else {
                $out .= ' ' . implode(', ', $this->_limit);
            }
        }

        return $out;
    }
}
