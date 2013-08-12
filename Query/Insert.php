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
 * \Hoa\Database\Query\Select
 */
-> import('Database.Query.Select');

}

namespace Hoa\Database\Query {

/**
 * Class \Hoa\Database\Query\Insert.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Insert {

    protected $_into          = null;
    protected $_or            = null;
    protected $_columns       = array();
    protected $_values        = array();
    protected $_defaultValues = false;



    public function into ( $name ) {

        $this->_into = $name;

        return $this;
    }

    public function rollback ( ) {

        return $this->_or('ROLLBACK');
    }

    public function abort ( ) {

        return $this->_or('ABORT');
    }

    public function replace ( ) {

        return $this->_or('REPLACE');
    }

    public function fail ( ) {

        return $this->_or('FAIL');
    }

    public function ignore ( ) {

        return $this->_or('IGNORE');
    }

    protected function _or ( $or ) {

        $this->_or = $or;

        return $this;
    }

    public function on ( $column ) {

        foreach(func_get_args() as $column)
            $this->_columns[] = $column;

        return $this;
    }

    public function values ( $expression ) {

        if($expression instanceof \Closure) {

            $subStatement  = new Select();
            $expression($subStatement);
            $this->_values = (string) $subStatement;
        }
        else {

            if($this->_values instanceof \Closure)
                $this->_values = array();

            $values = &$this->_values[];
            $values = array();

            foreach(func_get_args() as $expression)
                $values[] = $expression;
        }

        return $this;
    }

    public function defaultValues ( ) {

        $this->_defaultValues = true;

        return $this;
    }

    public function __get ( $name ) {

        switch(strtolower($name)) {

            case 'or':
                return $this;
              break;

            default:
                return $this->$name;
        }
    }

    public function __toString ( ) {

        $out = 'INSERT';

        if(null !== $this->_or)
            $out .= ' OR ' . $this->_or;

        $out .= ' INTO ' . $this->_into;

        if(true === $this->_defaultValues)
            return $out . ' DEFAULT VALUES';

        if(!empty($this->_columns))
            $out .= ' (' . implode(', ', $this->_columns) . ')';

        if(is_string($this->_values))
            return $out . ' ' . $this->_values;

        $tuples = array();

        foreach($this->_values as $tuple)
            $tuples[] = '(' . implode(', ', $tuple) . ')';

        return $out . ' VALUES ' . implode(', ', $tuples);
    }
}

}
