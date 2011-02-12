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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Cache\Exception
 */
-> import('Cache.Exception')

/**
 * \Hoa\Cache\Backend
 */
-> import('Cache.Backend.~')

/**
 * \Hoa\File\Finder
 */
-> import('File.Finder');

}

namespace Hoa\Cache\Backend {

/**
 * Class \Hoa\Cache\Backend\ZendPlatform.
 *
 * ZendPlatform backend manager (yes yes, Zend :-)).
 * Inspiration from Zend\Cache\Backend\ZendPlatform class for making this class.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 * @todo       Need to be tested. I do not have the Zend Platform, I cannot
 *             make the test myself.
 */

class ZendPlatform extends Backend {

    /**
     * Internal ZendPlatform prefix.
     *
     * @const string
     */
    const INTERNAL_ZP_PREFIX = 'internal\ZPtag:';



    /**
     * Validate that the Zend Platform is loaded and licensed.
     *
     * @access  public
     * @param   array  $parameters    Parameters.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function __construct ( Array $parameters = array() ) {

        if(!function_exists('accelerator_license_info'))
            throw new \Hoa\Cache\Exception(
                'The Zend Platform extension must be loaded for using this backend.', 0);

        if(!function_exists('accelerator_get_configuration')) {

            $licenseInfos = accelerator_license_info();
            throw new \Hoa\Cache\Exception(
                'The Zend Platform extension is not loaded correctly : %s.',
                1, $licenseInfos['failure_reason']);
        }

        $configurations = accelerator_get_configuration();

        if(@!$configurations['output_cache_licensed'])
            throw new \Hoa\Cache\Exception(
                'The Zend Platform extension does not have the proper license ' .
                'to use content caching features.', 2);

        if(@!$configurations['output_cache_enabled'])
            throw new \Hoa\Cache\Exception(
                'The Zend Platform content caching feature must be enabled for ' .
                'using this backend, set the ' .
                'zend_accelerator.output_cache_enabled directive to on.', 3);

        if(!is_writable($configuration['output_cache_dir']))
            throw new \Hoa\Cache\Exception(
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
     * Note : \Hoa\Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @access  public
     * @param   string  $lifetime    Lifetime of caches.
     * @return  void
     * @throw   \Hoa\Cache\Exception
     */
    public function clean ( $lifetime = \Hoa\Cache::CLEAN_EXPIRED ) {

        switch($lifetime) {

            case \Hoa\Cache::CLEAN_ALL:
              break;

            case \Hoa\Cache::CLEAN_EXPIRED:
                $lifetime = $this->getParameter('lifetime');
              break;

            case \Hoa\Cache::CLEAN_USER:
                throw new \Hoa\Cache\Exception(
                    '\Hoa\Cache::CLEAN_USER constant is not supported by ' .
                    'ZendPlatform cache backend.', 3);
              break;

            default:
                $lifetime = $lifetime;
        }

        $directory = ini_get('zend_accelerator.output_cache_dir') . DS .
                     '.php_cache_api';

        try {

            $cacheDir = new \Hoa\File\Finder(
                $directory,
                \Hoa\File\Finder::LIST_FILE |
                \Hoa\File\Finder::LIST_NO_DOT,
                \Hoa\File\Finder::SORT_INAME
            );

            foreach($cacheDir as $i => $fileinfo)
                if($fileinfo->getMTime() + $lifetime <= time())
                    $fileinfo->delete();
        }
        catch ( \Hoa\File\Exception\FileDoesNotExist $e ) { }

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

}
