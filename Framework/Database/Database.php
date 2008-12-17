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

class Hoa_Database {

    /**
     * Singleton.
     *
     * @var Hoa_Database object
     */
    private static $_instance   = null;

    /**
     * Collections of table caches.
     *
     * @var Hoa_Database array
     */
    protected $cache            = array();

    /**
     * The Hoa_Database parameters.
     *
     * @var Hoa_Database array
     */
    protected $parameters       = array(
        'base.classname'        => '(:Base)Base',
        'base.filename'         => '_(:Base).php',
        'base.directory'        => 'Data/Database/Model/(:Base)/',

        'table.classname'       => '(:Table)Table',
        'table.filename'        => '(:Table).php',
        'table.pkname'          => 'Pk(:Table)',
        'table.fkname'          => 'Fk(:Field)',

        'collection.classname'  => '(:Table)Collection',

        'cache.enable'          => true,
        'cache.filename.table'  => '(:Table).cache',
        'cache.filename.query'  => '(:Table)Query.cache',
        'cache.directory'       => 'Data/Database/Cache/(:Base)/',

        'constraint.methodname' => 'user(:Field)Constraint',

        'schema.filename'       => '(:Schema).xml',
        'schema.directory'      => 'Data/Database/Schema/',

        'connection.list'       => array(
            /**
             * An example.
             *
             *  'default'      => array(
             *      'dal'      => 'Pdo',
             *      'dsn'      => 'mysql:host=localhost;dbname=application',
             *      'username' => 'root',
             *      'password' => ''
             *      'options'  => true
             *  ),
             *  …
             */
        ),
        'connection.autoload'   => null // or connection ID, e.g. 'default'
    );



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     * @throw   Hoa_Database_Exception
     */
    private function __construct ( Array $parameters = array() ) {

        #IF_DEFINED HOA_STANDALONE
        if(empty($parameters))
            Hoa_Framework::configurePackage(
                'Database', $parameters, Hoa_Framework::CONFIGURATION_MIXE,
                array('connection.list'));
        #END_IF

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

            self::$_instance      = new self($parameters);

            if(null === $autoload = self::$_instance->parameters['connection.autoload'])
                return;

            Hoa_Database_Dal::getInstance($autoload);
        }

        return self::$_instance;
    }

    /**
     * Set parameters.
     *
     * @access  protected
     * @param   array      $parameters    Parameters.
     * @param   array      $recursive     Used for recursive parameters.
     * @return  array
     */
    protected function setParameters ( Array $parameters = array(),
                                             $recursive  = array() ) {

        if($recursive === array()) {
            $array       =& $this->parameters;
            $recursivity = false;
        }
        else {
            $array       =& $recursive;
            $recursivity = true;
        }

        if(empty($parameters))
            return $array;

        foreach($parameters as $option => $value) {

            if($option == 'connection.list') {

                $array[$option] = $value;
                continue;
            }

            if(empty($option) || (empty($value) && !is_bool($value)))
                continue;

            if(is_array($value))
                $array[$option] = $this->setParameters($value, $array[$option]);

            else
                $array[$option] = $value;
        }

        return $array;
    }

    /**
     * Get all parameters.
     *
     * @access  public
     * @return  array
     */
    public function getParameters ( ) {

        return $this->parameters;
    }

    /**
     * Get a specific parameter.
     *
     * @access  public
     * @param   string  $parameter    The parameter name.
     * @param   string  $variable     The parameter variable value.
     * @param   bool    $transform    Transform the parameter or not.
     * @return  mixed
     * @throw   Hoa_Database_Exception
     */
    public function getParameter ( $parameter, $variable = null,
                                   $transform = true ) {

        if(!isset($this->parameters[$parameter]))
            throw new Hoa_Database_Exception(
                'The parameter %s does not exists.', 0, $parameter);

        if(true === $transform)
            $return = $this->transform($this->parameters[$parameter], $variable);
        else
            $return = $this->parameters[$parameter];

        return $return;
    }

    /**
     * Verify if first letter is in upper case.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  bool
     */
    public function isUcFirst ( $string = '' ) {

        return $string == ucfirst($string);
    }

    /**
     * Match a string.
     *
     * @access  public
     * @param   string  $pattern        Pattern.
     * @param   string  $replacement    Replacement string.
     * @return  string
     */
    public function transform ( $pattern = '', $replacement = '' ) {

        if(empty($pattern))
            return false;

        preg_match('#^([^\(]+)?(?:\(:([\w]+)\))?(.*)?$#', $pattern, $matches);

        list(, $pre, $var, $post) = $matches;

        if(!empty($var)) {

            if($this->isUcFirst($var))
                $replacement = ucfirst($replacement);

            $return = $pre . $replacement . $post;
        }
        else
            $return = $pattern;

        return $return;
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

        $directory = $this->getParameter('base.directory', $name);

        if(!is_dir($directory))
            throw new Hoa_Database_Exception(
                'Cannot find the base directory %s.', 1, $directory);

        $file = $this->getParameter('base.filename', $name);

        if(!file_exists($directory . DS . $file))
            throw new Hoa_Database_Exception(
                'Cannot find the base file %s.',
                2, $directory . DS . $file);

        $class = $this->getParameter('base.classname', $name);

        require_once $directory . DS . $file;

        if(!class_exists($class))
            throw new Hoa_Database_Exception(
                'Cannot find the base class %s in %s.',
                3, array($class, $directory . DS . $file));

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
                'The table name must match with <base>.<table> ; given %s.',
                4, $name);

        list($base, $name) = explode('.', $name);

        $directory = $this->getParameter('base.directory', $base);

        if(!is_dir($directory))
            throw new Hoa_Database_Exception(
                'Cannot find the base directory %s.', 4, $directory);

        $file = $this->getParameter('table.filename', $name);

        if(!file_exists($directory . DS . $file))
            throw new Hoa_Database_Exception(
                'Cannot find the table file %s.',
                6, $directory . DS . $file);

        $class = $this->getParameter('table.classname', $name);

        require_once $directory . DS . $file;

        if(!class_exists($class))
            throw new Hoa_Database_Exception(
                'Cannot find the table class %s in %s.',
                7, array($class, $directory . DS . $file));

        $instance = new $class();

        $cache->set($name, $instance);

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
