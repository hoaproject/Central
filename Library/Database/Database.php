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
 * \Hoa\Database\Exception
 */
-> import('Database.Exception')

/**
 * \Hoa\Database\Dal
 */
-> import('Database.Dal.~');

/**
 * \Hoa\Database\Model\Table
 */
//-> import('Database.Model.Table')

/**
 * \Hoa\Database\Model\Collection
 */
//-> import('Database.Model.Collection')

/**
 * \Hoa\Database\Cache\Table
 */
//-> import('Database.Cache.Table');

}

namespace Hoa\Database {

/**
 * Class \Hoa\Database.
 *
 * Main class of the \Hoa\Database package. Manage the autoload connection, all
 * databases parameters, table and base cache etc.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Database implements \Hoa\Core\Parameter\Parameterizable {

    /**
     * Singleton.
     *
     * @var \Hoa\Database object
     */
    private static $_instance = null;

    /**
     * Collections of table caches.
     *
     * @var \Hoa\Database array
     */
    protected $cache          = array();

    /**
     * Parameters.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters    = null;



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     * @throw   \Hoa\Database\Exception
     */
    private function __construct ( Array $parameters = array() ) {

        $this->_parameters = new \Hoa\Core\Parameter(
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

                'connection.list.default.dal'      => Dal::PDO,
                'connection.list.default.dsn'      => 'mysql:host=localhost;dbname=foobar',
                'connection.list.default.username' => 'root',
                'connection.list.default.password' => '',
                'connection.list.default.options'  => true,
                'connection.autoload'              => null // or connection ID, e.g. 'default'
            )
        );

        $this->_parameters->setParameters($parameters);

        return;
    }

    /**
     * Singleton : get instance of \Hoa\Database.
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

            Dal::getInstance($autoload);
        }

        return self::$_instance;
    }

    /**
     * Get parameters.
     *
     * @access  public
     * @return  \Hoa\Core\Parameter
     */
    public function getParameters ( ) {

        return $this->_parameters;
    }

    /**
     * Get a base.
     *
     * @access  public
     * @param   string  $name    The base name.
     * @return  \Hoa\Database\Model\Base
     * @throw   \Hoa\Database\Exception
     */
    public function getBase ( $name ) {

        /*
        $oldBase = $this->getKeyword('base');

        $this->setKeyword($name);

        $directory = $this->getFormattedParameter('base.directory');
        $file      = $this->getFormattedParameter('base.file');

        require_once $directory . $file;

        $class     = $this->getFormattedParameter('base.class');

        if(!class_exists($class))
            throw new \Hoa\Database\Exception(
                'Cannot find the base class %s in %s.',
                1, array($class, $directory . $file));

        $this->setKeyword($oldBase);

        return new $class();
        */
    }

    /**
     * Get a table.
     *
     * @access  public
     * @param   string  $name    The table name, must match with :
     *                           base.table.
     * @return  \Hoa\Database\Model\Table
     * @throw   \Hoa\Database\Exception
     */
    public function getTable ( $name ) {

        /*
        $cache = $this->getTableCache($name);

        if(false !== $return = $cache->get(null))
            return $return;

        if(false === strpos($name, '.'))
            throw new \Hoa\Database\Exception(
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
            throw new \Hoa\Database\Exception(
                'Cannot find the table class %s in %s.',
                3, array($class, $directory . $file));

        $instance = new $class();

        $cache->set($name, $instance);

        $this->setKeyword('base',  $oldBase);
        $this->setKeyword('table', $oldTable);

        return $instance;
        */
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

        /*
        $this->getTableCache($name)->clean(null);
        */
    }

    /**
     * Get a table cache.
     *
     * @access  protected
     * @return  \Hoa\Database\Cache\Table
     */
    protected function getTableCache ( $name ) {

        /*
        $nameu = strtolower($name);

        if(isset($this->cache[$nameu]))
            return $this->cache[$nameu];

        $this->cache[$nameu] = new \Hoa\Database\Cache\Table($name);

        return $this->cache[$nameu];
        */
    }
}

}
