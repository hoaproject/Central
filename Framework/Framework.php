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
 * Some other framework constants.
 */
define('HOA_BASE',                     dirname(dirname(__FILE__)));
define('HOA_FRAMEWORK_BASE',           HOA_BASE . DS . 'Framework');
define('HOA_DATA_BASE',                HOA_BASE . DS . 'Data');
define('HOA_DATA_BIN',                 HOA_DATA_BASE . DS . 'Bin');
define('HOA_DATA_CONFIGURATION',       HOA_DATA_BASE . DS . 'Configuration');
define('HOA_DATA_CONFIGURATION_CACHE', HOA_DATA_CONFIGURATION . DS . 'Cache');
define('HOA_DATA_ETC',                 HOA_DATA_BASE . DS . 'Etc');
define('HOA_DATA_LOSTFOUND',           HOA_DATA_BASE . DS . 'Lost+found');
define('HOA_DATA_PRIVATE',             HOA_DATA_BASE . DS . 'Private');
define('HOA_DATA_PRIVATE_DATABASE',    HOA_DATA_PRIVATE . DS . 'Database');
define('HOA_DATA_PRIVATE_LOCAL',       HOA_DATA_PRIVATE . DS . 'Local');
define('HOA_DATA_PRIVATE_LOG',         HOA_DATA_PRIVATE . DS . 'Log');
define('HOA_DATA_PRIVATE_TEST',        HOA_DATA_PRIVATE . DS . 'Test');
define('HOA_DATA_TEMPLATE',            HOA_DATA_BASE . DS . 'Template');

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
    const IMPORT_PATH         = 0;

    /**
     * Import constant : load flag collection index.
     *
     * @const int
     */
    const IMPORT_LOAD         = 1;

    /**
     * Configuration constant : load configurations into a simple array.
     *
     * @const int
     */
    const CONFIGURATION_ARRAY = 0;

    /**
     * Configuration constant : load configurations into a dotted array, i.e. a
     * linearized array.
     *
     * @const int
     */
    const CONFIGURATION_DOT   = 1;

    /**
     * Configuration constant : load configurations into a simple and a dotted
     * array.
     *
     * @const int
     */
    const CONFIGURATION_MIXE  = 2;

    /**
     * Stack of all files that might be imported.
     *
     * @var Hoa_Framework array
     */
    private static $_importStack             = array();

    /**
     * Stack of all registered shutdown function.
     *
     * @var Hoa_Framework array
     */
    private static $_rsdf                    = array();

    /**
     * Current linearized configuration.
     *
     * @var Hoa_Framework array
     */
    private static $_linearizedConfiguration = array();

    /**
     * Tree of components, starts by the root.
     *
     * @var Hoa_Framework_Protocol_Root object
     */
    private static $_root                    = null;

    /**
     * Opened stream.
     *
     * @var Hoa_Framework resource
     */
    private $_stream                         = null;

    /**
     * Stream name (filename).
     *
     * @var Hoa_Framework string
     */
    private $_streamName                     = null;

    /**
     * Stream context (given by the streamWrapper class).
     *
     * @var Hoa_Framework resource
     */
    public $context                          = null;



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
    public function realPath ( $path ) {

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

        fclose($this->getStream());

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
     *                                    @param   string  &$openedPath    If
     *                                    the $path is opened successfully, and
     *                                    STREAM_USE_PATH is set in $options,
     *                                    $openedPath should be set to the full
     *                                    path of the file/resource that was
     *                                    actually opened.  @return  bool
     * @return  bool
     */
    public function stream_open ( $path, $mode, $options, &$openedPath ) {

        // context

        var_dump('Mode: ' . $mode);
        $p = $this->realPath($path);

        var_dump('real path: ' . $p);

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

        return closedir($this->getStream());
    }

    /**
     * Open directory handle.
     * This method is called in response to opendir().
     *
     * @access  public
     * @param   string  $path       Specifies the URL that was passed to opendir().
     * @param   int     $options    Whether or not to enforce safe_mode (0x04).
     * @return  bool
     */
    public function dir_opendir ( $path, $options ) {

        // context

        // resolve path and opendir.
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

        if(false === $p = self::getProtocol()->resolve($path))
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
        else
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

        static $back = HOA_FRAMEWORK_BASE;
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
                    $back  = HOA_FRAMEWORK_BASE;

                    if(!file_exists($final))
                        $final .= '.php';

                    if(!file_exists($final)) {

                        print_r(debug_backtrace());
                        throw new Hoa_Exception(
                            'File %s is not found.', 0, $final);
                    }

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
        $classPath = HOA_FRAMEWORK_BASE . DS .
                     str_replace('_', DS, $className) . '.php';

        // If it is an entry class.
        if(!file_exists($classPath)) {

            if(false !== strpos($className, '_'))
                $className .= substr($className, strrpos($className, '_')); 
            else
                $className .= '_' . $className;

            $classPath = HOA_FRAMEWORK_BASE . DS .
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
     * If Hoa is in standalone mode, the default package configuration is given
     * by the Data/Configuration/ files.
     * This method try to find the configuration cache, and return a well-formed
     * array.
     * The array can have three forms :
     *     * array, i.e. an associative array : key => value ;
     *     * dot,   i.e. a linearized array : key.subkey.subsubkey => value ;
     *     * mixe,  i.e. a mixe of array and dot form ; in this case, a list of
     *       parameters that should be transform from dot to array, should be
     *       given.
     * No error occured, only an empty array is given to $configuration. A
     * boolean is return : true if the configuration was succeed, else false.
     * This method must be used with many precautions. Hoa must run with or
     * without configuration files, and the user must already have the
     * possibility to overwrite the package configuration.
     *
     * @access  public
     * @param   string  $packageName      The package name, i.e. the cache
     *                                    filename to read.
     * @param   array   $configuration    Variable that will receive the
     *                                    configuration array.
     * @param   int     $type             Configuration type. Given by
     *                                    constants CONFIGURATION_*.
     * @param   array   $except           List of linearized keys that must be
     *                                    transform in array if the
     *                                    configuration type is mixe.
     * @return  bool
     */
    public static function configurePackage ( $packageName,
                                              &$configuration = null,
                                              $type           = self::CONFIGURATION_DOT,
                                              Array $except   = array() ) {

        $path = HOA_DATA_CONFIGURATION_CACHE . DS . $packageName . '.php';

        if(!file_exists($path)) {

            $configuration = array();
            return false;
        }

        if($type === self::CONFIGURATION_ARRAY) {

            $configuration = require $path;
            return true;
        }

        if($type === self::CONFIGURATION_DOT) {

            self::linearizeConfiguration(require $path);
            $configuration = self::getLinearizedConfiguration();
            return true;
        }

        if($type !== self::CONFIGURATION_MIXE) {

            $configuration = array();
            return false;
        }

        self::linearizeConfiguration(require $path);
        $configuration = self::getLinearizedConfiguration();

        foreach($except as $foo => $category) {

            foreach($configuration as $key => $value) {

                if(0 === preg_match('#^' . $category . '\.(.*)#', $key, $match))
                    continue;

                if(!isset($configuration[$category]))
                    $configuration[$category] = array();

                $configuration[$category] = array_merge_recursive(
                    $configuration[$category],
                    self::unlinearizeConfiguration($match[1], $value)
                );
                unset($configuration[$key]);
            }
        }

        return true;
    }

    /**
     * Linearize an array, i.e. transform :
     *     array(
     *         'a' => array(
     *             'b' => array(
     *                 'c' => 'd',
     *                 'e' => 'f'
     *             ),
     *             'g' => 'h'
     *         ),
     *         'x => 'y'
     *     )
     *  in
     *     array(
     *         'a.b.c' => 'd',
     *         'a.b.e' => 'f',
     *         'a.g'   => 'h',
     *         'x'     => 'y'
     *     )
     * The method returns a string, and not the array. To get the array, please,
     * see the self::getLinearizedConfiguration() method.
     *
     * @access  protected
     * @param   array      $configuration      The array to linearize.
     * @param   string     $prev               Only for recursion.
     * @return  string
     */
    protected static function linearizeConfiguration ( $configuration, $prev = null ) {

        $out = null;

        if(null === $prev)
            self::$_linearizedConfiguration = array();

        foreach($configuration as $key => $value) {

            $pprev = null !== $prev ? $prev . '.' : null;

            if(is_array($value)) {

                $key  = str_replace('.', '\\.', $key);
                $out .= $pprev . self::linearizeConfiguration($value, $pprev . $key);
            }
            else {

                $out  = $pprev . $key . "\n";
                self::$_linearizedConfiguration[
                    substr($out, strrpos(substr($out, -1), "\n"), -1)
                ] = $value;
            }
        }

        return $out;
    }

    /**
     * Return the last linearized array.
     * Please, see the self::linearizeConfiguration() method.
     *
     * @access  private
     * @return  array
     */
    private static function getLinearizedConfiguration ( ) {

        return self::$_linearizedConfiguration;
    }

    /**
     * Unlinearize an array (just a part of an array).
     * Please, see the self::linearizeConfiguration() method, it is the
     * inverse.
     *
     * @access  protected
     * @param   string     $key      The linearize key, e.g. : a.b.c.d.
     * @param   mixed      $value    The $key value.
     * @return  array
     */
    protected static function unlinearizeConfiguration ( $key, $value ) {

        $out     = array();
        $explode = preg_split('#((?<!\\\)\.)#', $key, -1, PREG_SPLIT_NO_EMPTY);
        $end     = count($explode) - 1;
        $i       = $end;

        while($i >= 0) {

            $explode[$i] = str_replace('\\.', '.', $explode[$i]);

            if($i != $end)
                $out = array($explode[$i] => $out);
            else
                $out = array($explode[$i] => $value);

            $i--;
        }

        return $out;
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
     * Component name.
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

        $pos  = strpos($path, '/');

        if($pos !== false)
            $next = substr($path, 0, $pos);
        else
            $next = $path;

        if(true === $this->componentExists($next))
            return $this->getComponent($next)->resolve(substr($path, $pos + 1));

        return $this->reach($path);
    }

    /**
     * Queue of the component. Must be overload in childs classes.
     *
     * @access  public
     * @param   string  $queue    Queue of the component (generally, a filename,
     *                            with probably a query).
     * @return  mixed
     */
    public function reach ( $queue ) {

        switch($queue) {

            case 'NEW':
                return date('YmdHis');
              break;
        }

        return false;
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
     * Component name.
     *
     * @var Hoa_Framework_Protocol_Application string
     */
    protected $_name = 'Application';
}

class Hoa_Framework_Protocol_Framework_Package extends Hoa_Framework_Protocol {

    /**
     * Component name.
     *
     * @var Hoa_Framework_Protocol_Framework_Package string
     */
    protected $_name = 'Package';
}

class Hoa_Framework_Protocol_Framework extends Hoa_Framework_Protocol {

    /**
     * Component name.
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
     * Component name.
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
