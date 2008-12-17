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
 * @subpackage  Hoa_File_Util
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
 * Class Hoa_File_Util.
 *
 * Extend some functionnalities to Hoa_File.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_File
 * @subpackage  Hoa_File_Util
 */

class Hoa_File_Util extends Hoa_File {

    /**
     * Clean a string (path, extension etc.).
     *
     * @access  public
     * @param   string  $str    String to clean.
     * @param   string  $add    Additional character.
     * @return  string
     */
    public static function makeSafe ( $str = '', $add = '' ) {

        return preg_replace('#[\t\n\r\0\x0B' . preg_quote($add) . ']#', '', $str);
    }

    /**
     * Make a secured string.
     *
     * @access  public
     * @param   string  $str    String.
     * @return  string
     * @todo    Maybe, this method could be ameliorate, aye ?
     *          Very experimental.
     */
    public static function makeSecure ( $str = '' ) {

        return preg_replace('#[^a-z0-9_\-\.]#i', '', $str);
    }

    /**
     * Build a well-formed path with a specific directory separator.
     *
     * @access  public
     * @param   array   $path    Parts of path.
     * @param   string  $ds      Directory Separator.
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function buildPath ( Array $path = array(), $ds = DS ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 0);

        if(empty($ds))
            throw new Hoa_File_Exception('Directory Separator could not be empty.', 1);

        foreach($path as $i => $p) {
            $p        = trim($p);
            $path[$i] = self::makeSafe($p, '/\\' . $ds);
        }

        return implode($ds, $path);
    }

    /**
     * Skip root of a path.
     *
     * @access  public
     * @param   string  $path    Path.
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function skipRoot ( $path = '' ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 2);

        if(OS_WIN)
            $path = substr($path, strpos($path, ':') + 1);

        return ltrim($path, '/\\');
    }

    /**
     * Check if a path is absolute or not.
     *
     * @access  public
     * @param   string  $path    Path.
     * @return  bool
     * @throw   Hoa_File_Exception
     */
    public static function isAbsolute ( $path = '' ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 3);

        return (bool) preg_match('#^(([a-z]\:(\\\|\/))|(\/)|(~))#Si', $path);
    }

    /**
     * Get the real path.
     *
     * @access  public
     * @param   string  $path    Path.
     * @param   string  $ds      Directory Separator.
     * @param   bool    $end     Make a difference between file and directory
                                 (a slash at the end or not)
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function realPath ( $path = '', $ds = DS, $end = true ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 4);

        if(empty($ds))
            throw new Hoa_File_Exception('Directory Separator could not be empty.', 5);

        $drive = '';
        $cwd   = getcwd();
        $path  = preg_replace('#[\\\\/]#', $ds, $path);
        $end   = $end && substr($path, -1) == $ds ? substr($path, -1) : '';

        if(OS_WIN) {

            if(preg_match('#^(?:([a-z]\:)(.*))#i', $path, $m)) {

                $drive = $m[1];
                $path  = $m[2];
            }
            else {

                $drive = substr($cwd, 0, 2);

                if($path{0} != $ds)
                    $path = substr($cwd, 3) . $ds . $path;
            }
        }
        elseif($path{0} != $ds)
            $path = $cwd . $ds . $path;

        $dirStack = array();
        foreach(explode($ds, $path) as $dir) {

            if(strlen($dir) > 0 && $dir != '.') {

                if($dir == '..')
                    array_pop($dirStack);
                else
                    $dirStack[] = $dir;
            }
        }

        $path = implode($ds, $dirStack);

        return $drive . $ds . $path . $end;
    }

    /**
     * Get a relative path.
     *
     * @access  public
     * @param   string  $path    Path.
     * @param   string  $root    Root.
     * @param   string  $ds      Directory Separator.
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function relativePath ( $path = '', $root = '', $ds = DS ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 6);

        if(empty($root))
            throw new Hoa_File_Exception('Root could not be empty.', 7);

        if(empty($ds))
            throw new Hoa_File_Exception('Directory Separator could not be empty.', 8);

        $path = self::realPath($path, $ds, false);
        $root = self::realPath($root, $ds, false);
        $dirs = explode($ds, $path);
        $comp = explode($ds, $root);

        $i = 0;
        while(isset($dirs[$i]) && isset($comp[$i]) && $dirs[$i] == $comp[$i])
            unset($dirs[$i], $comp[$i++]);

        return str_repeat('..' . $ds, count($comp)) . implode($ds, $dirs);
    }

    /**
     * Overvaluation of a path.
     *
     * @access  public
     * @param   string  $path       Path.
     * @param   string  $subPath    Sub-path.
     * @return  bool
     * @throw   Hoa_File_Exception
     */
    public static function overvaluationOfPath ( $path = '', $block = '', $ds = DS ) {

        if(empty($path))
            throw new Hoa_File_Exception('Path could not be empty.', 9);

        if(empty($block))
            throw new Hoa_File_Exception('Sub-path could not be empty.', 10);

        $out = self::relativePath($path, $block, $ds);

        return $out{0} != '.';
    }

    /**
     * Remove file extension.
     *
     * @access  public
     * @param   string  $filename    Filename.
     * @param   bool    $basename    Apply basename() or not.
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function skipExt ( $filename = '', $basename = true ) {

        if(empty($filename))
            throw new Hoa_File_Exception('Filename could not be empty.', 11);

        $out = substr($filename, 0, strrpos($filename, '.'));
        $out = self::makeSafe($out);
        return $basename ? basename($out) : $out;
    }

    /**
     * Get file extension.
     *
     * @access  public
     * @param   string  $filename    Filename.
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function getExt ( $filename = '' ) {

        if(empty($filename))
            throw new Hoa_File_Exception('Filename could not be empty.', 14);

        return self::makeSafe(substr($filename, strrpos($filename, '.')+1));
    }

    /**
     * Transform a "short size" to an "octal size".
     * Example : "2M" to "2097152" octets.
     *
     * @access  public
     * @param   string  $size    Size.
     * @return  int
     * @throw   Hoa_File_Exception
     */
    public static function getSize ( $size = '' ) {

        if(empty($size))
            throw new Hoa_File_Exception('Size could not be empty.', 12);

        if(is_numeric($size))
            return $size;

        if(!is_numeric($size)) {

            $foo   = substr($size, -1);
            $size *= ($foo == 'K' ? 1 << 10 :
                       ($foo == 'M' ? 1 << 20 :
                         ($foo == 'G' ? 1 << 30 : 1)
                       )
                     );
        }
        else
            throw new Hoa_File_Exception('Size could be a string.', 13);

        return $size;
    }

    /**
     * Return the tempory directory.
     *
     * @access  public
     * @return  string
     */
    public static function tmpDir ( ) {

        if(OS_WIN) {
            if(isset($_ENV['TEMP']))
                return $_ENV['TEMP'];

            if(isset($_ENV['TMP']))
                return $_ENV['TMP']. DS;

            if(isset($_ENV['windir']))
                return $_ENV['windir']. DS . 'temp' . DS;

            if(isset($_ENV['SystemRoot']))
                return $_ENV['SystemRoot']. DS . 'temp' . DS;

            if(isset($_SERVER['TEMP']))
                return $_SERVER['TEMP'] . DS;

            if(isset($_SERVER['TMP']))
                return $_SERVER['TMP'] . DS;

            if(isset($_SERVER['windir']))
                return $_SERVER['windir'] . DS . 'temp' . DS;

            if(isset($_SERVER['SystemRoot']))
                return $_SERVER['SystemRoot'] . DS . 'temp' . DS;

            return DS . 'temp' . DS;
        }
        if(isset($_ENV['TMPDIR']))
            return $_ENV['TMPDIR'] . DS;

        if(isset($_SERVER['TMPDIR']))
            return $_SERVER['TMPDIR'] . DS;

        if(version_compare(phpversion(), '5.2.1', '>='))
            return sys_get_temp_dir();

        return DS . 'tmp' . DS;
    }

    /**
     * Return a tempory file.
     *
     * @access  public
     * @param   string  $dirname    Directory, if null, will search
     *                              automatically.
     * @param   string  $prefix     Prefix of tempory file.
     * @return  ressource
     */
    public static function tmpFile ( $dirname = null, $prefix = '' ) {

        if(null === $dirname)
            $dirname = self::tmpDir();

        return tempnam($dirname, $prefix);
    }

    /**
     * Get file permissions.
     * Result should be intrepreted like this :
     *     s : Socket ;
     *     l : symbolic Link ;
     *     - : regular ;
     *     b : Block special ;
     *     d : Directory ;
     *     c : Character special ;
     *     p : FIFO Pipe ;
     *     u : unknown.
     *
     * @access  public
     * @param   string  $filename    Filename.
     * @return  string
     * @throw   Hoa_File_Exception
     */
    public static function getPerms ( $filename = '' ) {

        if(empty($filename))
            throw new Hoa_File_Exception('Filename could not be empty.', 15);

        if(!file_exists($filename))
            throw new Hoa_File_Exception('File %s does not exist.', 16, $filename);

        $perms = fileperms($filename);

        if (($perms & 0xC000) == 0xC000)
            $out = 's';
        elseif (($perms & 0xA000) == 0xA000)
            $out = 'l';
        elseif (($perms & 0x8000) == 0x8000)
            $out = '-';
        elseif (($perms & 0x6000) == 0x6000)
            $out = 'b';
        elseif (($perms & 0x4000) == 0x4000)
            $out = 'd';
        elseif (($perms & 0x2000) == 0x2000)
            $out = 'c';
        elseif (($perms & 0x1000) == 0x1000)
            $out = 'p';
        else
            $out = 'u';

        // Owner
        $out .= (($perms & 0x0100) ? 'r' : '-');
        $out .= (($perms & 0x0080) ? 'w' : '-');
        $out .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $out .= (($perms & 0x0020) ? 'r' : '-');
        $out .= (($perms & 0x0010) ? 'w' : '-');
        $out .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

        // World
        $out .= (($perms & 0x0004) ? 'r' : '-');
        $out .= (($perms & 0x0002) ? 'w' : '-');
        $out .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));

        return $out;
    }
}
