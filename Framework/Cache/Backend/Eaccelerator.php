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
 * @subpackage  Hoa_Cache_Backend_Eaccelerator
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
 * Class Hoa_Cache_Backend_Eaccelerator.
 *
 * Eaccelerator backend manager.
 * EAccelerator is an extension, take care that EAccelerator is loaded.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Eaccelerator
 */

class Hoa_Cache_Backend_Eaccelerator extends Hoa_Cache_Backend_Abstract {

    /**
     * Check if EAccelerator is loaded, else an exception is thrown.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( ) {

        if(!extension_loaded('eaccelerator'))
            throw new Hoa_Cache_Exception(
                'EAccelerator is not loaded on server.', 0);
    }

    /**
     * Load data from EAccelerator cache.
     * Data is obligatory unseralized, please see the self::save() method.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content.
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->clean();

        $return = eaccelerator_get($id_md5);

        if($exists === true)
            return false !== $return;

        $return = unserialize($return);

        return $return;
    }

    /**
     * Save cache content in EAccelerator store.
     * All data is obligatory serialized.
     *
     * @access  public
     * @param   string  $id_md5    Cache ID encoded in MD5.
     * @param   string  $data      Cache content.
     * @return  string
     */
    public function save ( $id_md5, $data ) {

        $this->clean();

        $data = serialize($data);

        return eaccelerator_put($id_md5, $data, $this->_frontendOptions['lifetime']);
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
                $infos = eaccelerator_list_keys();

                // EAccelerator bug (http://eaccelerator.net/ticket/287).
                foreach($infos as $foo => $info) {

                    $key = 0 === strpos($info['name'], ':') ? substr($info['name'], 1) : $info['name'];
                    if(false === eaccelerator_rm($key))
                        throw new Hoa_Cache_Exception(
                            'Remove all existing cache file failed (maybe for the %s cache).',
                            1, $key);
                }
              break;

            case Hoa_Cache::CLEANING_EXPIRED:
                // Manage by EAccelerator.
              break;

            case Hoa_Cache::CLEANING_USER:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEANING_USER constant is not supported by ' .
                    'EAccelerator backend.', 2);

            default:
                return false;
        }

        return true;
    }

    /**
     * Remove a cache data.
     *
     * @access  public
     * @param   string  $id_md5    ID of cache to remove (encoded in MD5).
     * @return  mixed
     */
    public function remove ( $id_md5 ) {

        return eaccelerator_rm($id_md5);
    }
}
