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

namespace Hoa\Cache\Backend;

use Hoa\Cache;
use Hoa\File as HoaFile;

/**
 * Class \Hoa\Cache\Backend\Sqlite.
 *
 * SQLite backend manager.
 * SQLite is an extension, take care that SQLite is loaded.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Sqlite extends Backend
{
    /**
     * SQLite connexion.
     *
     * @var resource
     */
    protected $_sqlite = null;



    /**
     * Check if SQLite is loaded, else an exception is thrown.
     *
     * @param   array  $parameters    Parameters.
     * @throws  \Hoa\Cache\Exception
     */
    public function __construct(array $parameters = [])
    {
        if (!extension_loaded('sqlite')) {
            throw new Cache\Exception(
                'SQLite is not loaded on server.',
                0
            );
        }

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content in SQLite database.
     *
     * @param   mixed  $data    Data to store.
     * @return  void
     */
    public function store($data)
    {
        $this->setSqlite();
        $this->clean();

        if (true  === $this->_parameters->getParameter('serialize_content')) {
            $data = serialize($data);
        }

        $lifetime = $this->_parameters->getParameter('lifetime');
        $md5      = $this->getIdMd5();

        $statement =
            'SELECT data FROM hoa_cache ' . "\n" .
            'WHERE  id = \'' . sqlite_escape_string($this->getIdMd5()) . '\'';
        $query = sqlite_query($statement, $this->getSqlite());

        if (0 === sqlite_num_rows($query)) {
            $statement =
                'INSERT INTO hoa_cache (' . "\n" .
                '    id, ' . "\n" .
                '    data, ' . "\n" .
                '    will_expire_at ' . "\n" .
                ')' . "\n" .
                'VALUES (' . "\n" .
                '    \'' .
                sqlite_escape_string($md5) . '\', ' . "\n" .
                '    \'' .
                sqlite_escape_string($data) . '\', ' . "\n" .
                '    \'' .
                (time() + $lifetime) . '\' ' . "\n" .
                ')';
        } else {
            $statement =
                'UPDATE hoa_cache ' . "\n" .
                'SET    data           = \'' .
                sqlite_escape_string($data) . '\', ' . "\n" .
                '       will_expire_at = \'' .
                (time() + $lifetime) . '\' ' . "\n" .
                'WHERE  id             = \'' .
                sqlite_escape_string($md5) . '\'';
        }

        return sqlite_query($statement, $this->getSqlite());
    }

    /**
     * Load data from SQLite database.
     *
     * @return  mixed
     */
    public function load()
    {
        $this->setSqlite();
        $this->clean();

        $statement =
            'SELECT data FROM hoa_cache ' . "\n" .
            'WHERE  id = \'' .
            sqlite_escape_string($this->getIdMd5()) . '\'';
        $query = sqlite_query($statement, $this->getSqlite());

        if (0 === sqlite_num_rows($query)) {
            return false;
        }

        $content = sqlite_fetch_single($query);

        if (true === $this->_parameters->getParameter('serialize_content')) {
            $content = unserialize($content);
        }

        return $content;
    }

    /**
     * Clean expired cache.
     *
     * @param   string  $lifetime    Lifetime of caches.
     * @return  void
     * @throws  \Hoa\Cache\Exception
     */
    public function clean($lifetime = Cache::CLEAN_EXPIRED)
    {
        $this->setSqlite();

        switch ($lifetime) {
            case Cache::CLEAN_ALL:
                $statement = 'DELETE FROM hoa_cache';

                break;

            case Cache::CLEAN_EXPIRED:
                $statement =
                    'DELETE FROM hoa_cache ' . "\n" .
                    'WHERE  will_expire_at < ' . sqlite_escape_string(time());

                break;

            case Cache::CLEAN_USER:
                throw new Cache\Exception(
                    '\Hoa\Cache::CLEAN_USER constant is not supported by ' .
                    'SQLite cache backend.',
                    1
                );

                break;
        }

        sqlite_query($statement, $this->getSqlite());

        return $num;
    }

    /**
     * Remove a cache data.
     *
     * @return  void
     */
    public function remove()
    {
        $this->setSqlite();

        $statement =
            'DELETE FROM hoa_cache ' . "\n" .
            'WHERE  id = \'' . sqlite_escape_string($id_md5) . '\'';

        sqlite_query($statement, $this->getSqlite());

        return;
    }

    /**
     * Set the SQLite support. If the specified database is :memory: or an
     * unexistant file, the self::createSchema() will be called.
     * If an existent file is given, it must contain the hoa_cache table
     * (please, see the self::createSchema() method). No test is done.
     * By default, the database -> host value will be choosen, but if it's
     * empty, the cache_directory will be choosen to place the database file.
     *
     * @return  void
     * @throws  \Hoa\Cache\Exception
     */
    protected function setSqlite()
    {
        if (null !== $this->_sqlite) {
            return;
        }

        $database = $this->_parameters->getParameter('sqlite.database.host');

        if (empty($database)) {
            $database = $this->_parameters->getParameter('sqlite.cache.directory');
        }

        $new = false;

        if (':memory:' === $database) {
            $new = true;
        } else {
            $new  = true;
            HoaFile\Directory::create(
                $database,
                HoaFile\Directory::MODE_CREATE_RECURSIVE
            );
        }

        if (false === $this->_sqlite = @sqlite_open($database, 0644, $error)) {
            throw new Cache\Exception(
                'Unable to connect to SQLite database : %s.',
                2,
                $error
            );
        }

        $new and $this->createSchema();

        return;
    }

    /**
     * Get the SQLite resource.
     *
     * @return  resource
     */
    protected function getSqlite()
    {
        return $this->_sqlite;
    }

    /**
     * Create the schema, i.e. create the hoa_cache table and the
     * hoa_cache_unique index.
     *
     * @return  void
     * @throws  \Hoa\Cache\Exception
     */
    protected function createSchema()
    {
        $statements = [
            'table' =>
                'CREATE TABLE hoa_cache (' . "\n" .
                '    id VARCHAR(32), ' . "\n" .
                '    data LONGVARCHAR, ' . "\n" .
                '    will_expire_at TIMESTAMP' . "\n" .
                ')',
            'index' =>
                'CREATE UNIQUE INDEX hoa_cache_unique ON hoa_cache (' . "\n" .
                '    id' . "\n" .
               ')'
        ];

        foreach ($statements as $name => $statement) {
            if (false === sqlite_query($statement, $this->getSqlite())) {
                throw new Cache\Exception(
                    sqlite_error_string(
                        sqlite_last_error($this->getSqlite())
                    ),
                    3
                );
            }
        }

        return;
    }

    /**
     * Close the SQLite connexion.
     *
     * @return  void
     */
    public function __destruct()
    {
        sqlite_close($this->getSqlite());

        return;
    }
}
