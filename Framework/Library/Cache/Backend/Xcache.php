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
 * @subpackage  Hoa_Cache_Backend_Xcache
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
 * Class Hoa_Cache_Backend_Xcache.
 *
 * Xcache manager.
 * XCache is an extension, take care that XCache is loaded.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Xcache
 */

class Hoa_Cache_Backend_Xcache extends Hoa_Cache_Backend_Abstract {

    /**
     * Check if XCache is loaded, else an exception is thrown.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( ) {

        if(!extension_loaded('xcache'))
            throw new Hoa_Cache_Exception(
                'XCache is not loaded on server.', 0);
    }

    /**
     * Load data from XCache cache.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content.
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->clean();

        if($exists === true)
            return xcache_isset($id_md5);

        $return = xcache_get($id_md5);

        if(   false !== $unserialize
           && false !== $this->_frontendOptions['serialize_content'])
            $return = unserialize($return);

        return $return;
    }

    /**
     * Save cache content in XCache store.
     *
     * @access  public
     * @param   string  $id_md5    Cache ID encoded in MD5.
     * @param   string  $data      Cache content.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    public function save ( $id_md5, $data ) {

        $this->clean();

        if(false !== $this->_frontendOptions['serialize_content'])
            $data = serialize($data);

        return xcache_set($id_md5, $data, $this->_frontendOptions['lifetime']);
    }

    /**
     * Clean expired cache.
     * Note : Hoa_Cache::CLEANING_USER is not supported, it's reserved for APC
     * backend.
     *
     * @access  public
     * @param   string  $lifetime    Lifetime of caches.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = Hoa_Cache::CLEANING_EXPIRED ) {

        switch($lifetime) {

            case Hoa_Cache::CLEANING_ALL:
                for($i = 0, $n = xcache_count(XC_TYPE_VAR); $i < $n; $i++)
                    if(false !== xcache_clear_cache(XC_TYPE_VAR, $i))
                        throw new Hoa_Cache_Exception(
                            'Clear all cache of XCache failed '. 
                            '(maybe for the cache number %s).',
                            1, $i);
              break;

            case Hoa_Cache::CLEANING_EXPIRED:
                // Manage by XCache.
              break;

            case Hoa_Cache::CLEANING_USER:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEANING_USER constant is not supported by ' .
                    'XCache backend.', 2);

            default:
                return false;
        }

        return true;
    }

    /**
     * Remove a cache data.
     * Attention : $id is an integer, not a md5 string.
     *
     * @access  public
     * @param   string  $id    ID of cache to remove (_not_ encoded in MD5).
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function remove ( $id ) {

        return xcache_clear_cache(XC_TYPE_VAR, $id);
    }
}
