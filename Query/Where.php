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
 * Class \Hoa\Database\Query\Where.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Where {

    protected $_where         = array();
    protected $_logicOperator = null;



    public function where ( $expression ) {

        $where = null;

        if(!empty($this->_where))
            $where = ($this->_logicOperator ?: 'AND') . ' ';

        if($expression instanceof \Closure) {

            $subWhere   = new self();
            $expression($subWhere);
            //                    skip “ WHERE”
            $expression = '(' . substr($subWhere, 7) . ')';
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

    public function reset ( ) {

        $this->_where = array();

        return $this;
    }

    public function __toString ( ) {

        if(empty($this->_where))
            return null;

        return ' WHERE ' . implode(' ', $this->_where);
    }
}

}
