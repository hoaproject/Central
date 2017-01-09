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
 * Class \Hoa\Cache\Backend\ZendPlatform.
 *
 * ZendPlatform backend manager (yes yes, Zend :-)).
 * Inspiration from Zend\Cache\Backend\ZendPlatform class for making this class.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 * @todo       Need to be tested. I do not have the Zend Platform, I cannot
 *             make the test myself.
 */
class ZendPlatform extends Backend
{
    /**
     * Internal ZendPlatform prefix.
     *
     * @const string
     */
    const INTERNAL_ZP_PREFIX = 'internal\ZPtag:';



    /**
     * Validate that the Zend Platform is loaded and licensed.
     *
     * @param   array  $parameters    Parameters.
     * @throws  \Hoa\Cache\Exception
     */
    public function __construct(array $parameters = [])
    {
        if (!function_exists('accelerator_license_info')) {
            throw new Cache\Exception(
                'The Zend Platform extension must be loaded to use this backend.',
                0
            );
        }

        if (!function_exists('accelerator_get_configuration')) {
            $licenseInfos = accelerator_license_info();
            throw new Cache\Exception(
                'The Zend Platform extension is not loaded correctly: %s.',
                1,
                $licenseInfos['failure_reason']
            );
        }

        $configurations = accelerator_get_configuration();

        if (@!$configurations['output_cache_licensed']) {
            throw new Cache\Exception(
                'The Zend Platform extension does not have the proper license ' .
                'to use content caching features.',
                2
            );
        }

        if (@!$configurations['output_cache_enabled']) {
            throw new Cache\Exception(
                'The Zend Platform content caching feature must be enabled to ' .
                'use this backend, set the ' .
                'zend_accelerator.output_cache_enabled directive to on.',
                3
            );
        }

        if (!is_writable($configuration['output_cache_dir'])) {
            throw new Cache\Exception(
                'The cache copies directory %s must be writable.',
                4,
                $configuration['output_cache_dir']
            );
        }

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content into the ZendPlatform storage.
     * Data is already serialized.
     *
     * @param   string  $data    Data to store.
     * @return  void
     */
    public function store($data)
    {
        $this->clean();

        output_cache_put(
            $this->getIdMd5(),
            [$data, time()]
        );

        return;
    }

    /**
     * Load a cache file.
     *
     * @return  mixed
     */
    public function load()
    {
        $this->clean();

        $content = output_cache_get(
            $this->getIdMd5(),
            $this->_parameters->getParameter('lifetime')
        );

        if (isset($return[0])) {
            return $return[0];
        }

        return false;
    }

    /**
     * Clean expired cache files.
     * Note : \Hoa\Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @param   string  $lifetime    Lifetime of caches.
     * @return  void
     * @throws  \Hoa\Cache\Exception
     */
    public function clean($lifetime = Cache::CLEAN_EXPIRED)
    {
        switch ($lifetime) {
            case Cache::CLEAN_ALL:
                break;

            case Cache::CLEAN_EXPIRED:
                $lifetime = $this->_parameters->getParameter('lifetime');

                break;

            case Cache::CLEAN_USER:
                throw new \Hoa\Cache\Exception(
                    '\Hoa\Cache::CLEAN_USER constant is not supported by ' .
                    'ZendPlatform cache backend.',
                    3
                );

                break;

            default:
                $lifetime = $lifetime;
        }

        $directory =
            ini_get('zend_accelerator.output_cache_dir') . DS .
           '.php_cache_api';

        try {
            $finder = new HoaFile\Finder();
            $finder
                ->in($directory)
                ->files()
                ->modified('since ' . $lifetime . ' seconds');

            foreach ($finder as $file) {
                $file->open()->delete();
                $file->close();
            }
        } catch (File\Exception\FileDoesNotExist $e) {
        }

        return;
    }

    /**
     * Remove a cache file.
     *
     * @return  void
     */
    public function remove()
    {
        output_cache_remove_key($this->getIdMd5());

        return;
    }
}
