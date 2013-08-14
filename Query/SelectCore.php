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
 * \Hoa\Database\Query\Join
 */
-> import('Database.Query.Join')

/**
 * \Hoa\Database\Query\Where
 */
-> import('Database.Query.Where');

}

namespace Hoa\Database\Query {

/**
 * Class \Hoa\Database\Query\SelectCore.
 *
 * Core of the SELECT query.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class SelectCore extends Where {

    /**
     * Columns.
     *
     * @var \Hoa\Database\Query\SelectCore array
     */
    protected $_columns       = null;

    /**
     * SELECT DISTINCT or SELECT ALL.
     *
     * @var \Hoa\Database\Query\SelectCore string
     */
    protected $_distinctOrAll = null;

    /**
     * Sources.
     *
     * @var \Hoa\Database\Query\SelectCore array
     */
    protected $_from          = array();

    /**
     * Group by expressions.
     *
     * @var \Hoa\Database\Query\SelectCore array
     */
    protected $_groupBy       = array();

    /**
     * Having expression.
     *
     * @var \Hoa\Database\Query\SelectCore string
     */
    protected $_having        = null;



    /**
     * Set columns.
     *
     * @access  public
     * @param   array  $columns    Columns.
     * @return  void
     */
    public function __construct ( Array $columns = array() ) {

        $this->_columns = $columns;

        return;
    }

    /**
     * Make a SELECT DISTINCT.
     *
     * @access  public
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function distinct ( ) {

        $this->_distinctOrAll = 'DISTINCT';

        return $this;
    }

    /**
     * Make a SELECT ALL.
     *
     * @access  public
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function all ( ) {

        $this->_distinctOrAll = 'ALL';

        return $this;
    }

    /**
     * Select a column.
     *
     * @access  public
     * @param   string  $column    Column.
     * @param   ...     ...
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function select ( $column ) {

        foreach(func_get_args() as $column)
            $this->_columns[] = $column;

        return $this;
    }

    /**
     * Group by expression.
     *
     * @access  public
     * @param   string  $expression    Expression.
     * @param   ...     ...
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function groupBy ( $expression ) {

        foreach(func_get_args() as $expression)
            $this->_groupBy[] = $expression;

        return $this;
    }

    /**
     * Having expression.
     *
     * @access  public
     * @param   string  $expression    Expression.
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function having ( $expression ) {

        $this->_having = $expression;

        return $this;
    }

    /**
     * Set source (regular or a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @param   ...    ...
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function from ( $source ) {

        foreach(func_get_args() as $source) {

            if($source instanceof self)
                $source = '(' . $source . ')';

            $this->_from[] = $source;
        }

        return $this;
    }

    /**
     * Alias the last declared source.
     *
     * @access  public
     * @param   string  $alias    Alias.
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function _as ( $alias ) {

        if(empty($this->_from))
            return $this;

        $this->_from[$alias] = '(' . array_pop($this->_from) . ')';

        return $this;
    }

    /**
     * Join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function join ( $source ) {

        return $this->_join('JOIN', $source);
    }

    /**
     * Natural join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function naturalJoin ( $source ) {

        return $this->_join('NATURAL JOIN', $source);
    }

    /**
     * Left join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function leftJoin ( $source ) {

        return $this->_join('LEFT JOIN', $source);
    }

    /**
     * Natural left join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function naturalLeftJoin ( $source ) {

        return $this->_join('NATURAL LEFT JOIN', $source);
    }

    /**
     * Left outer join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function leftOuterJoin ( $source ) {

        return $this->_join('LEFT OUTER JOIN', $source);
    }

    /**
     * Natural left outer join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function naturalLeftOuterJoin ( $source ) {

        return $this->_join('NATURAL LEFT OUTER JOIN', $source);
    }

    /**
     * Inner join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function innerJoin ( $source ) {

        return $this->_join('INNER JOIN', $source);
    }

    /**
     * Natural inner join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function naturalInnerJoin ( $source ) {

        return $this->_join('NATURAL INNER JOIN', $source);
    }

    /**
     * Cross join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function crossJoin ( $source ) {

        return $this->_join('CROSS JOIN', $source);
    }

    /**
     * Natural cross join a source (regular of a SELECT query).
     *
     * @access  public
     * @param   mixed  $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    public function naturalCrossJoin ( $source ) {

        return $this->_join('NATURAL CROSS JOIN', $source);
    }

    /**
     * Make a join.
     *
     * @access  protected
     * @param   string  $type      Type.
     * @param   mixed   $source    Source.
     * @return  \Hoa\Database\Query\Join
     */
    protected function _join ( $type, $source ) {

        if(empty($this->_from))
            return $this;

        if($source instanceof self)
            $source = '(' . $source . ')';

        end($this->_from);
        $key               = key($this->_from);
        $value             = current($this->_from);
        $this->_from[$key] = $value . ' ' . $type . ' ' . $source;

        return new Join($this, $this->_from);
    }

    /**
     * Reset some properties.
     *
     * @access  public
     * @return  \Hoa\Database\Query\SelectCore
     */
    public function reset ( ) {

        parent::reset();
        $this->_columns       = array();
        $this->_distinctOrAll = null;
        $this->_groupBy       = array();
        $this->_having        = array();
        $this->_from          = array();

        return $this;
    }

    /**
     * Generate the query.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = 'SELECT';

        if(null !== $this->_distinctOrAll)
            $out .= ' ' . $this->_distinctOrAll;

        if(!empty($this->_columns))
            $out .= ' ' . implode(', ', $this->_columns);
        else
            $out .= ' *';

        if(!empty($this->_from)) {

            $out .= ' FROM';

            foreach($this->_from as $alias => $from)
                if(is_int($alias))
                    $out .= ' ' . $from;
                else
                    $out .= ' ' . $from . ' AS ' . $alias;
        }

        $out .= parent::__toString();

        if(!empty($this->_groupBy)) {

            $out .= ' GROUP BY ' . implode(', ', $this->_groupBy);

            if(!empty($this->_having))
                $out .= ' HAVING ' . $this->_having;
        }

        return $out;
    }
}

}
