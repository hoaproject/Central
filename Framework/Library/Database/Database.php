<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Database
 *
 */

/**
 * Hoa_Database_Exception
 */
import('Database.Exception');

/**
 * Hoa_Database_Dal
 */
import('Database.Dal.~');

/**
 * Hoa_Database_Model_Table
 */
import('Database.Model.Table');

/**
 * Hoa_Database_Model_Collection
 */
import('Database.Model.Collection');

/**
 * Hoa_Database_Cache_Table
 */
import('Database.Cache.Table');

/**
 * Class Hoa_Database.
 *
 * Main class of the Hoa_Database package. Manage the autoload connection, all
 * databases parameters, table and base cache etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 */

class Hoa_Database implements Hoa_Core_Parameterizable {

    /**
     * Singleton.
     *
     * @var Hoa_Database object
     */
    private static $_instance = null;

    /**
     * Collections of table caches.
     *
     * @var Hoa_Database array
     */
    protected $cache          = array();

    /**
     * Parameters of Hoa_Database.
     *
     * @var Hoa_Core_Parameter object
     */
    protected $_parameters    = null;



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     * @throw   Hoa_Database_Exception
     */
    private function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Core_Parameter(
            $this,
            array(
                'base'   => null,
                'table'  => null,
                'field'  => null,
                'schema' => null
            ),
            array(
                'base.class'        => '(:base:U:)Base',
                'base.file'         => '_(:base:U:).php',
                'base.directory'    => 'hoa://Data/Etc/Database/Model/(:base:U:)/',

                'table.class'       => '(:table:U:)Table',
                'table.file'        => '(:table:U:).php',
                'table.primaryKey'  => 'Pk(:table:U:)',
                'table.foreignKey'  => 'Fk(:field:U:)',

                'collection.class'  => '(:table:U:)Collection',

                'cache.enable'      => true,
                'cache.file.table'  => '(:table:U:)Table.cache',
                'cache.file.query'  => '(:table:U:)Query.cache',
                'cache.directory'   => 'hoa://Data/Var/Private/Database/Cache/(:base:U:)/',

                'constraint.method' => 'user(:field:U:)Constraint',

                'schema.file'       => '(:schema:U:).xml',
                'schema.directory'  => 'Data/Database/Schema/',

                'connection.list.default.dal'      => Hoa_Database_Dal::PDO,
                'connection.list.default.dsn'      => 'mysql:host=localhost;dbname=foobar',
                'connection.list.default.username' => 'root',
                'connection.list.default.password' => '',
                'connection.list.default.options'  => true,
                'connection.autoload'              => null // or connection ID, e.g. 'default'
            )
        );

        $this->setParameters($parameters);
    }

    /**
     * Singleton : get instance of Hoa_Database.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public static function getInstance ( Array $parameters = array() ) {

        if(null === self::$_instance) {

            self::$_instance = new self($parameters);

            if(null === $autoload = self::$_instance->getParameter('connection.autoload'))
                return self::$_instance;

            Hoa_Database_Dal::getInstance($autoload);
        }

        return self::$_instance;
    }

    /**
     * Tricky method to centralize and share parameters only with specific
     * choosen class (as the DAL layer).
     *
     * @access  public
     * @param   Hoa_Database_Dal  $dal    DAL layer.
     * @return  void
     */
    public function shareParametersWithMe ( Hoa_Database_Dal $dal ) {

        if(get_class($dal) !== 'Hoa_Database_Dal')
            throw new Hoa_Database_Exception(
                'You shoud not used this method :-).', 0);

        $this->_parameters->shareWith(
            $this,
            $dal,
            Hoa_Core_Parameter::PERMISSION_READ
        );

        return $this->_parameters;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   Hoa_Core_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Core_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Core_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Core_Exception
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
     * @throw   Hoa_Core_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Get a base.
     *
     * @access  public
     * @param   string  $name    The base name.
     * @return  Hoa_Database_Model_Base
     * @throw   Hoa_Database_Exception
     */
    public function getBase ( $name ) {

        $oldBase = $this->getKeyword('base');

        $this->setKeyword($name);

        $directory = $this->getFormattedParameter('base.directory');
        $file      = $this->getFormattedParameter('base.file');

        require_once $directory . $file;

        $class     = $this->getFormattedParameter('base.class');

        if(!class_exists($class))
            throw new Hoa_Database_Exception(
                'Cannot find the base class %s in %s.',
                1, array($class, $directory . $file));

        $this->setKeyword($oldBase);

        return new $class();
    }

    /**
     * Get a table.
     *
     * @access  public
     * @param   string  $name    The table name, must match with :
     *                           base.table.
     * @return  Hoa_Database_Model_Table
     * @throw   Hoa_Database_Exception
     */
    public function getTable ( $name ) {

        $cache = $this->getTableCache($name);

        if(false !== $return = $cache->get(null))
            return $return;

        if(false === strpos($name, '.'))
            throw new Hoa_Database_Exception(
                'The table name must match with <base>.<table>; given %s.',
                2, $name);

        list($base, $name) = explode('.', $name);

        $oldBase   = $this->getKeyword('base');
        $oldTable  = $this->getKeyword('table');

        $this->setKeyword('base',  $base);
        $this->setKeyword('table', $table);

        $directory = $this->getFormattedParameter('base.directory');
        $file      = $this->getFormattedParameter('table.file');

        require_once $directory . $file;

        $class     = $this->getFormattedParameter('table.class');

        if(!class_exists($class))
            throw new Hoa_Database_Exception(
                'Cannot find the table class %s in %s.',
                3, array($class, $directory . $file));

        $instance = new $class();

        $cache->set($name, $instance);

        $this->setKeyword('base',  $oldBase);
        $this->setKeyword('table', $oldTable);

        return $instance;
    }

    /**
     * Clean a specific cache.
     *
     * @access  public
     * @param   string  $name    The table name, must match with :
     *                           base.table.
     * @return  void
     */
    public function cleanTableCache ( $name ) {

        $this->getTableCache($name)->clean(null);
    }

    /**
     * Get a table cache.
     *
     * @access  protected
     * @return  Hoa_Database_Cache_Table
     */
    protected function getTableCache ( $name ) {

        $nameu = strtolower($name);

        if(isset($this->cache[$nameu]))
            return $this->cache[$nameu];

        $this->cache[$nameu] = new Hoa_Database_Cache_Table($name);

        return $this->cache[$nameu];
    }
}
