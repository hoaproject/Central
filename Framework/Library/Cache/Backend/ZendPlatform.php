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
 * @subpackage  Hoa_Cache_Backend_ZendPlatform
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
 * Hoa_File_Finder
 */
import('File.Finder');

/**
 * Class Hoa_Cache_Backend_ZendPlatform.
 *
 * ZendPlatform backend manager (yes yes, Zend :-)).
 * Inspiration from Zend_Cache_Backend_ZendPlatform class for making this class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_ZendPlatform
 * @todo        Need to be tested. I do not have the Zend Platform, I cannot
 *              make the test myself.
 */

class Hoa_Cache_Backend_ZendPlatform extends Hoa_Cache_Backend_Abstract {

    /**
     * Internal ZendPlatform prefix.
     *
     * @const string
     */
    const INTERNAL_ZP_PREFIX = 'internal_ZPtag:';



    /**
     * Validate that the Zend Platform is loaded and licensed.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( ) {

        if(!function_exists('accelerator_license_info'))
            throw new Hoa_Cache_Exception(
                'The Zend Platform extension must be loaded for using this backend.', 0);

        if(!function_exists('accelerator_get_configuration')) {

            $licenseInfos = accelerator_license_info();
            throw new Hoa_Cache_Exception(
                'The Zend Platform extension is not loaded correctly : %s.',
                1, $licenseInfos['failure_reason']);
        }

        $configurations = accelerator_get_configuration();

        if(@!$configurations['output_cache_licensed'])
            throw new Hoa_Cache_Exception(
                'The Zend Platform extension does not have the proper license ' .
                'to use content caching features.', 2);

        if(@!$configurations['output_cache_enabled'])
            throw new Hoa_Cache_Exception(
                'The Zend Platform content caching feature must be enabled for ' .
                'using this backend, set the ' .
                'zend_accelerator.output_cache_enabled directive to on.', 3);

        if(!is_writable($configuration['output_cache_dir']))
            throw new Hoa_Cache_Exception(
                'The cache copies directory %s must be writable.',
                4, $configuration['output_cache_dir']);
    }

    /**
     * Load a cache file.
     * The $unserialize argument is not used here.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content (not
     *                                  used here).
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->clean();

        $lifetime = $this->_frontendOptions['lifetime'];

        if(true === $exists)
            $lifetime = 0;

        $return   = output_cache_get($id_md5, $lifetime);

        if(isset($return[0]))
            return $return[0];
        else
            return false;
    }

    /**
     * Save cache content into a file.
     * Note : the core auto-serializes the $data, so the serialize_content
     * parameter does not have any importance.
     *
     * @access  public
     * @param   string  $id      Cache ID encoded in MD5.
     * @param   string  $data    Cache content.
     * @return  bool
     */
    public function save ( $id_md5, $data ) {

        $this->clean();

        return output_cache_put($id_md5, array($data, time()));
    }

    /**
     * Clean expired cache files.
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
              break;

            case Hoa_Cache::CLEANING_EXPIRED:
                $lifetime = $this->_frontendOptions['lifetime'];
              break;

            case Hoa_Cache::CLEANING_USER:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEANING_USER constant is not supported by ' .
                    'ZendPlatform cache backend.', 3);
              break;

            default:
                $lifetime = $lifetime;
        }

        try {

            $directory = ini_get('zend_accelerator.output_cache_dir') . DS .
                         '.php_cache_api';

            $cacheDir  = new Hoa_File_Finder(
                $directory,
                Hoa_File_Finder::LIST_FILE |
                Hoa_File_Finder::LIST_NO_DOT,
                Hoa_File_Finder::SORT_INAME
            );

            foreach($cacheDir as $i => $fileinfo)
                if($fileinfo->getMTime() + $lifetime <= time())
                    $fileinfo->delete();
        }
        catch ( Hoa_File_Exception $e ) {

            throw new Hoa_Cache_Exception(
                $e->getFormattedMessage(),
                $e->getCode()
            );
        }

        return $delete;
    }

    /**
     * Remove a cache file.
     *
     * @access  public
     * @param   string  $id_md5    ID of cache to remove (encoded in MD5).
     * @return  mixed
     */
    public function remove ( $id_md5 ) {

        return output_cache_remove_key($id_md5);
    }
}
