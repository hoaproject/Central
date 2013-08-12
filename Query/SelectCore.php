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
 * \Hoa\Database\Query\Statement
 */
-> import('Database.Query.Statement');

}

namespace Hoa\Database\Query {

/**
 * Class \Hoa\Database\Query\SelectCore.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class SelectCore extends Statement {

    protected $_columns       = null;
    protected $_distinctOrAll = null;
    protected $_groupBy       = array();
    protected $_having        = null;


    public function __construct ( Array $columns = array() ) {

        $this->_columns = $columns;

        return;
    }

    public function distinct ( ) {

        $this->_distinctOrAll = 'DISTINCT';

        return $this;
    }

    public function all ( ) {

        $this->_distinctOrAll = 'ALL';

        return $this;
    }

    public function select ( $column ) {

        foreach(func_get_args() as $column)
            $this->_columns[] = $column;

        return $this;
    }

    public function groupBy ( $expression ) {

        $this->_groupBy[] = $expression;

        return $this;
    }

    public function having ( $expression ) {

        $this->_having = $expression;

        return $this;
    }

    public function reset ( ) {

        parent::reset();
        $this->_columns = array();
        $this->_distinctOrAll = null;
        $this->_groupBy       = array();
        $this->_having        = array();

        return $this;
    }

    public function __toString ( ) {

        $out = 'SELECT';

        if(null !== $this->_distinctOrAll)
            $out .= ' ' . $this->_distinctOrAll;

        if(!empty($this->_columns))
            $out .= ' ' . implode(', ', $this->_columns);
        else
            $out .= ' *';

        $out .= ' ' . parent::__toString();

        if(!empty($this->_groupBy)) {

            $out .= ' GROUP BY ' . implode(', ', $this->_groupBy);

            if(!empty($this->_having))
                $out .= ' HAVING ' . $this->_having;
        }

        return $out;
    }
}

}
