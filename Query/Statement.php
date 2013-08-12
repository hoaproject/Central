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

namespace Hoa\Database\Query {

/**
 * Class \Hoa\Database\Query\Statement.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Statement {

    protected $_from          = array();
    protected $_where         = array();
    protected $_logicOperator = null;



    public function from ( $source ) {

        foreach(func_get_args() as $source) {

            if($source instanceof self)
                $source = '(' . $source . ')';

            $this->_from[] = $source;
        }

        return $this;
    }

    public function _as ( $alias ) {

        if(empty($this->_from))
            return $this;

        $this->_from[$alias] = '(' . array_pop($this->_from) . ')';

        return $this;
    }

    public function join ( $source ) {

        return $this->_join('JOIN', $source);
    }

    public function naturalJoin ( $source ) {

        return $this->_join('NATURAL JOIN', $source);
    }

    public function leftJoin ( $source ) {

        return $this->_join('LEFT JOIN', $source);
    }

    public function naturalLeftJoin ( $source ) {

        return $this->_join('NATURAL LEFT JOIN', $source);
    }

    public function leftOuterJoin ( $source ) {

        return $this->_join('LEFT OUTER JOIN', $source);
    }

    public function naturalLeftOuterJoin ( $source ) {

        return $this->_join('NATURAL LEFT OUTER JOIN', $source);
    }

    public function innerJoin ( $source ) {

        return $this->_join('INNER JOIN', $source);
    }

    public function naturalInnerJoin ( $source ) {

        return $this->_join('NATURAL INNER JOIN', $source);
    }

    public function crossJoin ( $source ) {

        return $this->_join('CROSS JOIN', $source);
    }

    public function naturalCrossJoin ( $source ) {

        return $this->_join('NATURAL CROSS JOIN', $source);
    }

    protected function _join ( $type, $source ) {

        if(empty($this->_from))
            return $this;

        if($source instanceof self)
            $source = '(' . $source . ')';

        end($this->_from);
        $key               = key($this->_from);
        $value             = current($this->_from);
        $this->_from[$key] = $value . ' ' . $type . ' ' . $source;

        return new _Join($this, $this->_from);
    }

    public function where ( $expression ) {

        $where = null;

        if(!empty($this->_where))
            $where = ($this->_logicOperator ?: 'AND') . ' ';

        if($expression instanceof \Closure) {

            $subStatement = new self();
            $expression($subStatement);
            $subStatement->partialReset();
            //                    skip WHERE
            $expression   = '(' . substr($subStatement, 6) . ')';
        }

        $this->_where[]       = $where . $expression;
        $this->_logicOperator = null;

        return $this;
    }

    public function __call ( $name, Array $values ) {

        return call_user_func_array(array($this, '_' . $name), $values);
    }

    public function __get ( $name ) {

        switch(strtolower($name)) {

            case 'and':
            case 'or':
                $this->_logicOperator = strtoupper($name);
              break;

            default:
                return $this->$name;
        }

        return $this;
    }

    public function partialReset ( ) {

        $this->_from = array();

        return $this;
    }

    public function reset ( ) {

        $this->_from  = array();
        $this->_where = array();

        return $this;
    }

    public function __toString ( ) {

        $out = null;

        if(!empty($this->_from)) {

            $out = 'FROM';

            foreach($this->_from as $alias => $from)
                if(is_int($alias))
                    $out .= ' ' . $from;
                else
                    $out .= ' ' . $from . ' AS ' . $alias;
        }

        if(empty($this->_where))
            return $out;

        if(null !== $out)
            $out .= ' ';

        $out .= 'WHERE ' . implode(' ', $this->_where);

        return $out;
    }
}

class _Join {

    protected $_parent = null;
    protected $_from   = null;



    public function __construct ( Statement $parent, Array &$from ) {

        $this->_parent = $parent;
        $this->_from   = &$from;
        end($this->_from);

        return;
    }

    public function on ( $expression ) {

        $this->_from[key($this->_from)] = current($this->_from) .
                                          ' ON ' . $expression;

        return $this->_parent;
    }

    public function using ( $column ) {

        $this->_from[key($this->_from)] = current($this->_from) .
                                          ' USING (' .
                                          implode(', ', func_get_args()) . ')';

        return $this->_parent;
    }
}

}
