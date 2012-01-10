<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Database\Exception
 */
-> import('Database.Exception')

/**
 * \Hoa\Database\AbstractLayer\Pdo\PdoStatement
 */
-> import('Database.AbstractLayer.Pdo.PdoStatement')

/**
 * \Hoa\Database\IDal\Wrapper
 */
-> import('Database.IDal.Wrapper');

}

namespace Hoa\Database\AbstractLayer\Pdo {

/**
 * Class \Hoa\Database\AbstractLayer\Pdo.
 *
 * Wrap PDO.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Pdo implements \Hoa\Database\IDal\Wrapper {

    /**
     * Connection to database.
     *
     * @var \PDO object
     */
    protected $_connection = null;



    /**
     * Create a DAL instance, representing a connection to a database.
     *
     * @access  public
     * @param   string  $dns              The DNS of database.
     * @param   string  $username         The username to connect to database.
     * @param   string  $password         The password to connect to database.
     * @param   array   $driverOptions    The driver options.
     * @return  void
     * @throw   \Hoa\Database\Exception
     */
    public function __construct ( $dns, $username, $password,
                                  Array $driverOptions = array() ) {

        if(false === extension_loaded('pdo'))
            throw new \Hoa\Database\Exception(
                'The module PDO is not enabled.', 0);

        $connection = null;

        try {

            $connection = new \PDO($dns, $username, $password, $driverOptions);
        }
        catch ( \PDOException $e ) {

            throw new \Hoa\Database\Exception(
                $e->getMessage(), $e->getCode()
            );
        }

        $this->setConnection($connection);

        return;
    }

    /**
     * Set the connection.
     *
     * @access  protected
     * @param   \PDO        $connection    The PDO instance.
     * @return  \PDO
     */
    protected function setConnection ( \PDO $connection ) {

        $old               = $this->_connection;
        $this->_connection = $connection;

        return $old;
    }

    /**
     * Get the connection instance.
     *
     * @access  protected
     * @return  PDO
     * @throw   \Hoa\Database\Dal\Exception
     */
    protected function getConnection ( ) {

        if(null === $this->_connection)
            throw new \Hoa\Database\Exception(
                'Cannot return a null connection.', 1);

        return $this->_connection;
    }

    /**
     * Initiate a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function beginTransaction ( ) {

        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function commit ( ) {

        return $this->getConnection()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function rollBack ( ) {

        return $this->getConnection()->rollBack();
    }

    /**
     * Return the ID of the last inserted row or sequence value.
     *
     * @access  public
     * @param   string  $name    Name of sequence object (needed for some
     *                           driver).
     * @return  string
     * @throw   \Hoa\Database\Exception
     */
    public function lastInsertId ( $name = null ) {

        if(null === $name)
            return $this->getConnection()->lastInsertId();

        return $this->getConnection()->lastInsertId($name);
    }

    /**
     * Prepare a statement for execution and returns a statement object.
     *
     * @access  public
     * @param   string  $statement    This must be a valid SQL statement for the
     *                                target database server.
     * @param   array   $options      Options to set attributes values for the
     *                                AbstractLayer Statement.
     * @return  \Hoa\Database\AbstractLayer\Pdo\PdoStatement
     * @throw   \Hoa\Database\Exception
     */
    public function prepare ( $statement, Array $options = array() ) {

        return new PdoStatement(
            $this->getConnection()->prepare(
                $statement, $options
            )
        );
    }

    /**
     * Quote a sting for use in a query.
     *
     * @access  public
     * @param   string  $string    The string to be quoted.
     * @param   int     $type      Provide a data type hint for drivers that
     *                             have alternate quoting styles.
     * @return  string
     * @throw   \Hoa\Database\Exception
     */
    public function quote ( $string = null, $type = -1 ) {

        if($type < 0)
            return $this->getConnection()->quote($string);

        return $this->getConnection()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * \Hoa\Database\AbstractLayer\Pdo\PdoStatement object.
     *
     * @access  public
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  \Hoa\Database\AbstractLayer\Pdo\PdoStatement
     * @throw   \Hoa\Database\Exception
     */
    public function query ( $statement ) {

        $handle = $this->getConnection()->query($statement);

        if(!($handle instanceof \PDOStatement))
            throw new \Hoa\Database\Exception(
                '%3$s (%1$s/%2$d).', 2, $this->errorInfo());

        return new PdoStatement($handle);
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Database\Exception
     */
    public function errorCode ( ) {

        return $this->getConnection()->errorCode();
    }

    /**
     * Fetch extends error information associated with the last operation on the
     * database handle.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function errorInfo ( ) {

        return $this->getConnection()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Datatase\Exception
     */
    public function getAvailableDrivers ( ) {

        return $this->getConnection()->getAvailableDrivers();
    }

    /**
     * Set attributes.
     *
     * @access  public
     * @param   array   $attributes    Attributes values.
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function setAttributes ( Array $attributes ) {

        $out = true;

        foreach($attributes as $attribute => $value)
            $out &= $this->setAttribute($attribute, $value);

        return (bool) $out;
    }

    /**
     * Set a specific attribute.
     *
     * @access  public
     * @param   mixed   $attribute    Attribute name.
     * @param   mixed   $value        Attribute value.
     * @return  mixed
     * @throw   \Hoa\Database\Exception
     */
    public function setAttribute ( $attribute, $value ) {

        return $this->getConnection()->setAttribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function getAttributes ( ) {

        $out        = array();
        $attributes = array(
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
        );

        foreach($attributes as $i => $attribute)
            $out[$attribute] = $this->getAttribute($attribute);

        return $out;
    }

    /**
     * Retrieve a database connection attribute.
     *
     * @access  public
     * @param   string  $attribute    Attribute name.
     * @return  mixed
     * @throw   \Hoa\Database\Exception
     */
    public function getAttribute ( $attribute ) {

        return $this->getConnection()
                    ->getAttribute(constant('\PDO::ATTR_' . $attribute ));
    }
}

}
