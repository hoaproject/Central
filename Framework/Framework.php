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
 * Some usefull constants.
 */
!defined('DS')     and define('DS'    , DIRECTORY_SEPARATOR);
!defined('PS')     and define('PS'    , PATH_SEPARATOR);
!defined('CRLF')   and define('CRLF'  , "\r\n");
!defined('OS_WIN') and define('OS_WIN', !strncasecmp(PHP_OS, 'win', 3));

/**
 * Check if Hoa was well-included.
 */
!(
    !defined('HOA_FRAMEWORK') and define('HOA_FRAMEWORK', true)
)
and
    exit('The Hoa framework main file (Framework.php) must be included once.');

/**
 * Some other framework constants.
 */
define('HOA_BASE',                     dirname(dirname(__FILE__)));
define('HOA_FRAMEWORK_BASE',           dirname(__FILE__));
define('HOA_DATA_BASE',                HOA_BASE . DS . 'Data');
define('HOA_DATA_BIN',                 HOA_DATA_BASE . DS . 'Bin');
define('HOA_DATA_CONFIGURATION',       HOA_DATA_BASE . DS . 'Configuration');
define('HOA_DATA_CONFIGURATION_CACHE', HOA_DATA_CONFIGURATION . DS . 'Cache');
define('HOA_DATA_ETC',                 HOA_DATA_BASE . DS . 'Etc');
define('HOA_DATA_LOSTFOUND',           HOA_DATA_BASE . DS . 'Lost+found');
define('HOA_DATA_TEMPLATE',            HOA_DATA_BASE . DS . 'Template');

/**
 * Class Hoa_Framework.
 *
 * Hoa_Framework is the framework package manager.
 * Each package must include Hoa_Framework, because it is the “taproot” of the
 * framework.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.2
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
    private static   $importStack            = array();

    /**
     * Stack of all registered shutdown function.
     *
     * @var Hoa_Framework array
     */
    private static   $rsdf                   = array();

    /**
     * Current linearized configuration.
     *
     * @var Hoa_Framework array
     */
    protected static $linearizedConfiguration = array();




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
     * @return  void
     * @throw   Hoa_Exception
     */
    public static function import ( $path = null ) {

        static $back  = HOA_FRAMEWORK_BASE;
        static $last  = null;

        preg_match('#(?:(.*?)(?<!\\\)\.)|(.*)#', $path, $matches);

        $handle = !isset($matches[2]) ? $matches[1] : $matches[2];

        switch($handle) {

            case '~':

                $back .= DS . $last;
                $path  = substr($path, 2);

                if(   (is_dir($back)           && !empty($path))
                   || (is_file($back . '.php') &&  empty($path)))
                    import($path);
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
                                import(substr($path, strlen($handle) + 1));

                            elseif(is_file($found . '.php')) {

                                $back = $found . '\.php';
                                import(null);
                            }

                            $back  = $tmp;
                        }
                    }
                    else {

                        $back .= DS . $handle;
                        import(null);
                    }
                }
                else {

                    $last  = null;
                    $final = str_replace('\\.', '.', $back);

                    if(!file_exists($final))
                        $final .= '.php';

                    if(!file_exists($final))
                        throw new Hoa_Exception(
                            'File %s is not found.', 0, $final);

                    if(false === OS_WIN)
                        $inode = fileinode($final);
                    else
                        $inode = md5($final);

                    if(isset(self::$importStack[$inode]))
                        return;

                    self::$importStack[$inode] = array(
                        self::IMPORT_PATH => $final,
                        self::IMPORT_LOAD => false
                    );
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

            $classPath  = HOA_FRAMEWORK_BASE . DS .
                          str_replace('_', DS, $className) . '.php';
        }

        if(!file_exists($classPath))
            return;


        if(false === OS_WIN)
            $inode = fileinode($classPath);
        else
            $inode = md5($classPath);

        if(!isset(self::$importStack[$inode]))
            return;

        if(true === self::$importStack[$inode][self::IMPORT_LOAD])
            return;

        require self::$importStack[$inode][self::IMPORT_PATH];
        self::$importStack[$inode][self::IMPORT_LOAD] = true;

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

            $configuration = require($path);
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
     *         'x'     => y
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
            self::$linearizedConfiguration = array();

        foreach($configuration as $key => $value) {

            $pprev = null !== $prev ? $prev . '.' : null;

            if(is_array($value)) {

                $key  = str_replace('.', '\\.', $key);
                $out .= $pprev . self::linearizeConfiguration($value, $pprev . $key);
            }
            else {

                $out  = $pprev . $key . "\n";
                self::$linearizedConfiguration[
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
     * @access  protected
     * @return  array
     */
    protected static function getLinearizedConfiguration ( ) {

        return self::$linearizedConfiguration;
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
     * @param   string  $class      Class.
     * @param   string  $methode    Method.
     * @return  bool
     */
    public static function registerShutdownFunction ( $class = '', $method = '' ) {

        if(!isset(self::$rsdf[$class][$method])) {
            self::$rsdf[$class][$method] = true;
            return register_shutdown_function(array($class, $method));
        }

        return false;
    }
}


/**
 * Alias of Hoa_Framework::_define().
 *
 * @access  public
 * @param   string  $name      The name of the constant.
 * @param   string  $value     The value of the constant.
 * @param   bool    $case      True set the case-insentisitve.
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
 * @return  bool
 */
function import ( $fp ) {

    return Hoa_Framework::import($fp);
}


/**
 * Set the default autoload.
 */
spl_autoload_register(array('Hoa_Framework', 'autoload'));

/**
 * Catch uncaught exception.
 */
set_exception_handler(array('Hoa_Exception', 'handler'));
