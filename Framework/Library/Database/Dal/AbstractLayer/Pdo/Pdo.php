<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal_AbstractLayer_Pdo
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Database_Dal_Exception
 */
import('Database.Dal.Exception');

/**
 * Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement
 */
import('Database.Dal.AbstractLayer.Pdo.PdoStatement');

/**
 * Hoa_Database_Dal_Interface_Wrapper
 */
import('Database.Dal.Interface.Wrapper');

/**
 * Class Hoa_Database_Dal_AbstractLayer_Pdo.
 *
 * Wrap PDO.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal_AbstractLayer_Pdo
 */

class Hoa_Database_Dal_AbstractLayer_Pdo implements Hoa_Database_Dal_Interface_Wrapper {

    /**
     * Connection to database.
     *
     * @var PDO object
     */
    protected $connection = null;



    /**
     * Create a DAL instance, representing a connection to a database.
     *
     * @access  public
     * @param   string  $dns              The DNS of database.
     * @param   string  $username         The username to connect to database.
     * @param   string  $password         The password to connect to database.
     * @param   array   $driverOptions    The driver options.
     * @return  void
     * @throw   Hoa_Database_Dal_Exception
     */
    public function __construct ( $dns, $username, $password,
                                  Array $driverOption = array() ) {

        if(false === extension_loaded('pdo'))
            throw new Hoa_Database_Dal_Exception(
                'The module PDO is not enabled.', 0);

        $connection = null;

        try {

            $connection = new PDO($dns, $username, $password, $driverOption);
        }
        catch ( PDOException $e ) {

            throw new Hoa_Database_Dal_Exception(
                $e->getMessage(), $e->getCode()
            );
        }

        $this->setConnection($connection);
    }

    /**
     * Set the connection.
     *
     * @access  protected
     * @param   PDO        $connection    The PDO instance.
     * @return  PDO
     */
    protected function setConnection ( PDO $connection ) {

        $old              = $this->connection;
        $this->connection = $connection;

        return $old;
    }

    /**
     * Get the connection instance.
     *
     * @access  protected
     * @return  PDO
     * @throw   Hoa_Database_Dal_Exception
     */
    protected function getConnection ( ) {

        if(null === $this->connection)
            throw new Hoa_Database_Dal_Exception(
                'Cannot return a null connection.', 1);

        return $this->connection;
    }

    /**
     * Initiate a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function beginTransaction ( ) {

        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function commit ( ) {

        return $this->getConnection()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
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
     * @throw   Hoa_Database_Dal_Exception
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
     * @return  Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function prepare ( $statement, Array $options = array() ) {

        return new Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement(
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
     * @throw   Hoa_Database_Dal_Exception
     */
    public function quote ( $string = null, $type = -1 ) {

        if($type < 0)
            return $this->getConnection()->quote($string);

        return $this->getConnection()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement object.
     *
     * @access  public
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function query ( $statement ) {

        $tmp = $this->getConnection()->query($statement);

        if(!($tmp instanceof PDOStatement))
            throw new Hoa_Database_Dal_Exception(
                '%3$s (%1$s/%2$d).', 2, $this->errorInfo());

        return new Hoa_Database_Dal_AbstractLayer_Pdo_PdoStatement($tmp);
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Database_Dal_Exception
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
     * @throw   Hoa_Database_Dal_Exception
     */
    public function errorInfo ( ) {

        return $this->getConnection()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Datatase_Dal_Exception
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
     * @throw   Hoa_Database_Dal_Exception
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
     * @throw   Hoa_Database_Dal_Exception
     */
    public function setAttribute ( $attribute, $value ) {

        return $this->getConnection()->setAttribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
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

        foreach($attributes as $i => $suffix)
            $attributes[constant('PDO::ATTR_' . $suffix)] =
                $this->getAttribute(constant('PDO::ATTR_' . $suffix));
    }

    /**
     * Retrieve a database connection attribute.
     *
     * @access  public
     * @param   string  $attribute    Attribute name.
     * @return  mixed
     * @throw   Hoa_Database_Dal_Exception
     */
    public function getAttribute ( $attribute ) {

        return $this->getConnection()
                    ->getAttribute(constant('PDO::ATTR_' . $attribute ));
    }
}
