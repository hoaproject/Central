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
 * \Hoa\Database\DalStatement
 */
-> import('Database.DalStatement');

}

namespace Hoa\Database {

/**
 * Class \Hoa\Database\Dal.
 *
 * The heigher class of the Database Abstract Layer. It wrappes all DAL.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Dal implements \Hoa\Core\Parameter\Parameterizable {

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
    private static $_instance     = array();

    /**
     * Current singleton ID.
     *
     * @var \Hoa\Database\Dal string
     */
    private static $_id           = null;

    /**
     * The abstract layer instance.
     *
     * @var \Hoa\Database\IDal\Wrapper object
     */
    protected $_abstractLayer     = null;

    /**
     * Parameter of \Hoa\Database\Dal.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected static $_parameters = null;



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
     * @throw   \Hoa\Database\Exception
     */
    private function __construct ( $dalName, $dsn, $username, $password,
                                   Array $driverOptions = array() ) {

        if(0 !== preg_match('#^sqlite:([^$]+)$#i', $dsn, $matches))
            $dsn = 'sqlite:' . resolve($matches[1]);

        $this->setDal(dnew(
            '\Hoa\Database\AbstractLayer\\' . $dalName,
            array($dsn, $username, $password, $driverOptions)
        ));

        return;
    }

    /**
     * Initialize parameters.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public static function initializeParameters ( Array $parameters = array() ) {

        self::$_parameters = new \Hoa\Core\Parameter(
            __CLASS__,
            array(),
            array(
                /**
                 * Example:
                 *   'connection.list.default.dal'      => Dal::PDO,
                 *   'connection.list.default.dsn'      => 'sqlite:hoa://Data/Variable/Database/Foo.sqlite',
                 *   'connection.list.default.username' => '',
                 *   'connection.list.default.password' => '',
                 *   'connection.list.default.options'  => null,
                 */

                'connection.autoload' => null // or connection ID, e.g. 'default'.
            )
        );
        self::$_parameters->setParameters($parameters);

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
     * @return  \Hoa\Database\IDal\Wrapper
     * @throw   \Hoa\Database\Exception
     */
    public static function getInstance ( $id,
                                         $dalName  = null, $dsn      = null,
                                         $username = null, $password = null,
                                         Array $driverOptions = array() ) {

        if(null === self::$_parameters)
            self::initializeParameters();

        self::$_id = $id;

        if(isset(self::$_instance[$id]))
            return self::$_instance[$id];

        if(   null === $dalName
           && null === $dsn
           && null === $username
           && null === $password
           && empty($driverOptions)) {

            $list = self::$_parameters->unlinearizeBranche('connection.list');

            if(!isset($list[$id]))
                throw new Exception(
                    'Connection ID %s does not exist in the connection list.',
                    0, $id);

            $handle        = $list[$id];
            $dalName       = @$handle['dal']      ?: 'Undefined';
            $dsn           = @$handle['dsn']      ?: '';
            $username      = @$handle['username'] ?: '';
            $password      = @$handle['password'] ?: '';
            $driverOptions = @$handle['options']  ?: array();
        }

        return self::$_instance[$id] = new self(
            $dalName,
            $dsn,
            $username,
            $password,
            $driverOptions
        );
    }

    /**
     * Get the last instance of a DAL, i.e. the last used singleton.
     * If no instance was set but if the connection.autoload parameter is set,
     * then we auto-connect (autoload) a connection.
     *
     * @access  public
     * @return  \Hoa\Database\IDal\Wrapper
     * @throw   \Hoa\Database\Exception
     */
    public static function getLastInstance ( ) {

        if(null === self::$_parameters)
            self::initializeParameters();

        if(null === self::$_id) {

            $autoload = self::$_parameters->getFormattedParameter(
                'connection.autoload'
            );

            if(null !== $autoload)
                self::getInstance($autoload);
        }

        if(null === self::$_id)
            throw new Exception(
                'No instance was set, cannot return the last instance.', 0);

        return self::$_instance[self::$_id];
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters ( ) {

        return self::$_parameters;
    }

    /**
     * Set abstract layer instance.
     *
     * @access  protected
     * @param   \Hoa\Database\IDal\Wrapper  $dal    The DAL instance.
     * @return  \Hoa\Database\IDal\Wrapper
     */
    protected function setDal ( IDal\Wrapper $dal ) {

        $old                  = $this->_abstractLayer;
        $this->_abstractLayer = $dal;
    }

    /**
     * Get the abstract layer instance.
     *
     * @access  protected
     * @return  \Hoa\Database\IDal\Wrapper
     */
    protected function getDal ( ) {

        return $this->_abstractLayer;
    }

    /**
     * Initiate a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function beginTransaction ( ) {

        return $this->getDal()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function commit ( ) {

        return $this->getDal()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
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
     * @throw   \Hoa\Database\Exception
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
     * @return  \Hoa\Database\DalStatement
     * @throw   \Hoa\Database\Exception
     */
    public function prepare ( $statement, Array $options = array() ) {

        return new \Hoa\Database\DalStatement(
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
     * @throw   \Hoa\Database\Exception
     */
    public function quote ( $string = null, $type = -1 ) {

        if($type < 0)
            return $this->getDal()->quote($string);

        return $this->getDal()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * \Hoa\Database\DalStatement object.
     *
     * @access  public
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  \Hoa\Database\DalStatement
     * @throw   \Hoa\Database\Exception
     */
    public function query ( $statement ) {

        return new \Hoa\Database\DalStatement(
            $this->getDal()->query($statement)
        );
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

        return $this->getDal()->errorCode();
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

        return $this->getDal()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Datatase\Exception
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
     * @throw   \Hoa\Database\Exception
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
     * @throw   \Hoa\Database\Exception
     */
    public function setAttribute ( $attribute, $value ) {

        return $this->getDal()->setAtribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
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
     * @throw   \Hoa\Database\Exception
     */
    public function getAttribute ( $attribute ) {

        return $this->getDal()->getAttribute($attribute);
    }
}

}
