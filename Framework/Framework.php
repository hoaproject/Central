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
 * @package     Hoa_Framework
 *
 */

/**
 * Hoa_Exception
 */
require_once 'Exception.php';

/**
 * Some usefull constants, …
 */
!defined('DS')             and define('DS'    ,    DIRECTORY_SEPARATOR);
!defined('PS')             and define('PS'    ,    PATH_SEPARATOR);
!defined('CRLF')           and define('CRLF'  ,    "\r\n");
!defined('OS_WIN')         and define('OS_WIN',    !strncasecmp(PHP_OS, 'win', 3));
!defined('S_64_BITS')      and define('S_64_BITS', PHP_INT_SIZE == 8);
!defined('S_32_BITS')      and define('S_32_BITS', !S_64_BITS);
!defined('SUCCEED')        and define('SUCCEED',   true);
!defined('FAILED')         and define('FAILED',    false);
!defined('PHP_VERSION_ID') and $v = PHP_VERSION
                           and define('PHP_VERSION_ID',   $v{0} * 10000
                                                        + $v{2} * 100
                                                        + $v{4});
!defined('SUCCEED')        and define('SUCCEED',   true);
!defined('FAILED')         and define('FAILED',    false);

/**
 * … and type.
 */
!defined('void')           and define('void',      (unset) null);

/**
 * Check if Hoa was well-included.
 */
!(
    !defined('HOA')        and define('HOA', true)
)
and
    exit('The Hoa framework main file (Framework.php) must be included once.');

/**
 * Environment constants.
 */
!defined('HOA_FRAMEWORK')  and define('HOA_FRAMEWORK', dirname(__FILE__));
!defined('HOA_DATA')       and define('HOA_DATA',      dirname(dirname(__FILE__)) . DS . 'Data');

/**
 * Class Hoa_Framework.
 *
 * Hoa_Framework is the framework package manager.
 * Each package must include Hoa_Framework, because it is the “taproot” of the
 * framework.
 * And build the hoa:// protocol.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.3
 * @package     Hoa_Framework
 */

class Hoa_Framework {

    /**
     * Import constant : path collection index.
     *
     * @const int
     */
    const IMPORT_PATH = 0;

    /**
     * Import constant : load flag collection index.
     *
     * @const int
     */
    const IMPORT_LOAD = 1;

    /**
     * Stack of all files that might be imported.
     *
     * @var Hoa_Framework array
     */
    private static $_importStack = array();

    /**
     * Stack of all registered shutdown function.
     *
     * @var Hoa_Framework array
     */
    private static $_rsdf        = array();

    /**
     * Tree of components, starts by the root.
     *
     * @var Hoa_Framework_Protocol_Root object
     */
    private static $_root        = null;

    /**
     * Opened stream.
     *
     * @var Hoa_Framework resource
     */
    private $_stream             = null;

    /**
     * Stream name (filename).
     *
     * @var Hoa_Framework string
     */
    private $_streamName         = null;

    /**
     * Stream context (given by the streamWrapper class).
     *
     * @var Hoa_Framework resource
     */
    public $context              = null;



    /**
     * Check if a constant is already defined.
     * If the constant is defined, this method returns false.
     * Else this method declares the constant.
     *
     * @access  public
     * @param   string  $name     The name of the constant.
     * @param   string  $value    The value of the constant.
     * @param   bool    $case     True set the case-insensitive.
     * @return  bool
     */
    public static function _define ( $name = '', $value = '', $case = false) {

        if(!defined($name))
            return define($name, $value, $case);

        return false;
    }

    /**
     * Import a file or a directory.
     * This method find file, and write some informations (inode, path, and
     * already imported or not) into an “import register”. If a file is not in
     * this register, the autoload will return an error.
     * This method is dependent of include_path (ini_set).
     *
     * @access  public
     * @param   string  $path    Path.
     * @param   bool    $load    Load file when over.
     * @return  void
     * @throw   Hoa_Exception
     */
    public static function import ( $path = null, $load = false ) {

        static $back = HOA_FRAMEWORK;
        static $last = null;

        preg_match('#(?:(.*?)(?<!\\\)\.)|(.*)#', $path, $matches);

        $handle = !isset($matches[2]) ? $matches[1] : $matches[2];

        switch($handle) {

            case '~':

                $back .= DS . $last;
                $path  = substr($path, 2);

                if(   (is_dir($back)           && !empty($path))
                   || (is_file($back . '.php') &&  empty($path)))
                    self::import($path, $load);
              break;

            default:

                if(!empty($path)) {

                    $glob = glob($back . DS . $handle);

                    if(!empty($glob)) {

                        foreach($glob as $i => $found) {

                            $last  = str_replace('.', '\\.', $found);
                            $last  = substr($last, strrpos($last, DS) + 1);
                            $tmp   = $back;
                            $back .= DS . $last;
                            $foo   = substr($path, strlen($handle) + 1);

                            if(   (is_dir($found)  && !empty($foo))
                               || (is_file($found) &&  empty($foo)))
                                self::import(substr($path, strlen($handle) + 1), $load);

                            elseif(is_file($found . '.php')) {

                                $back = $found . '\.php';
                                self::import(null, $load);
                            }

                            $back = $tmp;
                        }
                    }
                    else {

                        $back .= DS . $handle;
                        self::import(null, $load);
                    }
                }
                else {

                    $last  = null;
                    $final = str_replace('\\.', '.', $back);
                    $back  = HOA_FRAMEWORK;

                    if(!file_exists($final))
                        $final .= '.php';

                    if(!file_exists($final))
                        throw new Hoa_Exception(
                            'File %s is not found.', 0, $final);

                    if(false === OS_WIN)
                        $inode = fileinode($final);
                    else
                        $inode = md5($final);

                    if(isset(self::$_importStack[$inode]))
                        return;

                    self::$_importStack[$inode] = array(
                        self::IMPORT_PATH => $final,
                        self::IMPORT_LOAD => $load
                    );

                    if(true === $load)
                        require $final;
                }
        }

        return;
    }

    /**
     * If file is imported (via self::import()), the autoload method will
     * load the file that contains the class $className.
     *
     * @access  public
     * @param   string  $className    Class name.
     * @return  void
     */
    public static function autoload ( $className ) {

        $hoa = substr($className, 0, 3);

        if($hoa != 'Hoa')
            return;

        // Skip “Hoa_”.
        $className = substr($className, 4);
        $classPath = HOA_FRAMEWORK . DS .
                     str_replace('_', DS, $className) . '.php';

        // If it is an entry class.
        if(!file_exists($classPath)) {

            if(false !== strpos($className, '_'))
                $className .= substr($className, strrpos($className, '_')); 
            else
                $className .= '_' . $className;

            $classPath = HOA_FRAMEWORK . DS .
                         str_replace('_', DS, $className) . '.php';
        }

        if(!file_exists($classPath))
            return;


        if(false === OS_WIN)
            $inode = fileinode($classPath);
        else
            $inode = md5($classPath);

        if(!isset(self::$_importStack[$inode]))
            return;

        if(true === self::$_importStack[$inode][self::IMPORT_LOAD])
            return;

        require self::$_importStack[$inode][self::IMPORT_PATH];
        self::$_importStack[$inode][self::IMPORT_LOAD] = true;

        return;
    }

    /**
     * Get protocol's root.
     *
     * @access  public
     * @return  Hoa_Framework_Protocol_Root
     */
    public static function getProtocol ( ) {

        if(null === self::$_root)
            self::$_root = new Hoa_Framework_Protocol_Root();

        return self::$_root;
    }

    /**
     * Get the real path of the given URL.
     * Could return false if the path cannot be reached.
     *
     * @access  public
     * @param   string  $path    Path (or URL).
     * @return  mixed
     */
    public static function realPath ( $path ) {

        return self::getProtocol()->resolve($path);
    }

    /**
     * Close a resource.
     * This method is called in response to fclose().
     * All resources that were locked, or allocated, by the wrapper should be
     * released.
     *
     * @access  public
     * @return  void
     */
    public function stream_close ( ) {

        if(true === @fclose($this->getStream())) {

            $this->_stream     = null;
            $this->_streamName = null;
        }

        return;
    }

    /**
     * Tests for end-of-file on a file pointer.
     * This method is called in response to feof().
     *
     * access   public
     * @return  bool
     */
    public function stream_eof ( ) {

        return feof($this->getStream());
    }

    /**
     * Flush the output.
     * This method is called in respond to fflush().
     * If we have cached data in our stream but not yet stored it into the
     * underlying storage, we should do so now.
     *
     * @access  public
     * @return  bool
     */
    public function stream_flush ( ) {

        return fflush($this->getStream());
    }

    /**
     * Advisory file locking.
     * This method is called in response to flock(), when file_put_contents()
     * (when flags contains LOCK_EX), stream_set_blocking() and when closing the
     * stream (LOCK_UN).
     *
     * @access  public
     * @param   int     $operation    Operation is one the following:
     *                                  * LOCK_SH to acquire a shared lock (reader) ;
     *                                  * LOCK_EX to acquire an exclusive lock (writer) ;
     *                                  * LOCK_UN to release a lock (shared or exclusive) ;
     *                                  * LOCK_NB if we don't want flock() to
     *                                    block while locking (not supported on
     *                                    Windows).
     * @return  bool
     */
    public function stream_lock ( $operation ) {

        return flock($this->getStream(), $operation);
    }

    /**
     * Open file or URL.
     * This method is called immediately after the wrapper is initialized (f.e.
     * by fopen() and file_get_contents()).
     *
     * @access  public
     * @param   string  $path           Specifies the URL that was passed to the
     *                                  original function.
     * @param   string  $mode           The mode used to open the file, as
     *                                  detailed for fopen().
     * @param   int     $options        Holds additional flags set by the
     *                                  streams API. It can hold one or more of
     *                                  the following values OR'd together:
     *                                    * STREAM_USE_PATH, if path is relative,
     *                                      search for the resource using the
     *                                      include_path;
     *                                    * STREAM_REPORT_ERRORS, if this is
     *                                    set, you are responsible for raising
     *                                    errors using trigger_error during
     *                                    opening the stream. If this is not
     *                                    set, you should not raise any errors.
     * @param   string  &$openedPath    If the $path is opened successfully, and
     *                                  STREAM_USE_PATH is set in $options,
     *                                  $openedPath should be set to the full
     *                                  path of the file/resource that was
     *                                  actually opened.
     * @return  bool
     */
    public function stream_open ( $path, $mode, $options, &$openedPath ) {

        $p = self::realPath($path);

        if(false === $p)
            return false;

        if(null === $this->context)
            $openedPath = fopen(
                $p,
                $mode,
                $options & STREAM_USE_PATH
            );
        else
            $openedPath = fopen(
                $p,
                $mode,
                $options & STREAM_USE_PATH,
                $this->context
            );

        $this->_stream     = $openedPath;
        $this->_streamName = $p;

        return true;
    }

    /**
     * Read from stream. 
     * This method is called in response to fread() and fgets().
     *
     * @access  public
     * @param   int     $count    How many bytes of data from the current
     *                            position should be returned.
     * @return  string
     */
    public function stream_read ( $count ) {

        return fread($this->getStream(), $count);
    }

    /**
     * Seek to specific location in a stream.
     * This method is called in response to fseek().
     * The read/write position of the stream should be updated according to the
     * $offset and $whence.
     *
     * @access  public
     * @param   int     $offset    The stream offset to seek to.
     * @param   int     $whence    Possible values:
     *                               * SEEK_SET to set position equal to $offset
     *                                 bytes ;
     *                               * SEEK_CUR to set position to current
     *                                 location plus $offsete ;
     *                               * SEEK_END to set position to end-of-file
     *                                 plus $offset.
     * @return  bool
     */
    public function stream_seek ( $offset, $whence = SEEK_SET ) {

        return fseek($this->getStream(), $offset, $whence);
    }

    /**
     * Retrieve information about a file resource.
     * This method is called in response to fstat().
     *
     * @access  public
     * @return  array
     */
    public function stream_stat ( ) {

        return fstat($this->getStream());
    }

    /**
     * Retrieve the current position of a stream.
     * This method is called in response to ftell().
     *
     * @access  public
     * @return  int
     */
    public function stream_tell ( ) {

        return ftell($this->getStream());
    }

    /**
     * Write to stream.
     * This method is called in response to fwrite().
     *
     * @access  public
     * @param   string  $data    Should be stored into the underlying stream.
     * @return  int
     */
    public function stream_write ( $data ) {

        return fwrite($this->getStream(), $data);
    }

    /**
     * Close directory handle.
     * This method is called in to closedir().
     * Any resources which were locked, or allocated, during opening and use of
     * the directory stream should be released.
     *
     * @access  public
     * @return  bool
     */
    public function dir_closedir ( ) {

        if(true === $handle = @closedir($this->getStream())) {

            $this->_stream     = null;
            $this->_streamName = null;
        }

        return $handle;
    }

    /**
     * Open directory handle.
     * This method is called in response to opendir().
     *
     * @access  public
     * @param   string  $path       Specifies the URL that was passed to opendir().
     * @param   int     $options    Whether or not to enforce safe_mode (0x04).
     *                              It is not used here.
     * @return  bool
     */
    public function dir_opendir ( $path, $options ) {

        $p      = self::realPath($path);
        $handle = null;

        if(null === $this->context)
            $handle = @opendir($p, $this->context);
        else
            $handle = @opendir($p);

        if(false === $handle)
            return false;

        $this->_stream     = $handle;
        $this->_streamName = $p;

        return true;
    }

    /**
     * Read entry from directory handle.
     * This method is called in response to readdir().
     *
     * @access  public
     * @return  mixed
     */
    public function dir_readdir ( ) {

        return readdir($this->getStream());
    }

    /**
     * Rewind directory handle.
     * This method is called in response to rewinddir().
     * Should reset the output generated by self::dir_readdir, i.e. the next
     * call to self::dir_readdir should return the first entry in the location
     * returned by self::dir_opendir.
     *
     * @access  public
     * @return  bool
     */
    public function dir_rewinddir ( ) {

        return rewinddir($this->getStream());
    }

    /**
     * Create a directory.
     * This method is called in response to mkdir().
     *
     * @access  public
     * @param   string  $path       Directory which should be created.
     * @param   int     $mode       The value passed to mkdir().
     * @param   int     $options    A bitwise mask of values.
     * @return  bool
     */
    public function mkdir ( $path, $mode, $options ) {

        if(null === $this->context)
            return mkdir(
                $path,
                $mode,
                $option === STREAM_MKDIR_RECURSIVE
            );
        else
            return mkdir(
                $path,
                $mode,
                $option === STREAM_MKDIR_RECURSIVE,
                $this->context
            );
    }

    /**
     * Rename a file or directory.
     * This method is called in response to rename().
     * Should attempt to rename $from to $to.
     *
     * @access  public
     * @param   string  $from    The URL to current file.
     * @param   string  $to      The URL which $from should be renamed to.
     * @return  bool
     */
    public function rename ( $from, $to ) {

        if(null === $this->context)
            return rename($from, $to);
        else
            return rename($from, $to, $this->context);
    }

    /**
     * Remove a directory.
     * This method is called in response to rmdir().
     *
     * @access  public
     * @param   string  $path       The directory URL which should be removed.
     * @param   int     $options    A bitwise mask of values. It is not used
     *                              here.
     * @return  bool
     */
    public function rmdir ( $path, $options ) {

        if(null === $this->context)
            return rmdir($path);
        else
            return rmdir($path, $this->context);
    }

    /**
     * Delete a file.
     * This method is called in response to unlink().
     *
     * @access  public
     * @param   string  $path    The file URL which should be deleted.
     * @return  bool
     */
    public function unlink ( $path ) {

        if(null === $this->context)
            return unlink($path);
        else
            return unlink($path, $this->context);
    }

    /**
     * Retrieve information about a file.
     * This method is called in response to all stat() related functions.
     *
     * @access  public
     * @param   string  $path     The file URL which should be retrieve
     *                            information about.
     * @param   int     $flags    Holds additional flags set by the streams API.
     *                            It can hold one or more of the following
     *                            values OR'd together.
     *                            STREAM_URL_STAT_LINK: for resource with the
     *                            ability to link to other resource (such as an
     *                            HTTP location: forward, or a filesystem
     *                            symlink). This flag specified that only
     *                            information about the link itself should be
     *                            returned, not the resource pointed to by the
     *                            link. This flag is set in response to calls to
     *                            lstat(), is_link(), or filetype().
     *                            STREAM_URL_STAT_QUIET: if this flag is set,
     *                            our wrapper should not raise any errors. If
     *                            this flag is not set, we are responsible for
     *                            reporting errors using the trigger_error()
     *                            function during stating of the path.
     * @return  array
     */
    public function url_stat ( $path, $flags ) {

        if(false === $p = self::realPath($path))
            if($flags & STREAM_URL_STAT_QUIET)
                return array(); // Not sure…
            else
                return trigger_error(
                    'Path ' . $path . ' cannot be resolved.',
                    E_WARNING
                );

        if($flags & STREAM_URL_STAT_LINK)
            return lstat($p);
        else
            return stat($p);
    }

    /**
     * Get stream resource.
     *
     * @access  protected
     * @return  resource
     */
    protected function getStream ( ) {

        return $this->_stream;
    }

    /**
     * Get stream name.
     *
     * @access  protected
     * @return  resource
     */
    protected function getStreamName ( ) {

        return $this->_streamName;
    }

    /**
     * Apply and save a register shutdown function.
     * It may be analogous to a static __destruct, but it allows us to make more
     * that a __destruct method.
     *
     * @access  public
     * @param   string  $class     Class.
     * @param   string  $method    Method.
     * @return  bool
     */
    public static function registerShutdownFunction ( $class = '', $method = '' ) {

        if(!isset(self::$_rsdf[$class][$method])) {

            self::$_rsdf[$class][$method] = true;
            return register_shutdown_function(array($class, $method));
        }

        return false;
    }
}

/**
 * Interface Hoa_Framework_Parameterizable.
 *
 * Interface for all classes or packages that are parameterizable.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Parameterizable
 */

interface Hoa_Framework_Parameterizable {

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array     $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in );

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( );

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string    $key      Key.
     * @param   mixed     $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value );

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string    $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key );

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key );
}

/**
 * Class Hoa_Framework_Parameter.
 *
 * The parameter object, contains a set of parameter. It can be shared with
 * other class with permissions (read, write or both).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Protocol
 */

class Hoa_Framework_Parameter {

    /**
     * Permission to read.
     *
     * @const int
     */
    const PERMISSION_READ  = 1;

    /**
     * Permission to write.
     *
     * @const int
     */
    const PERMISSION_WRITE = 2;

    /**
     * Collection of package's parameters.
     *
     * @var Hoa_Framework_Parameter array
     */
    private $_parameters = array();

    /**
     * Collection of package's keywords.
     *
     * @var Hoa_Framework_Parameter array
     */
    private $_keywords   = array();

    /**
     * Parameters' owner.
     *
     * @var Hoa_Framework_Parameter string
     */
    private $_owner      = null;

    /**
     * Owner's friends with associated permissions.
     *
     * @var Hoa_Framework_Parameter array
     */
    private $_friends    = array();



    /**
     * Construct a new set of parameters.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $owner         Owner.
     * @param   array                          $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Hoa_Framework_Parameterizable $owner,
                                  Array $parameters = array() ) {

        $this->_owner = get_class($owner);

        if(!empty($parameters))
            $this->setDefaultParameters($owner, $parameters);

        return;
    }

    /**
     * Set default parameters to a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id            Owner or friends.
     * @param   array                          $parameters    Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setDefaultParameters ( Hoa_Framework_Parameterizable $id,
                                           Array $parameters ) {

        $this->check($id, self::PERMISSION_WRITE);

        $this->_parameters = $parameters;

        // Before assigning, check if a file does not exist. It has a higher
        // priority.

        return;
    }

    /**
     * Get default parameters from a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable   $id    Owner or friends.
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getDefaultParameters ( Hoa_Framework_Parameterizable $id ) {

        return $this->getParameters($id);
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id      Owner or friends.
     * @param   array                          $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Hoa_Framework_Parameterizable $id,
                                    Array $in ) {

        foreach($in as $key => $value)
            $this->setParameter($id, $key, $value);

        return;
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id      Owner or friends.
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( Hoa_Framework_Parameterizable $id ) {

        $this->check($id, self::PERMISSION_READ);

        return $this->_parameters;
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id       Owner or friends.
     * @param   string                         $key      Key.
     * @param   mixed                          $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( Hoa_Framework_Parameterizable $id,
                                   $key, $value ) {

        $this->check($id, self::PERMISSION_WRITE);

        $old = null;

        if(true === array_key_exists($key, $this->_parameters))
            $old = $this->_parameters[$key];

        $this->_parameters[$key] = $value;

        return $old;
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id       Owner or friends.
     * @param   string                         $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( Hoa_Framework_Parameterizable $id, $key ) {

        $parameters = $this->getParameters($id);

        if(array_key_exists($key, $parameters))
            return $parameters[$key];

        return null;
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id       Owner or friends.
     * @param   string                         $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( Hoa_Framework_Parameterizable $id,
                                            $key ) {

        $parameter = $this->getParameter($id, $key);

        if(null === $parameter)
            return null;

        return self::zFormat(
            $parameter,
            $this->getKeywords($id),
            $this->getParameters($id)
        );
    }

    /**
     * Set many keywords to a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id    Owner or friends.
     * @param   array                          $in    Keywords to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setKeywords ( Hoa_Framework_Parameterizable $id,
                                  Array $in = array() ) {

        foreach($in as $key => $value)
            $this->setKeyword($id, $key, $value);

        return;
    }

    /**
     * Get many keywords from a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id    Owner or friends.
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getKeywords ( Hoa_Framework_Parameterizable $id ) {

        $this->check(id, self::PERMISSION_READ);

        return $this->_keywords;
    }

    /**
     * Set a keyword to a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id       Owner or friends.
     * @param   string                         $key      Key.
     * @param   mixed                          $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public static function setKeyword ( Hoa_Framework_Parameterizable $id,
                                        $key, $word ) {

        $this->check($id, self::PERMISSION_WRITE);

        $old = null;

        if(true === array_key_exists($key, $this->_keywords))
            $old = $this->_keywords[$key];

        $this->_keywords[$key] = $value;

        return $old;
    }

    /**
     * Get a keyword from a class.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id         Owner or friends.
     * @param   string                         $keyword    Keyword.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public static function getKeyword ( Hoa_Framework_Parameterizable $id,
                                        $keyword ) {

        $keywords = $this->getKeywords($id);

        if(true === array_key_exists($id, $keywords))
            return $keywords[$id][$key];

        return null;
    }

    /**
     * ZFormat a string.
     * ZFormat is inspired from the famous Zsh (please, take a look at
     * http://zsh.org), and specifically from ZStyle.
     *
     * ZFormat has the following pattern:
     *     (:subject[:format]:)
     *
     * where subject could be a:
     *   * keyword, i.e. a simple string: foo;
     *   * reference to an existing parameter, i.e. a simple string prefixed by
     *     a %: %bar;
     *   * constant, i.e. a combination of chars, first is prefixed by a _: _Ymd
     *     will given the current year, followed by the current month and
     *     finally the current day.
     *
     * and where the format is a combination of chars, that apply functions on
     * the subject:
     *   * h: to get the head of a path (equivalent to dirname);
     *   * t: to get the tail of a path (equivalent to basename);
     *   * r: to get the path without extension;
     *   * e: to get the extension;
     *   * l: to get the result in lowercase;
     *   * u: to get the result in uppercase;
     *   * U: to get the result with the first letter in uppercase;
     *   * s/<foo>/<bar>/: to replace all matches <foo> by <bar> (the last / is
     *     optional, only if more options are given after);
     *   * s%<foo>%<bar>%: to replace the prefix <foo> by <bar> (the last % is
     *     also optional);
     *   * s#<foo>#<bar>#: to replace the suffix <foo> by <bar> (the last # is
     *     also optional).
     *
     * Known constants are:
     *   * d: day of the month, 2 digits with leading zeros;
     *   * j: day of the month without leading zeros;
     *   * N: ISO-8601 numeric representation of the day of the week;
     *   * w: numeric representation of the day of the week;
     *   * z: the day of the year (starting from 0);
     *   * W: ISO-8601 week number of year, weeks starting on Monday;
     *   * m: numeric representation of a month, with leading zeros;
     *   * n: numeric representation of a month, without leading zeros;
     *   * Y: a full numeric representation of a year, 4 digits;
     *   * y: a two digit representation of a year;
     *   * g: 12-hour format of an hour without leading zeros;
     *   * G: 24-hour format of an hour without leading zeros;
     *   * h: 12-hour format of an hour with leading zeros;
     *   * H: 24-hour format of an hour with leading zeros;
     *   * i: minutes with leading zeros;
     *   * s: seconds with leading zeros;
     *   * u: microseconds;
     *   * O: difference to Greenwich time (GMT) in hours;
     *   * T: timezone abbreviation;
     *   * U: seconds since the Unix Epoch (a timestamp).
     * There are very usefull for dynamic cache paths for example.
     *
     * Examples:
     *   Let keywords $k and parameters $p:
     *     $k = array(
     *         'foo'      => 'bar',
     *         'car'      => 'DeLoReAN',
     *         'power'    => 2.21,
     *         'answerTo' => 'life_universe_everything_else',
     *         'answerIs' => 42,
     *         'hello'    => 'wor.l.d'
     *     );
     *     $p = array(
     *         'plpl'        => '(:foo:U:)',
     *         'foo'         => 'ar(:%plpl:)',
     *         'favoriteCar' => 'A (:car:l:)!',
     *         'truth'       => 'To (:answerTo:ls/_/ /U:) is (:answerIs:).',
     *         'file'        => '/a/file/(:_Ymd:)/(:hello:trr:).(:power:e:)',
     *         'recursion'   => 'oof(:%foo:s#ar#az:)'
     *     );
     *   Then, after applying the zFormat, we get:
     *     * plpl:        'Bar', put the first letter in uppercase;
     *     * foo:         'arBar', call the parameter plpl;
     *     * favoriteCar: 'A delorean!', all is in lowercase;
     *     * truth:       'To Life universe everything else is 42', all is in
     *                    lowercase, then replace underscores by spaces, and
     *                    finally put the first letter in uppercase; and no
     *                    transformation for 42;
     *     * file:        '/a/file/20090505/wor.21', get date constants, then
     *                    get the tail of the path and remove extension twice,
     *                    and add the extension of power;
     *     * recursion:   'oofarbaz', get 'arbar' first, and then, replace the
     *                    suffix 'ar' by 'az'.
     *
     * @access  public
     * @param   string    $parameter     Parameter.
     * @param   array     $keywords      Keywords.
     * @param   array     $parameters    Parameters.
     * @return  string
     * @throw   Hoa_Exception
     *
     * @todo
     *   Add the cast. Maybe like this: (:subject:format[:cast]:) where cast
     * could be integer, float, array etc.
     */
    public static function zFormat ( $parameter,
                                     Array $keywords   = array(),
                                     Array $parameters = array() ) {

        preg_match_all(
            '#([^\(]+)?(?:\(:(.*?):\))?#',
            $parameter,
            $matches,
            PREG_SET_ORDER
        );
        array_pop($matches);

        $out = null;

        foreach($matches as $i => $match) {

            $out .= $match[1];

            if(!isset($match[2]))
                continue;

            preg_match(
                '#([^:]+)(?::(.*))?#',
                $match[2],
                $submatch
            );

            if(!isset($submatch[1]))
                continue;

            $key    = $submatch[1];
            $word   = substr($key, 1);
            $handle = null;

            // Call a parameter.
            if($key[0] == '%') {

                if(false === array_key_exists($word, $parameters))
                    throw new Hoa_Exception(
                        'Parameter %s is not found in the parameter rule %s.',
                        0, array($word, $parameter));

                $newParameters = $parameters;
                unset($newParameters[$word]);

                $handle = self::zFormat(
                    $parameters[$word],
                    $keywords,
                    $newParameters
                );

                unset($newParameters);
            }
            // Call a constant (only date constants for now).
            elseif($key[0] == '_') {

                preg_match_all(
                    '#(d|j|N|w|z|W|m|n|Y|y|g|G|h|H|i|s|u|O|T|U)#',
                    $word,
                    $constants
                );

                if(!isset($constants[1]))
                    throw new Hoa_Exception(
                        'An invalid constant char is found in the parameter ' .
                        'rule %s.', 1, $parameter);

                $handle = date(implode('', $constants[1]));
            }
            // Call a keyword.
            else {

                if(false === array_key_exists($key, $keywords))
                    throw new Hoa_Exception(
                        'Keyword %s is not found in the parameter rule %s.', 2,
                        array($key, $parameter));

                $handle = $keywords[$key];
            }

            if(!isset($submatch[2])) {

                $out .= $handle;
                continue;
            }

            preg_match_all(
                '#(h|t|r|e|l|u|U|s(/|%|\#)(.*?)(?<!\\\)\2(.*?)(?:(?<!\\\)\2|$))#',
                $submatch[2],
                $flags
            );

            if(empty($flags))
                continue;

            foreach($flags[1] as $i => $flag)
                switch($flag) {

                    case 'h':
                        $handle = dirname($handle);
                      break;

                    case 't':
                        $handle = basename($handle);
                      break;

                    case 'r':
                        if(false !== $position = strrpos($handle, '.', 1))
                            $handle = substr($handle, 0, $position);
                      break;

                    case 'e':
                        if(false !== $position = strrpos($handle, '.', 1))
                            $handle = substr($handle, $position + 1);
                      break;

                    case 'l':
                        $handle = strtolower($handle);
                      break;

                    case 'u':
                        $handle = strtoupper($handle);
                      break;

                    case 'U':
                        $handle = ucfirst($handle);
                      break;

                    default:
                        if(!isset($flags[3]) && !isset($flags[4]))
                            throw new Hoa_Exception(
                                'Unrecognized format pattern in the parameter %s.',
                                0, $parameter);

                        if(isset($flags[3][1]) && isset($flags[3][1])) {

                            $l = $flags[3][1];
                            $r = $flags[4][1];
                        }
                        else {

                            $l = $flags[3][0];
                            $r = $flags[4][0];
                        }

                        $l     = preg_quote($l, '#');

                        switch($flags[2][0]) {

                            case '%':
                                $l  = '^' . $l;
                              break;

                            case '#':
                                $l .= '$';
                              break;
                        }

                        $handle = preg_replace('#' . $l . '#', $r, $handle);
                }

            $out .= $handle;
        }

        return $out;
    }

    /**
     * Check if an object has permissions to read or write into this set of
     * parameters.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $id             Owner or friends.
     * @param   int                            $permissions    Permissions
     *                                                         (please, see the
     *                                                         self::PERMISSION_*
     *                                                         constants).
     * @return  bool
     * @throw   Hoa_Exception
     */
    public function check ( Hoa_Framework_Parameterizable $id, $permissions ) {

        $iid = get_class($id);

        if($this->_owner == $iid)
            return true;

        if(!array_key_exists($iid, $this->_friends))
            throw new Hoa_Exception(
                'Class %s is not friend of %s and cannot share its parameters.',
                0, array($iid, $this->_owner));

        $p = $this->_friends[$iid];

        if(0 == $permissions & $p)
            if(1 == $permissions & self::PERMISSION_READ)
                throw new Hoa_Exception(
                    'Class %s does not have permission to read parameters ' .
                    'from %s.', 1, array($iid, $this->_owner));
            else
                throw new Hoa_Exception(
                    'Class %s does not have permission to write parameters ' .
                    'from %s.', 2, array($iid, $this->_owner));

        return true;
    }

    /**
     * Share this set of parameters of another class.
     * Only owner can share its set of parameters with someone else; it is more
     * simple like this… (because of changing permissions cascade effect).
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $owner          Owner.
     * @param   Hoa_Framework_Parameterizable  $friend         Friend.
     * @param   int                            $permissions    Permissions
     *                                                         (please, see the
     *                                                         self::PERMISSION_*
     *                                                         constants).
     * @return  void
     * @throw   Hoa_Exception
     */
    public function shareWith ( Hoa_Framework_Parameterizable $owner,
                                Hoa_Framework_Parameterizable $friend,
                                $permissions ) {

        if($this->_owner != get_class($owner))
            throw new Hoa_Exception(
                'Only owner (here %s) can share its parameters; try with %s.',
                3, array($this->_owner, get_class($owner)));

        $this->_friends[get_class($friend)] = $permissions;

        return;
    }
}

/**
 * Class Hoa_Framework_Protocol.
 *
 * Abstract class for all hoa://'s components.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Protocol
 */

abstract class Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol string
     */
    protected $_name     = null;

    /**
     * Collections of sub-components.
     *
     * @var Hoa_Framework_Protocol array
     */
    private $_components = array();

    /**
     * Static indentation for the __toString() method.
     *
     * @var Hoa_Framework_Protocol int
     */
    private static $i    = 0;



    /**
     * Add a component.
     *
     * @access  public
     * @param   Hoa_Framework_Protocol  $component    Component to add.
     * @return  array
     */
    public function addComponent ( Hoa_Framework_Protocol $component ) {

        $this->_components[$component->getName()] = $component;

        return $this->_components;
    }

    /**
     * Get a specific component.
     *
     * @access  public
     * @param   string  $component    Component name.
     * @return  Hoa_Framework_Protocol
     * @throw   Hoa_Exception
     */
    public function getComponent ( $component ) {

        if(false === $this->componentExists($component))
            throw new Hoa_Exception(
                'Component %s does not exist.', 0, $component);

        return $this->_components[$component];
    }

    /**
     * Check if a component exists.
     *
     * @access  public
     * @param   string  $component    Component name.
     * @return  bool
     */
    public function componentExists ( $component ) {

        return array_key_exists($component, $this->_components);
    }

    /**
     * Resolve a path, i.e. iterate the components tree and reach the queue of
     * the path.
     *
     * @access  public
     * @param   string  $path    Path to resolve.
     * @return  string
     */
    public function resolve ( $path ) {

        if(substr($path, 0, 6) == 'hoa://')
            $path = substr($path, 6);

        $pos = strpos($path, '/');

        if($pos !== false)
            $next = substr($path, 0, $pos);
        else
            $next = $path;

        if(true === $this->componentExists($next))
            return $this->getComponent($next)->resolve(substr($path, $pos + 1));

        return $this->reach($path);
    }

    /**
     * Queue of the component. Should be overload in childs classes.
     *
     * @access  public
     * @param   string  $queue    Queue of the component (generally, a filename,
     *                            with probably a query).
     * @return  mixed
     */
    public function reach ( $queue ) {

        return $queue;
    }

    /**
     * Get component's name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Print a tree of component.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = null;

        foreach($this->_components as $foo => $component) {

            $out .= str_repeat('  ', self::$i) . $component->getName() . "\n";

            self::$i++;
            $out .= $component->__toString();
            self::$i--;
        }

        return $out;
    }
}

/**
 * Class Hoa_Framework_Protocol_*.
 *
 * hoa:// protocol's components.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Protocol
 * @subpackage  Hoa_Framework_Protocol_*
 */

class Hoa_Framework_Protocol_Application extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Application string
     */
    protected $_name = 'Application';
}

class Hoa_Framework_Protocol_Data_Configuration extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Data_Configuration string
     */
    protected $_name = 'Configuration';



    /**
     * Queue of the component.
     *
     * @access  public
     * @param   stirng  $queue    Queue of the component (generally, a filename,
     *                            with probably a query; here it is a path).
     * @return  mixed
     */
    public function reach ( $queue ) {

        return HOA_DATA . DS . 'Configuration' . DS . 'Cache' . DS . $queue;
    }
}

class Hoa_Framework_Protocol_Data extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Data string
     */
    protected $_name = 'Data';



    /**
     * Add components.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->addComponent(new Hoa_Framework_Protocol_Data_Configuration());

        return;
    }
}

class Hoa_Framework_Protocol_Framework_Package extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Framework_Package string
     */
    protected $_name = 'Package';
}

class Hoa_Framework_Protocol_Framework extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Framework string
     */
    protected $_name = 'Framework';



    /**
     * Add components.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->addComponent(new Hoa_Framework_Protocol_Framework_Package());

        return;
    }
}

class Hoa_Framework_Protocol_Root extends Hoa_Framework_Protocol {

    /**
     * Component's name.
     *
     * @var Hoa_Framework_Protocol_Root string
     */
    protected $_name = '/';



    /**
     * Add components.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->addComponent(new Hoa_Framework_Protocol_Application());
        $this->addComponent(new Hoa_Framework_Protocol_Data());
        $this->addComponent(new Hoa_Framework_Protocol_Framework());

        return;
    }
}


/**
 * Alias of Hoa_Framework::_define().
 *
 * @access  public
 * @param   string  $name     The name of the constant.
 * @param   string  $value    The value of the constant.
 * @param   bool    $case     True set the case-insentisitve.
 * @return  bool
 */
function _define ( $name = '', $value = '', $case = false ) {

    return Hoa_Framework::_define($name, $value, $case);
}

/**
 * Alias of Hoa_Framework::import().
 *
 * @access  public
 * @param   string  $path    Path.
 * @param   bool    $load    Load file when over.
 * @return  bool
 */
function import ( $path, $load = false ) {

    return Hoa_Framework::import($path, $load);
}


/**
 * Register the hoa:// protocol.
 */
stream_wrapper_register('hoa', 'Hoa_Framework', 0);

/**
 * Set the default autoload.
 */
spl_autoload_register(array('Hoa_Framework', 'autoload'));

/**
 * Catch uncaught exception.
 */
set_exception_handler(array('Hoa_Exception', 'handler'));
