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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Hoa_Cache_Backend
 */
import('Cache.Backend');

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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_ZendPlatform
 * @todo        Need to be tested. I do not have the Zend Platform, I cannot
 *              make the test myself.
 */

class Hoa_Cache_Backend_ZendPlatform extends Hoa_Cache_Backend {

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
     * @param   array  $parameters    Parameters.
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( Array $parameters = array() ) {

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

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content into the ZendPlatform storage.
     * Data is already serialized.
     *
     * @access  public
     * @param   string  $data    Data to store.
     * @return  void
     */
    public function store ( $data ) {

        $this->clean();

        output_cache_put(
            $this->getIdMd5(),
            array($data, time())
        );

        return;
    }

    /**
     * Load a cache file.
     *
     * @access  public
     * @return  mixed
     */
    public function load ( ) {

        $this->clean();

        $content = output_cache_get(
            $this->getIdMd5(),
            $this->getParameter('lifetime')
        );

        if(isset($return[0]))
            return $return[0];

        return false;
    }

    /**
     * Clean expired cache files.
     * Note : Hoa_Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @access  public
     * @param   string  $lifetime    Lifetime of caches.
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = Hoa_Cache::CLEAN_EXPIRED ) {

        switch($lifetime) {

            case Hoa_Cache::CLEAN_ALL:
              break;

            case Hoa_Cache::CLEAN_EXPIRED:
                $lifetime = $this->getParameter('lifetime');
              break;

            case Hoa_Cache::CLEAN_USER:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEAN_USER constant is not supported by ' .
                    'ZendPlatform cache backend.', 3);
              break;

            default:
                $lifetime = $lifetime;
        }

        $directory = ini_get('zend_accelerator.output_cache_dir') . DS .
                     '.php_cache_api';

        try {

            $cacheDir = new Hoa_File_Finder(
                $directory,
                Hoa_File_Finder::LIST_FILE |
                Hoa_File_Finder::LIST_NO_DOT,
                Hoa_File_Finder::SORT_INAME
            );

            foreach($cacheDir as $i => $fileinfo)
                if($fileinfo->getMTime() + $lifetime <= time())
                    $fileinfo->delete();
        }
        catch ( Hoa_File_Exception_FileDoesNotExist $e ) { }

        return;
    }

    /**
     * Remove a cache file.
     *
     * @access  public
     * @return  void
     */
    public function remove ( ) {

        output_cache_remove_key($this->getIdMd5());

        return;
    }
}
