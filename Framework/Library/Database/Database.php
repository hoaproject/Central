<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

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
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 */

class Hoa_Database implements Hoa_Framework_Parameterizable {

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
     * @var Hoa_Framework_Parameter object
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

        $this->_parameters = new Hoa_Framework_Parameter(
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
                'connection.list.default.passowrd' => '',
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

            if(null === $autoload = self::$_instance->parameters['connection.autoload'])
                return;

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
            Hoa_Framework_Parameter::PERMISSION_READ
        );

        return $this->_parameters;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
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
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
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
                2, array($class, $directory . $file));

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
                4, $name);

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
                7, array($class, $directory . $file));

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
