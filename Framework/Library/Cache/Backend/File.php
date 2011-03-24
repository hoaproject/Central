<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * Class \Hoa\Cache\Backend\File.
 *
 * File backend manager.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class File extends Backend {

    /**
     * Save cache content into a file.
     *
     * @access  public
     * @param   mixed  $data    Data to store.
     * @return  void
     */
    public function store ( $data ) {

        $this->clean();

        if(true === $this->getParameter('serialize_content'))
            $data = serialize($data);

        if(true === $this->getParameter('file.compress.active'))
            $data = gzcompress(
                $data,
                $this->getParameter('file.compress.level')
            );

        $this->setId($this->getIdMd5());
        $directory = $this->getFormattedParameter('file.cache.directory');

        @mkdir($directory, 0755, true);

        file_put_contents(
            $directory . $this->getFormattedParameter('file.cache.file'),
            $data
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
        $this->setId($this->getIdMd5());

        $filename = $this->getFormattedParameter('file.cache.directory') .
                    $this->getFormattedParameter('file.cache.file');

        if(false === file_exists($filename))
            return false;

        $content = file_get_contents($filename);

        if(true === $this->getParameter('file.compress.active'))
            $content = gzuncompress($content);

        if(true === $this->getParameter('serialize_content'))
            $content = unserialize($content);

        return $content;
    }

    /**
     * Clean expired cache files.
     * Note : \Hoa\Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @access  public
     * @param   int  $lifetime    Lifetime of caches.
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
                    '\Hoa\Cache::CLEAN_USER constant is not supported by %s.' .
                    2, __CLASS__);
              break;

            default:
                $lifetime = $lifetime;
        }

        $this->setId($this->getIdMd5());
        $time = time();

        try {

            $cacheDir  = new \Hoa\File\Finder(
                $this->getFormattedParameter('file.cache.directory'),
                \Hoa\File\Finder::LIST_FILE |
                \Hoa\File\Finder::LIST_NO_DOT,
                \Hoa\File\Finder::SORT_INAME
            );

            foreach($cacheDir as $i => $fileinfo)
                if($fileinfo->getMTime() + $lifetime <= $time)
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

        $this->setId($this->getIdMd5());

        $filename = $this->getFormattedParameter('file.cache.directory') .
                    $this->getFormattedParameter('file.cache.file');

        $file = new \Hoa\File\Read($filename);
        $file->delete();
        $file->close();

        return;
    }
}

}
