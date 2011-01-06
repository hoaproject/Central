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
 * @subpackage  Hoa_Cache_Backend_File
 *
 */

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
 * Class Hoa_Cache_Backend_File.
 *
 * File backend manager.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_File
 */

class Hoa_Cache_Backend_File extends Hoa_Cache_Backend {

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
     * Note : Hoa_Cache::CLEAN_USER is not supported, it's reserved for APC
     * backend.
     *
     * @access  public
     * @param   int  $lifetime    Lifetime of caches.
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
                    'Hoa_Cache::CLEAN_USER constant is not supported by %s.' .
                    2, __CLASS__);
              break;

            default:
                $lifetime = $lifetime;
        }

        $this->setId($this->getIdMd5());
        $time = time();

        try {

            $cacheDir  = new Hoa_File_Finder(
                $this->getFormattedParameter('file.cache.directory'),
                Hoa_File_Finder::LIST_FILE |
                Hoa_File_Finder::LIST_NO_DOT,
                Hoa_File_Finder::SORT_INAME
            );

            foreach($cacheDir as $i => $fileinfo)
                if($fileinfo->getMTime() + $lifetime <= $time)
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

        $this->setId($this->getIdMd5());

        $filename = $this->getFormattedParameter('file.cache.directory') .
                    $this->getFormattedParameter('file.cache.file');

        $file = new Hoa_File_Read($filename);
        $file->delete();
        $file->close();

        return;
    }
}
