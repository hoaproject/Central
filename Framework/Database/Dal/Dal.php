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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Database_Dal
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Database_Dal_Exception
 */
import('Database.Dal.Exception');

/**
 * Hoa_Database_Dal_DalStatement
 */
import('Database.Dal.DalStatement');

/**
 * Hoa_Database
 */
import('Database.~');

/**
 * Class Hoa_Database_Dal.
 *
 * The heigher class of the Database Abstract Layer. It wrappes all DAL.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Dal
 */

class Hoa_Database_Dal implements Hoa_Framework_Parameterizable_Readable {

    /**
     * Abstract layer : DBA.
     *
     * @const string
     */
    const DBA  = 'Dba';

    /**
     * Abstract layer : DBX.
     *
     * @const string
     */
    const DBX  = 'Dbx';

    /**
     * Abstract layer : Odbc.
     *
     * @const string
     */
    const ODBC = 'Odbc';

    /**
     * Abstract layer : PDO.
     *
     * @const string
     */
    const PDO  = 'Pdo';

    /**
     * Multiton.
     *
     * @var Hoa_Database_Dal array
     */
    private static $_instance = array();

    /**
     * Current singleton ID.
     *
     * @var Hoa_Database_Dal string
     */
    private static $_id       = null;

    /**
     * The abstract layer instance.
     *
     * @var Hoa_Database_Dal_Interface_Wrapper object
     */
    protected $abstractLayer  = null;

    /**
     * Parameter of Hoa_Database.
     *
     * @var Hoa_Framework_Parameter object
     */
    protected $_parameters    = null;



    /**
     * Create a DAL instance, representing a connection to a database.
     * The constructor is private to make a multiton.
     *
     * @access  private
     * @param   string   $dalName          The abstract layer name.
     * @param   string   $dsn              The DSN of database.
     * @param   string   $username         The username to connect to database.
     * @param   string   $password         The password to connect to database.
     * @param   array    $driverOptions    The driver options.
     * @return  void
     * @throw   Hoa_Database_Dal_Exception
     */
    private function __construct ( $dalName, $dsn, $username, $password,
                                   Array $driverOption = array() ) {

        $this->_parameters = Hoa_Database::getInstance()
                                 ->shareParametersWithMe($this);

        if(   !isset($dalName)
           && !isset($dsn)
           && !isset($username)
           && !isset($password)
           && empty($driverOption)) {

            $parameters = $this->_parameters->unlinearizeBranche(
                $this,
                'connection.list'
            );

            if(!isset($parameters[$id]))
                throw new Hoa_Database_Exception(
                    'Cannot load the %s connection, because parameters are not ' .
                    'found.', 0, $id);

            $profile = $parameters[$id];

            if(!array_key_exists('dal', $profile))
                throw new Hoa_Database_Exception(
                    'The connection profile of %s need the “dal” information.',
                    1, $id);

            if(!array_key_exists('dsn', $profile))
                throw new Hoa_Database_Exception(
                    'The connection profile of %s need the “dsn” information.',
                    2, $id);

            if(!array_key_exists('username', $profile))
                throw new Hoa_Database_Exception(
                    'The connection profile of %s need the “username” information.',
                    3, $id);

            if(!array_key_exists('password', $profile))
                throw new Hoa_Database_Exception(
                    'The connection profile of %s need the “password” information.',
                    4, $id);

            if(!isset($profile['options']))
                $profile['options'] = array();

            $dalName      = $profile['dal'];
            $dsn          = $profile['dsn'];
            $username     = $profile['username'];
            $password     = $profile['password'];
            $driverOption = $profile['options'];
        }

        // Our own factory (to be more independant).
        import('Database.Dal.AbstractLayer.' . $dalName . '.~');

        $className = 'Hoa_Database_Dal_AbstractLayer_' . $dalName;
        $dal       = new $className($dsn, $username, $password, $driverOption);

        $this->setDal($dal);
    }

    /**
     * Make a multiton on the $id.
     *
     * @access  public
     * @param   string  $id               The instance ID.
     * @param   string  $dalName          The abstract layer name.
     * @param   string  $dsn              The DSN of database.
     * @param   string  $username         The username to connect to database.
     * @param   string  $password         The password to connect to database.
     * @param   array   $driverOptions    The driver options.
     * @return  Hoa_Database_Dal_Interface_Wrapper
     * @throw   Hoa_Database_Dal_Exception
     */
    public static function getInstance ( $id,
                                         $dalName  = null, $dsn      = null,
                                         $username = null, $password = null,
                                         Array $driverOption = array() ) {

        self::$_id = $id;

        if(isset(self::$_instance[$id]))
            return self::$_instance[$id];

        return self::$_instance[$id] = new self(
            $dalName,
            $dsn,
            $username,
            $password,
            $driverOption
        );
    }

    /**
     * Get the last instance of a DAL, i.e. the last used singleton.
     *
     * @access  public
     * @return  Hoa_Database_Dal_Interface_Wrapper
     * @throw   Hoa_Database_Dal_Exception
     */
    public static function getLastInstance ( ) {

        if(null === self::$_id)
            Hoa_Database::getInstance();

        if(null === self::$_id)
            throw new Hoa_Database_Dal_Exception(
                'No instance was set, cannot return the last instance.', 5);

        return self::$_instance[self::$_id];
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Set abstract layer instance.
     *
     * @access  protected
     * @param   Hoa_Database_Dal_Interface_Wrapper  $dal    The dal instance.
     * @return  Hoa_Database_Dal_Interface_Wrapper
     */
    protected function setDal ( Hoa_Database_Dal_Interface_Wrapper $dal ) {

        $old                 = $this->abstractLayer;
        $this->abstractLayer = $dal;
    }

    /**
     * Get the abstract layer instance.
     *
     * @access  protected
     * @return  Hoa_Database_Dal_Interface_Wrapper
     */
    protected function getDal ( ) {

        return $this->abstractLayer;
    }

    /**
     * Initiate a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function beginTransaction ( ) {

        return $this->getDal()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function commit ( ) {

        return $this->getDal()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Database_Dal_Exception
     */
    public function rollBack ( ) {

        return $this->getDal()->rollBack();
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
            return $this->getDal()->lastInsertId();

        return $this->getDal()->lastInsertId($name);
    }

    /**
     * Prepare a statement for execution and returns a statement object.
     *
     * @access  public
     * @param   string  $statement    This must be a valid SQL statement for the
     *                                target database server.
     * @param   array   $options      Options to set attributes values for the
     *                                AbstractLayer Statement.
     * @return  Hoa_Database_Dal_DalStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function prepare ( $statement, Array $options = array() ) {

        return new Hoa_Database_Dal_DalStatement(
            $this->getDal()->prepare(
                $statement, $options
            )
        );
    }

    /**
     * Quote a string for use in a query.
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
            return $this->getDal()->quote($string);

        return $this->getDal()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * Hoa_Database_Dal_DalStatement object.
     *
     * @access  public
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  Hoa_Database_Dal_DalStatement
     * @throw   Hoa_Database_Dal_Exception
     */
    public function query ( $statement ) {

        return new Hoa_Database_Dal_DalStatement(
            $this->getDal()->query($statement)
        );
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

        return $this->getDal()->errorCode();
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

        return $this->getDal()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Datatase_Dal_Exception
     */
    public function getAvailableDrivers ( ) {

        return $this->getDal()->getAvailableDrivers();
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

        return $this->getDal()->setAttributes($attributes);
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

        return $this->getDal()->setAtribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Dal_Exception
     */
    public function getAttributes ( ) {

        return $this->getDal()->getAttributes();
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

        return $this->getDal()->getAttribute($attribute);
    }
}
