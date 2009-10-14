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
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Memcache
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
 * Class Hoa_Cache_Backend_Memcache.
 *
 * Memcache backend manager.
 * Memcache is PECL extension, so it's not installed in PHP. Take care
 * that Memcache module is loaded.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Memcache
 */

class Hoa_Cache_Backend_Memcache extends Hoa_Cache_Backend_Abstract {

    /**
     * Memcache object.
     *
     * @var Memcache object
     */
    protected $_memcache = null;



    /**
     * Check if Memcache is loaded and prepare variables.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( ) {

        if(!extension_loaded('memcache'))
            throw new Hoa_Cache_Exception(
                'Memcache module extension is not loaded on server.', 0);
    }

    /**
     * Load a Memcache items.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content.
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->setMemcache();
        $this->clean();

        $return = $this->_memcache->get($id_md5);

        if(true === $exists)
            return is_bool($return);

        if(   false === is_bool($return)
           && false !== $unserialize
           && false !== $this->_frontendOptions['serialize_content'])
            $return = unserialize($return);

        return $return;
    }

    /**
     * Save cache content into a Memcache items.
     *
     * @access  public
     * @param   string  $id_md5    Cache ID encoded in MD5.
     * @param   string  $data      Cache content.
     * @return  string
     */
    public function save ( $id_md5, $data ) {

        $this->setMemcache();
        $this->clean();

        if(false !== $this->_frontendOptions['serialize_content'])
            $data = serialize($data);

        $flag = $this->_backendOptions['compress']['active']
                    ? MEMCACHE_COMPRESSED
                    : 0;

        $lifetime = $this->_frontendOptions['lifetime'] > 2592000 // 30 days
                        ? 2592000
                        : $this->_frontendOptions['lifetime'];

        return $this->_memcache->set($id_md5, $data, $flag, $lifetime);
    }

    /**
     * Flush all existing items on Memcache server.
     * Note : only Hoa_Cache::CLEANING_ALL is supported by Memcache.
     *
     * @access  public
     * @param   string  $lifetime    Specific lifetime.
     * @return  bool
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = Hoa_Cache::CLEANING_ALL ) {

        $this->setMemcache();

        if($lifetime != Hoa_Cache::CLEANING_ALL)
            throw new Hoa_Cache_Exception(
                'Only Hoa_Cache::CLEANING_ALL constant is supported by ' .
                'Memcache backend.', 1);

        if(false === @$this->_memcache->flush())
            throw new Hoa_Cache_Exception(
                'Flush all existing items on Memcache server %s failed.',
                2, $this->_backendOptions['database']['host']);

        return true;
    }

    /**
     * Remove a memcache items.
     *
     * @access  public
     * @param   string  $id_md5    ID of cache to remove (encoded in MD5).
     * @return  mixed
     */
    public function remove ( $id_md5 ) {

        $this->setMemcache();

        return $this->_memcache->delete($id_md5);
    }

    /**
     * Set Memcache object.
     *
     * @access  protected
     * @return  bool
     */
    protected function setMemcache ( ) {

        if(is_object($this->_memcache))
            return true;

        $this->_memcache = new Memcache();
        $this->_memcache->addServer($this->_backendOptions['database']['host'],
                                    $this->_backendOptions['database']['port'],
                                    $this->_backendOptions['database']['persistent']);
    }

    /**
     * Close connection to Memcache.
     *
     * @access  public
     * @return  bool
     */
    public function __destruct ( ) {

        if($this->_memcache !== null)
            return $this->_memcache->close();
    }
}
