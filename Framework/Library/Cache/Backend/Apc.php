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
 * @subpackage  Hoa_Cache_Backend_Apc
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
 * Class Hoa_Cache_Backend_Apc.
 *
 * Alternative PHP Cache backend manager.
 * APC is a PECL extension, so it's not installed in PHP. Take care that APC
 * module is loaded.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Apc
 */

class Hoa_Cache_Backend_Apc extends Hoa_Cache_Backend_Abstract {

    /**
     * Check if APC is loaded, else an exception is thrown.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( ) {

        if(!extension_loaded('apc'))
            throw new Hoa_Cache_Exception(
                'APC (PECL extension) is not loaded on server.', 0);
    }

    /**
     * Load data from APC cache.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content.
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->clean();

        $return = apc_fetch($id_md5);

        if(true === $exists)
            return false !== $return;

        if(   false !== $unserialize
           && false !== $this->_frontendOptions['serialize_content'])
            $return   = unserialize($return);

        return $return;
    }

    /**
     * Save cache content in APC store.
     *
     * @access  public
     * @param   string  $id_md5    Cache ID encoded in MD5.
     * @param   string  $data      Cache content.
     * @return  string
     */
    public function save ( $id_md5, $data ) {

        $this->clean();

        if(false !== $this->_frontendOptions['serialize_content'])
            $data  = serialize($data);

        return apc_store($id_md5, $data, $this->_frontendOptions['lifetime']);
    }

    /**
     * Clean expired cache.
     * Note : Hoa_Cache::CLEANING_EXPIRED is not supported with APC.
     *
     * @access  public
     * @param   string  $lifetime    Lifetime of caches.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = Hoa_Cache::CLEANING_USER ) {

        switch($lifetime) {

            case Hoa_Cache::CLEANING_ALL:
                return apc_clear_cache();
              break;

            case Hoa_Cache::CLEANING_EXPIRED:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEANING_EXPIRED constant is not supported by ' .
                    'APC cache backend.', 1);
              break;

            case Hoa_Cache::CLEANING_USER:
                return apc_clear_cache('user');
              break;

            default:
                return apc_clear_cache('user');
        }
    }

    /**
     * Remove a cache data.
     *
     * @access  public
     * @param   string  $id_md5    ID of cache to remove (encoded in MD5).
     * @return  mixed
     */
    public function remove ( $id_md5 ) {

        return apc_delete($id_md5);
    }
}
