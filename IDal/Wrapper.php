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

/**
 * Interface \Hoa\Database\IDal\Wrapper.
 *
 * Interface of a DAL wrapper.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
interface Wrapper
{
    /**
     * Create a DAL instance, representing a connection to a database.
     *
     * @param   string  $dsn              The DSN of database.
     * @param   string  $username         The username to connect to database.
     * @param   string  $password         The password to connect to database.
     * @param   array   $driverOptions    The driver options.
     * @throws  \Hoa\Database\Exception
     */
    public function __construct(
        $dsn,
        $username,
        $password,
        array $driverOptions = []
    );

    /**
     * Initiate a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function beginTransaction();

    /**
     * Commit a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function commit();

    /**
     * Roll back a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function rollBack();

    /**
     * Return the ID of the last inserted row or sequence value.
     *
     * @param   string  $name    Name of sequence object (needed for some
     *                           driver).
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function lastInsertId($name = null);

    /**
     * Prepare a statement for execution and returns a statement object.
     *
     * @param   string  $statement    This must be a valid SQL statement for the
     *                                target database server.
     * @param   array   $options      Options to set attributes values for the
     *                                AbstractLayer Statement.
     * @return  \Hoa\Database\IDal\WrapperStatement
     * @throws  \Hoa\Database\Exception
     */
    public function prepare($statement, array $options = []);

    /**
     * Quote a string for use in a query.
     *
     * @param   string  $string    The string to be quoted.
     * @param   int     $type      Provide a data type hint for drivers that
     *                             have alternate quoting styles.
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function quote($string = null, $type = -1);

    /**
     * Execute an SQL statement, returning a result set as a
     * \Hoa\Database\IDal\WrapperStatement object.
     *
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  \Hoa\Database\IDal\WrapperStatement
     * @throws  \Hoa\Database\Exception
     */
    public function query($statement);

    /**
     * Fetch the SQLSTATE associated with the last operation on the database
     * handle.
     *
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function errorCode();

    /**
     * Fetch extends error information associated with the last operation on the
     * database handle.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function errorInfo();

    /**
     * Return an array of available drivers.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function getAvailableDrivers();

    /**
     * Set attributes.
     *
     * @param   array   $attributes    Attributes values.
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function setAttributes(array $attributes);

    /**
     * Set a specific attribute.
     *
     * @param   mixed   $attribute    Attribute name.
     * @param   mixed   $value        Attribute value.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function setAttribute($attribute, $value);

    /**
     * Retrieve all database connection attributes.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function getAttributes();

    /**
     * Retrieve a database connection attribute.
     *
     * @param   string  $attribute    Attribute name.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function getAttribute($attribute);
}
