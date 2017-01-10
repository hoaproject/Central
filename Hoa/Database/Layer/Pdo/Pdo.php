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

namespace Hoa\Database\Layer\Pdo;

use Hoa\Consistency;
use Hoa\Database;

/**
 * Class \Hoa\Database\Layer\Pdo.
 *
 * Wrap PDO.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Pdo implements Database\IDal\Wrapper
{
    /**
     * Connection to database.
     *
     * @var \PDO
     */
    protected $_connection = null;



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
    ) {
        if (false === extension_loaded('pdo')) {
            throw new Database\Exception(
                'The module PDO is not enabled.',
                0
            );
        }

        $connection = null;

        try {
            $connection = new \PDO($dsn, $username, $password, $driverOptions);
        } catch (\PDOException $e) {
            throw new Database\Exception(
                $e->getMessage(),
                $e->getCode(),
                null,
                $e
            );
        }

        $this->setConnection($connection);

        return;
    }

    /**
     * Set the connection.
     *
     * @param   \PDO        $connection    The PDO instance.
     * @return  \PDO
     */
    protected function setConnection(\PDO $connection)
    {
        $old               = $this->_connection;
        $this->_connection = $connection;

        return $old;
    }

    /**
     * Get the connection instance.
     *
     * @return  \PDO
     * @throws  \Hoa\Database\Exception
     */
    protected function getConnection()
    {
        if (null === $this->_connection) {
            throw new Database\Exception(
                'Cannot return a null connection.',
                1
            );
        }

        return $this->_connection;
    }

    /**
     * Initiate a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function beginTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function commit()
    {
        return $this->getConnection()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function rollBack()
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Return the ID of the last inserted row or sequence value.
     *
     * @param   string  $name    Name of sequence object (needed for some
     *                           driver).
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function lastInsertId($name = null)
    {
        if (null === $name) {
            return $this->getConnection()->lastInsertId();
        }

        return $this->getConnection()->lastInsertId($name);
    }

    /**
     * Prepare a statement for execution and returns a statement object.
     *
     * @param   string  $statement    This must be a valid SQL statement for the
     *                                target database server.
     * @param   array   $options      Options to set attributes values for the
     *                                Layer Statement.
     * @return  \Hoa\Database\Layer\Pdo\Statement
     * @throws  \Hoa\Database\Exception
     */
    public function prepare($statement, array $options = [])
    {
        if (!isset($options[\PDO::ATTR_CURSOR])) {
            try {
                $this->getConnection()->getAttribute(\PDO::ATTR_CURSOR);
                $options[\PDO::ATTR_CURSOR] = \PDO::CURSOR_SCROLL;
            } catch (\PDOException $e) {
                // Cursors are not supported by the driver, see
                // https://github.com/hoaproject/Database/issues/35.
            }
        }

        $handle = $this->getConnection()->prepare($statement, $options);

        if (!($handle instanceof \PDOStatement)) {
            throw new Database\Exception(
                '%3$s (%1$s/%2$d).',
                2,
                $this->errorInfo()
            );
        }

        return new Statement($handle);
    }

    /**
     * Quote a sting for use in a query.
     *
     * @param   string  $string    The string to be quoted.
     * @param   int     $type      Provide a data type hint for drivers that
     *                             have alternate quoting styles.
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function quote($string = null, $type = -1)
    {
        if ($type < 0) {
            return $this->getConnection()->quote($string);
        }

        return $this->getConnection()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * \Hoa\Database\Layer\Pdo\Statement object.
     *
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  \Hoa\Database\Layer\Pdo\Statement
     * @throws  \Hoa\Database\Exception
     */
    public function query($statement)
    {
        $handle = $this->getConnection()->query($statement);

        if (!($handle instanceof \PDOStatement)) {
            throw new Database\Exception(
                '%3$s (%1$s/%2$d).',
                3,
                $this->errorInfo()
            );
        }

        return new Statement($handle);
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database
     * handle.
     *
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function errorCode()
    {
        return $this->getConnection()->errorCode();
    }

    /**
     * Fetch extends error information associated with the last operation on the
     * database handle.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function errorInfo()
    {
        return $this->getConnection()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function getAvailableDrivers()
    {
        return $this->getConnection()->getAvailableDrivers();
    }

    /**
     * Set attributes.
     *
     * @param   array   $attributes    Attributes values.
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function setAttributes(array $attributes)
    {
        $out = true;

        foreach ($attributes as $attribute => $value) {
            $out &= $this->setAttribute($attribute, $value);
        }

        return (bool) $out;
    }

    /**
     * Set a specific attribute.
     *
     * @param   mixed   $attribute    Attribute name.
     * @param   mixed   $value        Attribute value.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function setAttribute($attribute, $value)
    {
        return $this->getConnection()->setAttribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function getAttributes()
    {
        $out        = [];
        $attributes = [
             0 => 'AUTOCOMMIT',
             1 => 'CASE',
             2 => 'CLIENT_VERSION',
             3 => 'CONNECTION_STATUS',
             4 => 'DRIVER_NAME',
             5 => 'ERRMODE',
             6 => 'ORACLE_NULLS',
             7 => 'PERSISTENT',
             8 => 'PREFETCH',
             9 => 'SERVER_INFO',
            10 => 'SERVER_VERSION',
            11 => 'TIMEOUT'
        ];

        foreach ($attributes as $attribute) {
            $out[$attribute] = $this->getAttribute($attribute);
        }

        return $out;
    }

    /**
     * Retrieve a database connection attribute.
     *
     * @param   string  $attribute    Attribute name.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function getAttribute($attribute)
    {
        return
            $this
                ->getConnection()
                ->getAttribute(constant('\PDO::ATTR_' . $attribute));
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Database\Layer\Pdo\Pdo');
