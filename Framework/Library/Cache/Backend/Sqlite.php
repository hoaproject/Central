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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Sqlite
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache
 */
import('Cache.~');

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Hoa_Cache_Backend_Abstract
 */
import('Cache.Backend.Abstract');

/**
 * Class Hoa_Cache_Backend_Sqlite.
 *
 * SQLite backend manager.
 * SQLite is an extension, take care that SQLite is loaded.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Sqlite
 */

class Hoa_Cache_Backend_Sqlite extends Hoa_Cache_Backend_Abstract {

    /**
     * SQLite connexion.
     *
     * @var Hoa_Cache_Backend_Sqlite resource
     */
    protected $_sqlite = null;



    /**
     * Check if SQLite is loaded, else an exception is thrown.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( ) {

        if(!extension_loaded('sqlite'))
            throw new Hoa_Cache_Exception(
                'SQLite is not loaded on server.', 0);
    }

    /**
     * Load data from SQLite database.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content.
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->setSqlite();
        $this->clean();

        $statement = 'SELECT data FROM hoa_cache ' . "\n" .
                     'WHERE  id = \'' . sqlite_escape_string($id_md5) . '\'';
        $query     = sqlite_query($statement, $this->getSqlite());

        if(0     === $num = sqlite_num_rows($query))
            return false;

        if(true  === $exists)
            return 0 !== $num;

        $return    = sqlite_fetch_single($query);

        if(   $unserialize !== false
           && $this->_frontendOptions['serialize_content'] !== false)
            $return = unserialize($return);

        return $return;
    }

    /**
     * Save cache content in SQLite database.
     *
     * @access  public
     * @param   string  $id_md5    Cache ID encoded in MD5.
     * @param   string  $data      Cache content.
     * @return  string
     */
    public function save ( $id_md5, $data ) {

        $this->setSqlite();
        $this->clean();

        if($this->_frontendOptions['serialize_content'] !== false)
            $data = serialize($data);

        $lifetime = $this->_frontendOptions['lifetime'];

        if(false === $this->load($id_md5, true, true))
            $statement = 'INSERT INTO hoa_cache (' . "\n" .
                         '       id, ' . "\n" .
                         '       data, ' . "\n" .
                         '       will_expire_at ' . "\n" .
                         ')' . "\n" .
                         'VALUES (' . "\n" .
                         '       \'' . sqlite_escape_string($id_md5) . '\', ' . "\n" .
                         '       \'' . sqlite_escape_string($data) . '\', ' . "\n" .
                         '       \'' . (time() + $lifetime) . '\' ' . "\n" .
                         ')';
        else
            $statement = 'UPDATE hoa_cache ' . "\n" .
                         'SET    data           = \'' . sqlite_escape_string($data)  . '\', ' . "\n" .
                         '       will_expire_at = \'' . (time() + $lifetime) . '\' ' . "\n" .
                         'WHERE  id             = \'' . sqlite_escape_string($id_md5) . '\'';

        return sqlite_query($statement, $this->getSqlite());
    }

    /**
     * Clean expired cache.
     *
     * @access  public
     * @param   string  $lifetime    Lifetime of caches.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = Hoa_Cache::CLEANING_EXPIRED ) {

        $this->setSqlite();

        switch($lifetime) {

            case Hoa_Cache::CLEANING_ALL:
                $statement = 'DELETE FROM hoa_cache';
              break;

            case Hoa_Cache::CLEANING_EXPIRED:
                $statement = 'DELETE FROM hoa_cache ' . "\n" .
                             'WHERE  will_expire_at < ' . sqlite_escape_string(time());
              break;

            case Hoa_Cache::CLEANING_USER:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEANING_USER constant is not supported by ' .
                    'SQLite cache backend.', 1);
              break;
        }

        if(0 === $num = sqlite_num_rows(sqlite_query($statement, $this->getSqlite())))
            return false;

        return $num;
    }

    /**
     * Remove a cache data.
     *
     * @access  public
     * @param   string  $id_md5    ID of cache to remove (encoded in MD5).
     * @return  mixed
     */
    public function remove ( $id_md5 ) {

        $this->setSqlite();

        $statement = 'DELETE FROM hoa_cache ' . "\n" .
                     'WHERE  id = \'' . sqlite_escape_string($id_md5) . '\'';

        return sqlite_query($statement, $this->getSqlite());
    }

    /**
     * Set the SQLite support. If the specified database is :memory: or an
     * unexistant file, the self::createSchema() will be called.
     * If an existent file is given, it must contain the hoa_cache table
     * (please, see the self::createSchema() method). No test is done.
     * By default, the database -> host value will be choosen, but if it's
     * empty, the cache_directory will be choosen to place the database file.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    protected function setSqlite ( ) {

        if(null !== $this->_sqlite)
            return;

        $database     = $this->_backendOptions['database']['host'];

        if(empty($database))
            $database = $this->_backendOptions['cache_directory'];

        $new = false;
        if($database == ':memory:')
            $new = true;
        elseif(!is_file($database)) {

            $new  = true;
            $dir  = dirname($database);
            $mask = umask(0000);
            if(!is_dir($dir))
                @mkdir($dir, 0777, true);

            touch($database);
            umask($mask);
        }

        if(false === $this->_sqlite = @sqlite_open($database, 0644, $error))
            throw new Hoa_Cache_Exception(
                'Unable to connect to SQLite database : %s.', 2, $error);

        $new and $this->createSchema();

        return;
    }

    /**
     * Get the SQLite resource.
     *
     * @access  protected
     * @return  resource
     */
    protected function getSqlite ( ) {

        return $this->_sqlite;
    }

    /**
     * Create the schema, i.e. create the hoa_cache table and the
     * hoa_cache_unique index.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    protected function createSchema ( ) {

        $statements   = array(
            'table'   => 'CREATE TABLE hoa_cache (' . "\n" .
                         '    id VARCHAR(32), ' . "\n" .
                         '    data LONGVARCHAR, ' . "\n" .
                         '    will_expire_at TIMESTAMP' . "\n" .
                         ')',
            'index'   => 'CREATE UNIQUE INDEX hoa_cache_unique ON hoa_cache (' . "\n" .
                         '    id' . "\n" .
                         ')'
        );

        $database     = $this->_backendOptions['database'];

        if(empty($database))
            $database = $this->_backendOptions['cache_directory'];

        foreach($statements as $name => $statement)
            if(false === sqlite_query($statement, $this->getSqlite()))
                throw new Hoa_Cache_Exception(
                    sqlite_error_string(sqlite_last_error($database)), 3);

        return;
    }

    /**
     * Close the SQLite connexion.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        sqlite_close($this->getSqlite());
    }
}
