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

namespace Hoa\Cache;

use Hoa\Consistency;
use Hoa\Zformat;

/**
 * Class \Hoa\Cache.
 *
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Cache implements Zformat\Parameterizable
{
    /**
     * Clean all entries.
     *
     * @const int
     */
    const CLEAN_ALL     = -1;

    /**
     * Clean expired entries.
     *
     * @const int
     */
    const CLEAN_EXPIRED =  0;

    /**
     * Clean (only for the APC backend).
     *
     * @const int
     */
    const CLEAN_USER    =  1;

    /**
     * Parameters.
     *
     * @var \Hoa\Zformat\Parameter
     */
    private $_parameters  = null;

    /**
     * Current ID (key : id, value : id_md5).
     *
     * @var array
     */
    protected static $_id = [];



    /**
     * Constructor.
     *
     * @param   array   $parameters    Parameters.
     */
    public function __construct(array $parameters = [])
    {
        $this->_parameters = new Zformat\Parameter(
            __CLASS__,
            [
                'id' => null
            ],
            [
                'lifetime'                     => 3600,
                'serialize_content'            => true,
                'make_id_with.cookie'          => true,
                'make_id_with.files'           => true,
                'make_id_with.get'             => true,
                'make_id_with.post'            => true,
                'make_id_with.server'          => false,
                'make_id_with.session'         => true,

                'apc'                          => '',

                'eaccelerator'                 => '',

                'file.cache_directory'         => 'hoa://Data/Variable/Cache/(:id:).ca',
                'file.compress.active'         => true,
                'file.compress.level'          => true,

                'memcache.compress.active'     => true,
                'memcache.database.host'       => '127.0.0.1',
                'memcache.database.port'       => 11211,
                'memcache.database.persistent' => true,

                'sqlite.cache_directory'       => 'hoa://Data/Variable/Cache/Cache.db',
                /**
                 * Example with a SQLite database loaded in memory:
                 *
                 * 'sqlite.cache_directory'    => ':memory:',
                 */
                'sqlite.database.host'         => '127.0.0.1',

                'xcache'                       => '',

                'zendplatform'                 => ''
            ]
        );

        $this->_parameters->setParameters($parameters);

        return;
    }

    /**
     * Get parameters.
     *
     * @return  \Hoa\Zformat\Parameter
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Make an ID according to frontend options.
     * We privilage _makeId to built suffixe of ID.
     * As an idenfier shoud be unique, we add environments variables values. In
     * this way, the identifier represents the current state of application.
     *
     * @param   string    $id    Identifier.
     * @return  string
     * @throws  \Hoa\Cache\Exception
     */
    protected function makeId($id = null)
    {
        $_id = $id;

        if (true === $this->_parameters->getParameter('make_id_with.cookie') &&
            isset($_COOKIE)) {
            $_id .= serialize($this->ksort($_COOKIE));
        }

        if (true === $this->_parameters->getParameter('make_id_with.files') &&
            isset($_FILES)) {
            $_id .= serialize($this->ksort($_FILES));
        }

        if (true === $this->_parameters->getParameter('make_id_with.get') &&
            isset($_GET)) {
            $_id .= serialize($this->ksort($_GET));
        }

        if (true === $this->_parameters->getParameter('make_id_with.post') &&
            isset($_POST)) {
            $_id .= serialize($this->ksort($_POST));
        }

        if (true === $this->_parameters->getParameter('make_id_with.server') &&
            isset($_SERVER)) {
            $_id .= serialize($this->ksort($_SERVER));
        }

        if (true === $this->_parameters->getParameter('make_id_with.session') &&
            isset($_SESSION)) {
            $_id .= serialize($this->ksort($_SESSION));
        }

        return self::$_id[$id] = md5($id . $_id);
    }

    /**
     * Set the current ID.
     *
     * @param   string  $id    ID.
     * @return  string
     */
    protected function setId($id)
    {
        $old = $this->_parameters->getKeyword('id');
        $this->_parameters->setKeyword('id', $id);

        return $old;
    }

    /**
     * Get last ID.
     *
     * @return  string
     * @throws  \Hoa\Cache\Exception
     */
    protected function getId()
    {
        end(self::$_id);

        return key(self::$_id);
    }

    /**
     * Get last ID in MD5 format.
     *
     * @return  string
     * @throws  \Hoa\Cache\Exception
     */
    protected function getIdMd5()
    {
        end(self::$_id);

        return current(self::$_id);
    }

    /**
     * Remove a couple of ID/ID_MD5.
     * By default, the last ID is removed.
     *
     * @return  void
     */
    protected function removeId()
    {
        unset(self::$_id[$this->getId()]);

        return;
    }

    /**
     * Sort array of arrays etc., according to keys, recursively.
     *
     * @param   array   $array    Array to sort.
     * @return  array
     */
    public function ksort(array &$array)
    {
        ksort($array);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->ksort($value);
            }
        }

        return $array;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Cache\Cache');
