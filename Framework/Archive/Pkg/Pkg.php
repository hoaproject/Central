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
 * @package     Hoa_Archive_Pkg
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Archive_Pkg_Exception
 */
import('Archive.Pkg.Exception');

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
 * Class Hoa_Archive_Pkg.
 *
 * Create or "decompress" a package..
 *
 * @author       Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright    Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license      http://gnu.org/licenses/gpl.txt GNU GPL
 * @since        PHP 5
 * @version      0.2
 * @package      Hoa_Archive_Pkg
 * @depreciated
 */

class Hoa_Archive_Pkg {

    /**
     * File content.
     *
     * @var Hoa_Archive_Pkg array
     */
    protected $content = array(
        'name'  => '',
        'dirs'  => array(),
        'files' => array()
    );



    /**
     * mk
     * Make a .pkg.
     *
     * @access  public
     * @param   dir     string    Directory to package.
     * @param   name    string    name.pkg.
     * @return  string
     * @throw   Hoa_File_Exception
     * @throw   Hoa_Archive_Pkg_Exception
     */
    public function mk ( $dir = '', $name = '' ) {

        if(empty($dir))
            throw new Hoa_Archive_Pkg_Exception('Directory could not be empty.', 0);

        if(empty($name))
            throw new Hoa_Archive_Pkg_Exception('Name could not be empty.', 1);

        if(!is_dir($dir))
            throw new Hoa_Archive_Pkg_Exception('%s must be a directory.', 2, $dir);

        $this->content['name']   = $name;
        $this->content['dirs'][] = $dir;

        $this->dirs($dir);
        $this->files();

        $out = serialize($this->content);
        $gzp = gzopen($name . '.pkg.gz', 'w9');
        gzwrite($gzp, $out);
        gzclose($gzp);

        return $name . '.pkg.gz';
    }

    /**
     * view
     * View a pkg file content.
     *
     * @access  public
     * @param   file    string    Filename.
     * @return  string
     * @throw   Hoa_Archive_Pkg_Exception
     */
    public function view ( $file ) {

        if(!file_exists($file))
            throw new Hoa_Archive_Pkg_Exception('File %s does not exist.', 3, $file);

        return unserialize(implode('', gzfile($file)));
    }

    /**
     * unwind
     * Unwind a pkg file.
     *
     * @access  public
     * @param   file    string    Filename.
     * @return  string
     * @throw   Hoa_File_Exception
     * @throw   Hoa_Archive_Pkg_Exception
     */
    public function unwind ( $file = '' ) {

        if(!file_exists($file))
            throw new Hoa_Archive_Pkg_Exception('File %s does not exist.', 4, $file);

        $content = $this->view($file);
        $root    = substr($file, 0, -4) . DS;

        Hoa_File_Directory::create($root);

        foreach($content['dirs'] as $i => $dir)
            Hoa_File_Directory::create($root . $dir);

        foreach($content['files'] as $file => $data) {

            $f = new Hoa_file($root . $file, Hoa_File::MODE_TRUNCATE_WRITE);
            $f->writeAll(base64_decode($data));
            $f->close();
        }

        return $root;
    }

    /**
     * dirs
     * Find all directories paths.
     *
     * @access  protected
     * @param   dir     string    Start directory.
     * @return  void
     * @throw   Hoa_File_Exception
     */
    protected function dirs ( $dir ) {

        $scan = new Hoa_File_Finder(
            $dir,
            Hoa_File_Finder::LIST_ALL |
            Hoa_File_Finder::LIST_NO_DOT,
            Hoa_File_Finder::SORT_INAME
        );

        if(!empty($scan))
            foreach($scan as $i => $dirs) {

                $this->content['dirs'][] = $dir . $dirs['name'] . DS;
                $this->dirs($dir . $dirs['name'] . DS);
            }

        return;
    }

    /**
     * files
     * Find all files paths, and encode files contents.
     *
     * @access  protected
     * @return  void
     * @throw   Hoa_File_Exception
     */
    protected function files ( ) {

        foreach($this->content['dirs'] as $e => $dir) {

            $scan = new Hoa_File_Finder(
                $dir,
                Hoa_File_Finder::LIST_FILE,
                Hoa_File_Finder::SORT_INAME
            );

            foreach($scan as $i => $files) {
                $key   = $dir . $files['name'];
                $value = base64_encode(Hoa_File::readAll($key));
                $this->content['files'][$key] = $value;
                unset($key, $value);
            }
        }
    }
}
