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
 * @package     Hoa_File
 * @subpackage  Hoa_File_Dir
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_File
 */
import('File.~');

/**
 * Class Hoa_File_Dir.
 *
 * Manage directory (scan, copy, delete etc.).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_File
 * @subpackage  Hoa_File_Dir
 */

class Hoa_File_Dir {

    /**
     * File list type.
     *
     * @const int
     */
    const LIST_FILE    = 1;
    const LIST_DIR     = 2;
    //const LIST_NODOT = LIST_FILE | LIST_DIR;
    const LIST_DOT     = 4;
    //const LIST_ALL   = LIST_FILE | LIST_DIR | LIST_DOT;

    /**
     * File sort type.
     *
     * @const int
     */
    const SORT_NONE    =  0;
    const SORT_REVERSE =  1;
    const SORT_NAME    =  2;
    const SORT_SIZE    =  4;
    const SORT_DATE    =  8;
    const SORT_RANDOM  = 16;

    /**
     * File callback type.
     *
     * @const string
     */
    const CALL_NAME    = 'name';
    const CALL_SIZE    = 'size';
    const CALL_MTIME   = 'mtime';

    /**
     * Overwrite or not.
     *
     * @const bool
     */
    const OVERWRITE         = true;
    const DONOT_OVERWRITE   = false;



    /**
     * Create directories and nested directories.
     *
     * @access  public
     * @param   string  $dirs      Directories.
     * @param   bool    $mkdir     Force to make directory if no exists.
     * @return  bool
     * @throw   Hoa_File_Exception
     */
    public static function create ( $dirs = '', $mkdir = true ) {

        if(empty($dirs))
            throw new Hoa_File_Exception('Directories could not be empty.', 0);

        $dirs = explode('/', $dirs);

        $dirBack = '';
        while(!empty($dirs)) {

            $dirBack .= array_shift($dirs) . '/';
            if(!is_dir($dirBack)) {

                if($mkdir) {

                    if(false === @mkdir($dirBack, 0777))
                        throw new Hoa_File_Exception(
                            'Could not make directory (%s).', 1, $dirBack);
                }
                else
                    throw new Hoa_File_Exception(
                        'Directory %s does not exist.', 2, $dirBack);
            }
        }

        return true;
    }

    /**
     * Scan a directory, sort the result, and apply a function by callback on
     * certain element.
     *
     * @access  public
     * @param   string  $path       Path to directory.
     * @param   int     $list       Type of element should be scanned.
     * @param   int     $sort       Type of sort to apply on scanned elements.
     * @param   string  $methch     Method to apply element by callback.
     * @param   string  $on         Element that should be call by the method.
     * @return  array
     * @throw   Hoa_File_Exception
     */
    public static function scan ( $path   = '',
                                  $list   = self::LIST_FILE,
                                  $sort   = self::SORT_NAME,
                                  $methcb = null,
                                  $on     = self::CALL_NAME ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 3);

        if(!is_dir($path))
            throw new Hoa_File_Exception('Path must be a directory.', 4);

        if(   $on != self::CALL_NAME
           && $on != self::CALL_SIZE
           && $on != self::CALL_MTIME)
            throw new Hoa_File_Exception(
                'The parameter $on must be equal to CALL_NAME, CALL_SIZE, ' .
                'or CALL_MTIME ; given %s.', 8, $on);

        $out = array();
        $dir = dir($path);

        while(false !== $entry = $dir->read()) {

            $isRef = $entry == '.' || $entry == '..';
            $isDir = $isRef || is_dir($path . DS . $entry);

            if($list & self::LIST_DOT || !$isRef) {

                if(   $list & self::LIST_FILE && !$isDir
                   || $list & self::LIST_DIR  &&  $isDir
                   || $list & self::LIST_DOT  &&  $isDir) {

                    $out[] = array(
                        'name'  => $entry,
                        'size'  => !$isDir ? filesize($path . DS . $entry) : null,
                        'mtime' => filemtime($path . DS . $entry)
                    );

                    end($out);
                    $foo = key($out);

                    if(is_callable($methcb))
                        $out[$foo][$on] = call_user_func($methcb, $out[$foo][$on]);
                }
            }
        }

        $dir->close();

        return self::sortScan($out, $sort);
    }

    /**
     * Sort the result of scan.
     *
     * @access  protected
     * @param   array      $files     List of files.
     * @param   int        $sort      Type of sort.
     * @return  array
     * @throw   Hoa_File_Exception
     */
    protected static function sortScan ( $files = array(), $sort = self::SORT_NONE ) {

        if($sort == self::SORT_NONE)
            return $files;

        if($sort == self::SORT_REVERSE)
            return array_reverse($files);

        if($sort & self::SORT_RANDOM) {

            shuffle($files);
            return $files;
        }

        $names = array();
        $sizes = array();
        $dates = array();
    
        if($sort & self::SORT_NAME)
            $r = &$names;
        elseif($sort & self::SORT_DATE)
            $r = &$dates;
        elseif($sort & self::SORT_SIZE)
            $r = &$sizes;
        else
            return asort($files, SORT_REGULAR);

        $sortFlags = array(
            self::SORT_NAME => SORT_STRING, 
            self::SORT_DATE => SORT_NUMERIC, 
            self::SORT_SIZE => SORT_NUMERIC,
        );

        foreach($files as $file) {

            $names[] = $file['name'];
            $sizes[] = $file['size'];
            $dates[] = $file['mtime'];
        }


        if($sort & self::SORT_REVERSE)
            if(!isset($sortFlags[$sort & ~1]))
                throw new Hoa_File_Exception(
                    'Constant sort combinaison is not supported.', 5);
            else
                arsort($r, $sortFlags[$sort & ~1]);
        else
            if(!isset($sortFlags[$sort]))
                throw new Hoa_File_Exception(
                    'Constant sort combinaison is not supported.', 6);
            else
                asort($r, $sortFlags[$sort]);

        $result = array();
        foreach ($r as $i => $f)
            $result[] = $files[$i];

        return $result;
    }

    /**
     * Make a copy of a directory.
     *
     * @access  public
     * @param   string  $source        Directory source.
     * @param   string  $dest          Directory destination.
     * @param   bool    $overwrite     Overwrite file or not.
     * @return  bool
     * @throw   Hoa_File_Exception
     */
    public static function copy ( $source    = '', $dest = '',
                                  $overwrite = self::DONOT_OVERWRITE ) {

        if(empty($source))
            throw new Hoa_File_Exception('Source could not be empty.', 5);

        if(empty($dest))
            throw new Hoa_File_Exception('Destination could not be empty.', 6);

        self::create($dest);
        $content = self::scan($source, self::LIST_FILE | self::LIST_DIR);

        print_r($content);

        foreach($content as $id => $element) {

            $element = $element['name'];

            if(is_dir($source . $element))
                self::copy($source . $element . DS, $dest . $element . DS, $overwrite);
            elseif(is_file($source . $element))
                Hoa_File::copy($source . $element, $dest . $element, $overwrite);
        }

        return true;
    }

    /**
     * Delete a directory.
     *
     * @access  public
     * @param   array   $dir       Path to directory.
     * @param   bool    $force     Force to delete content of directory or not.
     * @return  bool
     * @throw   Hoa_File_Exception
     */
    public static function delete ( $dir = '', $force = true ) {

        static $s = 0;

        if(empty($dir))
            throw new Hoa_File_Exception('Directory could not be empty.', 7);

        if(!is_dir($dir))
            throw new Hoa_File_Exception('%s is not a directory.', 8, $dir);

        $content = self::scan($dir, self::LIST_FILE | self::LIST_DIR);

        if($force && !empty($content)) {

            foreach($content as $id => $element) {

                $element = $dir . $element['name'];

                if(is_dir($element)) {

                    $s++;
                        self::delete($element . DS, $force);
                    $s--;

                    if(false === rmdir($element . DS))
                        throw new Hoa_File_Exception(
                            'Directory %s could not be removed.',
                                9, $element . DS);
                }
                elseif(is_file($element))
                    Hoa_File::delete($element);
            }
        }
        else
            throw new Hoa_File_Exception('Directory %s is not empty.', 10, $dir);

        if($s == 0) {

            if(false === rmdir($dir))
                throw new Hoa_File_Exception('Directory %s could not be removed.',
                    11, $dir);
            else
                return true;
        }
    }
}
