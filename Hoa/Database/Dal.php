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

namespace Hoa\Database;

use Hoa\Consistency;
use Hoa\Event;
use Hoa\Zformat;

/**
 * Class \Hoa\Database\Dal.
 *
 * The higher class of the Database Abstract Layer. It wrappes all DAL.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Dal implements Zformat\Parameterizable, Event\Source
{
    /**
     * Abstract layer: DBA.
     *
     * @const string
     */
    const DBA  = 'Dba';

    /**
     * Abstract layer: DBX.
     *
     * @const string
     */
    const DBX  = 'Dbx';

    /**
     * Abstract layer: Odbc.
     *
     * @const string
     */
    const ODBC = 'Odbc';

    /**
     * Abstract layer: PDO.
     *
     * @const string
     */
    const PDO  = 'Pdo';

    /**
     * Multiton.
     *
     * @var array
     */
    private static $_instance        = [];

    /**
     * Current singleton ID.
     *
     * @var string
     */
    private static $_id              = null;

    /**
     * Current ID.
     *
     * @var string
     */
    protected $__id                  = null;

    /**
     * The layer instance.
     *
     * @var \Hoa\Database\IDal\Wrapper
     */
    protected $_layer                = null;

    /**
     * Parameter of \Hoa\Database\Dal.
     *
     * @var \Hoa\Zformat\Parameter
     */
    protected static $_parameters    = null;

    /**
     * The layer connection parameter.
     *
     * @var array
     */
    protected $_connectionParameters = [];



    /**
     * Create a DAL instance, representing a connection to a database.
     * The constructor is private to make a multiton.
     *
     * @param   array   $connectionParameters    The layer connection parameter.
     */
    private function __construct(array $connectionParameters)
    {
        $this->_connectionParameters = $connectionParameters;

        $id    = $this->__id = self::$_id;
        $event = 'hoa://Event/Database/' . $id;

        Event::register($event . ':opened', $this);
        Event::register($event . ':closed', $this);

        return;
    }

    /**
     * Initialize parameters.
     *
     * @param   array  $parameters    Parameters.
     * @return  void
     */
    public static function initializeParameters(array $parameters = [])
    {
        self::$_parameters = new Zformat\Parameter(
            __CLASS__,
            [],
            [
                /**
                 * Example:
                 *   'connection.list.default.dal'      => Dal::PDO,
                 *   'connection.list.default.dsn'      => 'sqlite:hoa://Data/Variable/Database/Foo.sqlite',
                 *   'connection.list.default.username' => '',
                 *   'connection.list.default.password' => '',
                 *   'connection.list.default.options'  => null,
                 */

                'connection.autoload' => null // or connection ID, e.g. 'default'.
            ]
        );
        self::$_parameters->setParameters($parameters);

        return;
    }

    /**
     * Make a multiton on the $id.
     *
     * @param   string  $id               The instance ID.
     * @param   string  $dalName          The database abstract layer name.
     * @param   string  $dsn              The DSN of database.
     * @param   string  $username         The username to connect to database.
     * @param   string  $password         The password to connect to database.
     * @param   array   $driverOptions    The driver options.
     * @return  \Hoa\Database\Dal
     * @throws  \Hoa\Database\Exception
     */
    public static function getInstance(
        $id,
        $dalName             = null,
        $dsn                 = null,
        $username            = null,
        $password            = null,
        array $driverOptions = []
    ) {
        if (null === self::$_parameters) {
            self::initializeParameters();
        }

        self::$_id = $id;

        if (isset(self::$_instance[$id])) {
            return self::$_instance[$id];
        }

        if (null === $dalName  &&
            null === $dsn      &&
            null === $username &&
            null === $password &&
            empty($driverOptions)) {
            $list = self::$_parameters->unlinearizeBranch('connection.list');

            if (!isset($list[$id])) {
                throw new Exception(
                    'Connection ID %s does not exist in the connection list.',
                    0,
                    $id
                );
            }

            $handle        = $list[$id];
            $dalName       = @$handle['dal']      ?: 'Undefined';
            $dsn           = @$handle['dsn']      ?: '';
            $username      = @$handle['username'] ?: '';
            $password      = @$handle['password'] ?: '';
            $driverOptions = @$handle['options']  ?: [];
        }

        return self::$_instance[$id] = new self([
            $dalName,
            $dsn,
            $username,
            $password,
            $driverOptions
        ]);
    }

    /**
     * Get the last instance of a DAL, i.e. the last used singleton.
     * If no instance was set but if the connection.autoload parameter is set,
     * then we auto-connect (autoload) a connection.
     *
     * @return  \Hoa\Database\IDal\Wrapper
     * @throws  \Hoa\Database\Exception
     */
    public static function getLastInstance()
    {
        if (null === self::$_parameters) {
            self::initializeParameters();
        }

        if (null === self::$_id) {
            $autoload = self::$_parameters->getFormattedParameter(
                'connection.autoload'
            );

            if (null !== $autoload) {
                self::getInstance($autoload);
            }
        }

        if (null === self::$_id) {
            throw new Exception(
                'No instance was set, cannot return the last instance.',
                1
            );
        }

        return self::$_instance[self::$_id];
    }

    /**
     * Get parameters.
     *
     * @return  \Hoa\Zformat\Parameter
     */
    public function getParameters()
    {
        return self::$_parameters;
    }

    /**
     * Open a connection to the database.
     *
     * @return  void
     */
    private function open()
    {
        list(
            $dalName,
            $dsn,
            $username,
            $password,
            $driverOptions
        ) = $this->_connectionParameters;

        // Please see https://bugs.php.net/55154.
        if (0 !== preg_match('#^sqlite:(.+)$#i', $dsn, $matches)) {
            $dsn = 'sqlite:' . resolve($matches[1]);
        }

        $this->setDal(
            Consistency\Autoloader::dnew(
                'Hoa\Database\Layer\\' . $dalName,
                [$dsn, $username, $password, $driverOptions]
            )
        );

        $id = $this->getId();
        Event::notify(
            'hoa://Event/Database/' . $id . ':opened',
            $this,
            new Event\Bucket([
                'id'            => $id,
                'dsn'           => $dsn,
                'username'      => $username,
                'driverOptions' => $driverOptions
            ])
        );

        return;
    }

    /**
     * Close connection to the database.
     *
     * @return  bool
     */
    public function close()
    {
        $id    = $this->getId();
        $event = 'hoa://Event/Database/' . $id;

        $this->_layer = null;
        self::$_id    = null;
        unset(self::$_instance[$id]);

        Event::notify(
            $event . ':closed',
            $this,
            new Event\Bucket(['id' => $id])
        );

        Event::unregister($event . ':opened');
        Event::unregister($event . ':closed');

        return true;
    }

    /**
     * Set database abstract layer instance.
     *
     * @param   \Hoa\Database\IDal\Wrapper  $dal    The DAL instance.
     * @return  \Hoa\Database\IDal\Wrapper
     */
    protected function setDal(IDal\Wrapper $dal)
    {
        $old          = $this->_layer;
        $this->_layer = $dal;

        return $old;
    }

    /**
     * Get the database abstract layer instance.
     *
     * @return  \Hoa\Database\IDal\Wrapper
     */
    protected function getDal()
    {
        if (null === $this->_layer) {
            $this->open();
        }

        return $this->_layer;
    }

    /**
     * Initiate a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function beginTransaction()
    {
        return $this->getDal()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function commit()
    {
        return $this->getDal()->commit();
    }

    /**
     * Roll back a transaction.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function rollBack()
    {
        return $this->getDal()->rollBack();
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
            return $this->getDal()->lastInsertId();
        }

        return $this->getDal()->lastInsertId($name);
    }

    /**
     * Prepare a statement for execution and returns a statement object.
     *
     * @param   string  $statement    This must be a valid SQL statement for the
     *                                target database server.
     * @param   array   $options      Options to set attributes values for the
     *                                layer statement.
     * @return  \Hoa\Database\DalStatement
     * @throws  \Hoa\Database\Exception
     */
    public function prepare($statement, array $options = [])
    {
        return new DalStatement(
            $this->getDal()->prepare(
                $statement, $options
            )
        );
    }

    /**
     * Quote a string for use in a query.
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
            return $this->getDal()->quote($string);
        }

        return $this->getDal()->quote($string, $type);
    }

    /**
     * Execute an SQL statement, returning a result set as a
     * \Hoa\Database\DalStatement object.
     *
     * @param   string  $statement    The SQL statement to prepare and execute.
     * @return  \Hoa\Database\DalStatement
     * @throws  \Hoa\Database\Exception
     */
    public function query($statement)
    {
        return new DalStatement(
            $this->getDal()->query($statement)
        );
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
        return $this->getDal()->errorCode();
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
        return $this->getDal()->errorInfo();
    }

    /**
     * Return an array of available drivers.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function getAvailableDrivers()
    {
        return $this->getDal()->getAvailableDrivers();
    }

    /**
     * Set attributes.
     *
     * @param   array  $attributes    Attributes values.
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function setAttributes(array $attributes)
    {
        return $this->getDal()->setAttributes($attributes);
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
        return $this->getDal()->setAttribute($attribute, $value);
    }

    /**
     * Retrieve all database connection attributes.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function getAttributes()
    {
        return $this->getDal()->getAttributes();
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
        return $this->getDal()->getAttribute($attribute);
    }

    /**
     * Get current ID.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->__id;
    }
}
