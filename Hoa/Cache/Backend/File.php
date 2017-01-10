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
 * Class \Hoa\Cache\Backend\File.
 *
 * File backend manager.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class File extends Backend
{
    /**
     * Save cache content into a file.
     *
     * @param   mixed  $data    Data to store.
     * @return  void
     */
    public function store($data)
    {
        $this->clean();

        if (true === $this->_parameters->getParameter('serialize_content')) {
            $data = serialize($data);
        }

        if (true === $this->_parameters->getParameter('file.compress.active')) {
            $data = gzcompress(
                $data,
                $this->_parameters->getParameter('file.compress.level')
            );
        }

        $this->setId($this->getIdMd5());
        $directory =
            $this->_parameters->getFormattedParameter('file.cache.directory');

        @mkdir($directory, 0755, true);

        file_put_contents(
            $directory .
            $this->_parameters->getFormattedParameter('file.cache.file'),
            $data
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
        $this->setId($this->getIdMd5());

        $filename =
            $this->_parameters->getFormattedParameter('file.cache.directory') .
            $this->_parameters->getFormattedParameter('file.cache.file');

        if (false === file_exists($filename)) {
            return false;
        }

        $content = file_get_contents($filename);

        if (true === $this->_parameters->getParameter('file.compress.active')) {
            $content = gzuncompress($content);
        }

        if (true === $this->_parameters->getParameter('serialize_content')) {
            $content = unserialize($content);
        }

        return $content;
    }

    /**
     * Clean expired cache files.
     * Note : \Hoa\Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @param   int  $lifetime    Lifetime of caches.
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
                throw new Cache\Exception(
                    '\Hoa\Cache::CLEAN_USER constant is not supported by %s.' .
                    2,
                    __CLASS__
                );

            default:
                $lifetime = $lifetime;
        }

        $this->setId($this->getIdMd5());

        try {
            $cacheDirectory = $this->_parameters->getFormattedParameter(
                'file.cache.directory'
            );
            $finder = new HoaFile\Finder();
            $finder
                ->in($cacheDirectory)
                ->files()
                ->modified('since ' . $lifetime . ' seconds');

            foreach ($finder as $file) {
                $file->open()->delete();
                $file->close();
            }
        } catch (HoaFile\Exception\FileDoesNotExist $e) {
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
        $this->setId($this->getIdMd5());

        $filename =
            $this->_parameters->getFormattedParameter('file.cache.directory') .
            $this->_parameters->getFormattedParameter('file.cache.file');

        $file = new HoaFile\Read($filename);
        $file->delete();
        $file->close();

        return;
    }
}
