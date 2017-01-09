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

namespace Hoa\Database\IDal;

use Hoa\Database;
use Hoa\Iterator;

/**
 * Interface \Hoa\Database\IDal\WrapperStatement.
 *
 * Interface of a DAL statement wrapper.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
interface WrapperStatement extends Iterator\Aggregate
{
    /**
     * Execute a prepared statement.
     *
     * @param   array   $bindParameters    Bind parameters values if
     *                                     bindParameter() is not called.
     * @return  \Hoa\Database\IDal\WrapperStatement
     * @throws  \Hoa\Database\Exception
     */
    public function execute(array $bindParameters = []);

    /**
     * Bind a parameter to te specified variable name.
     *
     * @param   mixed   $parameter    Parameter name.
     * @param   mixed   $value        Parameter value.
     * @param   int     $type         Type of value.
     * @param   int     $length       Length of data type.
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function bindParameter(
        $parameter,
        &$value,
        $type   = null,
        $length = null
    );

    /**
     * Return an array containing all of the result set rows.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function fetchAll();

    /**
     * Set the Iterator fetching style.
     *
     * @param   int    $offset         This value must be one of the
     *                                 DalStatement::FROM_* constants or an
     *                                 arbitrary offset.
     * @param   int    $orientation    This value must be DalStatement::FORWARD
     *                                 or DalStatement::BACKWARD constant.
     * @param   int    $style          This value must be one of the
     *                                 DalStatement::AS_* constants.
     * @param   mixed  $arg1           For AS_CLASS: The Class name.
     *                                 For AS_REUSABLE_OBJECT: An object.
     * @param   array  $arg2           For AS_CLASS: Constructor arguments.
     * @return  \Hoa\Database\IDal\WrapperStatement
     */
    public function setFetchingStyle(
        $offset      = Database\DalStatement::FROM_START,
        $orientation = Database\DalStatement::FORWARD,
        $style       = Database\DalStatement::AS_MAP,
        $arg1        = null,
        $arg2        = null
    );

    /**
     * Fetch the first row in the result set.
     *
     * @param   int  $style    Must be one of the DalStatement::AS_* constants.
     * @return  mixed
     */
    public function fetchFirst($style = null);

    /**
     * Fetch the last row in the result set.
     *
     * @param   int  $style    Must be one of the DalStatement::AS_* constants.
     * @return  mixed
     */
    public function fetchLast($style = null);

    /**
     * Return a single column from the next row of the result set or false if
     * there is no more row.
     *
     * @param   int  $column    Column index.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function fetchColumn($column = 0);

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function closeCursor();

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function errorCode();

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function errorInfo();
}
