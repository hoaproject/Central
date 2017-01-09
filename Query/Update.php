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
 * Class \Hoa\Database\Query\Update.
 *
 * Build an UPDATE query.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Update extends Where implements Dml
{
    use EncloseIdentifier;

    /**
     * Table.
     *
     * @var string
     */
    protected $_table = null;

    /**
     * Alternative to UPDATE.
     *
     * @var string
     */
    protected $_or    = null;

    /**
     * Pairs to update.
     *
     * @var array
     */
    protected $_set   = [];



    /**
     * Update or rollback.
     *
     * @return  \Hoa\Database\Query\Update
     */
    public function rollback()
    {
        return $this->_or('ROLLBACK');
    }

    /**
     * Update or abort.
     *
     * @return  \Hoa\Database\Query\Update
     */
    public function abort()
    {
        return $this->_or('ABORT');
    }

    /**
     * Update or replace.
     *
     * @return  \Hoa\Database\Query\Update
     */
    public function replace()
    {
        return $this->_or('REPLACE');
    }

    /**
     * Update or fail.
     *
     * @return  \Hoa\Database\Query\Update
     */
    public function fail()
    {
        return $this->_or('FAIL');
    }

    /**
     * Update or ignore.
     *
     * @return  \Hoa\Database\Query\Update
     */
    public function ignore()
    {
        return $this->_or('IGNORE');
    }

    /**
     * Declare an alternative to “INSERT”.
     *
     * @param   string  $or    Alternative.
     * @return  \Hoa\Database\Query\Update
     */
    protected function _or($or)
    {
        $this->_or = $or;

        return $this;
    }

    /**
     * Set the table.
     *
     * @param   string  $table    Table.
     * @return  \Hoa\Database\Query\Update
     */
    public function table($table)
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Set a pair.
     *
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  \Hoa\Database\Query\Update
     */
    public function set($name, $value)
    {
        $this->_set[$name] = $value;

        return $this;
    }

    /**
     * Generate the query.
     *
     * @return  string
     */
    public function __toString()
    {
        $out = 'UPDATE';

        if (null !== $this->_or) {
            $out .= ' OR ' . $this->_or;
        }

        $out .= ' ' . $this->enclose($this->_table);
        $set  = [];

        foreach ($this->_set as $name => $value) {
            $set[] = $this->enclose($name) . ' = ' . $value;
        }

        $out .= ' SET ' . implode(', ', $set);

        return $out . parent::__toString();
    }
}
