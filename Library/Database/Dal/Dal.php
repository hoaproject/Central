<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Database\Dal\Exception
 */
-> import('Database.Dal.Exception')

/**
 * \Hoa\Database\Dal\DalStatement
 */
-> import('Database.Dal.DalStatement')

/**
 * \Hoa\Database
 */
-> import('Database.~');

}

namespace Hoa\Database\Dal {

/**
 * Class \Hoa\Database\Dal.
 *
 * The heigher class of the Database Abstract Layer. It wrappes all DAL.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Dal implements \Hoa\Core\Parameterizable\Readable {

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
     * @var \Hoa\Database\Dal array
     */
    private static $_instance = array();

    /**
     * Current singleton ID.
     *
     * @var \Hoa\Database\Dal string
     */
    private static $_id       = null;

    /**
     * The abstract layer instance.
     *
     * @var \Hoa\Database\Dal\IDal\Wrapper object
     */
    protected $abstractLayer  = null;

    /**
     * Parameter of \Hoa\Database.
     *
     * @var \Hoa\Core\Parameter object
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
     * @throw   \Hoa\Database\Dal\Exception
     */
    private function __construct ( $dalName, $dsn, $username, $password,
                                   Array $driverOption = array() ) {

        $this->_parameters = \Hoa\Database::getInstance()
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

            if(!isset($parameters[self::$_id]))
                throw new Exception(
                    'Cannot load the %s connection, because parameters are not ' .
                    'found.', 0, self::$_id);

            $profile = $parameters[self::$_id];

            if(!array_key_exists('dal', $profile))
                throw new Exception(
                    'The connection profile of %s need the “dal” information.',
                    1, self::$_id);

            if(!array_key_exists('dsn', $profile))
                throw new Exception(
                    'The connection profile of %s need the “dsn” information.',
                    2, self::$_id);

            if(!array_key_exists('username', $profile))
                throw new Exception(
                    'The connection profile of %s need the “username” information.',
                    3, self::$_id);

            if(!array_key_exists('password', $profile))
                throw new Exception(
                    'The connection profile of %s need the “password” information.',
                    4, self::$_id);

            if(!isset($profile['options']))
                $profile['options'] = array();

            $dalName      = $profile['dal'];
            $dsn          = $profile['dsn'];
            $username     = $profile['username'];
            $password     = $profile['password'];
            $driverOption = $profile['options'];
        }

        $this->setDal(dnew(
            '\Hoa\Database\Dal\AbstractLayer\\' . $dalName,
            array($dsn, $username, $password, $driverOption)
        ));

        return;
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
     * @return  \Hoa\Database\Dal\IDal\Wrapper
     * @throw   \Hoa\Database\Dal\Exception
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
     * @return  \Hoa\Database\Dal\IDal\Wrapper
     * @throw   \Hoa\Database\Dal\Exception
     */
    public static function getLastInstance ( ) {

        if(null === self::$_id)
            \Hoa\Database::getInstance();

        if(null === self::$_id)
            throw new Exception(
                'No instance was set, cannot return the last instance.', 5);

        return self::$_instance[self::$_id];
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Set abstract layer instance.
     *
     * @access  protected
     * @param   \Hoa\Database\Dal\IDal\Wrapper  $dal    The dal instance.
     * @return  \Hoa\Database\Dal\IDal\Wrapper
     */
    protected function setDal ( \Hoa\Database\Dal\IDal\Wrapper $dal ) {

        $old                 = $this->abstractLayer;
        $this->abstractLayer = $dal;
    }

    /**
     * Get the abstract layer instance.
     *
     * @access  protected
     * @return  \Hoa\Database\Dal\IDal\Wrapper
     */
    protected function getDal ( ) {

        return $this->abstractLayer;
    }

    /**
     * Initiate a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function beginTransaction ( ) {

        return $this->getDal()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function commit ( ) {

        return $this->getDal()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Dal\Exception
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
     * @throw   \Hoa\Database\Dal\Exception
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
     * @return  \Hoa\Database\Dal\DalStatement
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function prepare ( $statement, Array $options = array() ) {

        return new \Hoa\Database\Dal\DalStatement(
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
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function quote ( $string = null, $type = -1 ) {

        if($type < 0)
            return $this->getDal()->quote($string);

        return $this->getDal()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * \Hoa\Database\Dal\DalStatement object.
     *
     * @access  public
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  \Hoa\Database\Dal\DalStatement
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function query ( $statement ) {

        return new \Hoa\Database\Dal\DalStatement(
            $this->getDal()->query($statement)
        );
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Database\Dal\Exception
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
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function errorInfo ( ) {

        return $this->getDal()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Datatase\Dal\Exception
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
     * @throw   \Hoa\Database\Dal\Exception
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
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function setAttribute ( $attribute, $value ) {

        return $this->getDal()->setAtribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Dal\Exception
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
     * @throw   \Hoa\Database\Dal\Exception
     */
    public function getAttribute ( $attribute ) {

        return $this->getDal()->getAttribute($attribute);
    }
}

}
