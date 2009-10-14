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
 * @subpackage  Hoa_Cache_Backend_File
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
 * Hoa_File
 */
import('File.~');

/**
 * Hoa_File_Directory
 */
import('File.Directory');

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
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_File
 */

class Hoa_Cache_Backend_File extends Hoa_Cache_Backend_Abstract {

    /**
     * Load a cache file.
     *
     * @access  public
     * @param   string  $id_md5         ID encoded in MD5.
     * @param   bool    $unserialize    Enable unserializing of content.
     * @param   bool    $exists         Test if cache exists or not.
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function load ( $id_md5, $unserialize = true, $exists = false ) {

        $this->clean();

        $filePath = $this->getFilePath($id_md5);

        if(false === $file_exists = file_exists($filePath))
            return false;

        if(true  === $exists)
            return $file_exists;

        try {

            $file    = new Hoa_File($filePath, Hoa_File::MODE_READ);
            $content = $file->readAll();
        }
        catch ( Hoa_File_Exception $e ) {

            throw new Hoa_Cache_Exception(
                $e->getFormattedMessage(), $e->getCode()
            );
        }

        if($this->_backendOptions['compress']['active'] === true)
            $content = gzuncompress($content);

        if($unserialize !== false
           && $this->_frontendOptions['serialize_content'] !== false)
            $content = unserialize($content);


        return $content;
    }

    /**
     * Save cache content into a file.
     *
     * @access  public
     * @param   string  $id      Cache ID encoded in MD5.
     * @param   string  $data    Cache content.
     * @return  string
     * @throw   Hoa_Cache_Exception
     */
    public function save ( $id_md5, $data ) {

        $this->clean();

        if($this->_frontendOptions['serialize_content'] !== false)
            $data = serialize($data);

        if(!is_dir($this->_backendOptions['cache_directory']))
            throw new Hoa_Cache_Exception('%s directory is not found.',
                1, $this->_backendOptions['cache_directory']);

        $filePath = $this->getFilePath($id_md5);

        if($this->_backendOptions['compress']['active'] === true)
            $data = gzcompress($data, $this->_backendOptions['compress']['level']);

        try {

            $file = new Hoa_File($filePath, Hoa_File::MODE_TRUNCATE_WRITE);
            $out  = $file->writeAll($data);
        }
        catch ( Hoa_File_Exception $e ) {

            var_dump('plplpl');

            throw new Hoa_Cache_Exception(
                $e->getFormattedMessage(),
                $e->getCode()
            );
        }

        return $out;
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
                    'File cache backend.', 2);
              break;

            default:
                $lifetime = $lifetime;
        }

        $delete = false;

        try {

            $cacheDir  = new Hoa_File_Finder(
                $this->_backendOptions['cache_directory'],
                Hoa_File_Finder::LIST_FILE |
                Hoa_File_Finder::LIST_NO_DOT,
                Hoa_File_Finder::SORT_INAME
            );
            $fileStack = array();

            foreach($cacheDir as $i => $fileinfo)
                if($fileinfo->getMTime() + $lifetime <= time())
                    $fileStack[] = $fileinfo->__toString();

            foreach($fileStack as $foo => $bar) {

                $file    = new Hoa_File($bar); 
                $delete |= $file->delete();
            }
        }
        catch ( Hoa_File_Exception $e ) {

            throw new Hoa_Cache_Exception(
                $e->getFormattedMessage(),
                $e->getCode()
            );
        }

        return (bool) $delete;
    }

    /**
     * Remove a cache file.
     *
     * @access  public
     * @param   string  $id_md5    ID of cache to remove (encoded in MD5).
     * @return  mixed
     * @throw   Hoa_Cache_Exception
     */
    public function remove ( $id_md5 ) {

        try {

            $file = new Hoa_File($this->getFilePath($id_md5));
            $file->delete();
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
     * Get cache file extension.
     *
     * @access  protected
     * @param   string     $filename     Filename.
     * @return  string
     */
    protected function getFilePath ( $filename ) {

        return $this->_backendOptions['cache_directory'] .
               $filename .
               (true === $this->_backendOptions['compress']['active']
                    ? '.gz'
                    : '') .
               '.cache';
    }
}
