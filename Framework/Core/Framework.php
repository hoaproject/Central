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
 * @package     Hoa_Framework
 *
 */

/**
 * Hoa_Exception
 */
require_once 'Exception.php';

/**
 * Hoa_Framework_Parameter
 */
require_once 'Parameter.php';

/**
 * Hoa_Framework_Protocol
 */
require_once 'Protocol.php';

/**
 * Some usefull constants.
 */
!defined('SUCCEED')        and define('SUCCEED',   true);
!defined('FAILED')         and define('FAILED',    false);
!defined('DS')             and define('DS'    ,    DIRECTORY_SEPARATOR);
!defined('PS')             and define('PS'    ,    PATH_SEPARATOR);
!defined('CRLF')           and define('CRLF'  ,    "\r\n");
!defined('OS_WIN')         and define('OS_WIN',    !strncasecmp(PHP_OS, 'win', 3));
!defined('S_64_BITS')      and define('S_64_BITS', PHP_INT_SIZE == 8);
!defined('S_32_BITS')      and define('S_32_BITS', !S_64_BITS);
!defined('PHP_VERSION_ID') and $v = PHP_VERSION
                           and define('PHP_VERSION_ID',   $v{0} * 10000
                                                        + $v{2} * 100
                                                        + $v{4});
!defined('void')           and define('void',      (unset) null);

/**
 * Hoa constants.
 */
!defined('HOA_VERSION_MAJOR')   and define('HOA_VERSION_MAJOR',   0);
!defined('HOA_VERSION_MINOR')   and define('HOA_VERSION_MINOR',   5);
!defined('HOA_VERSION_RELEASE') and define('HOA_VERSION_RELEASE', 5);
!defined('HOA_VERSION_STATUS')  and define('HOA_VERSION_STATUS',  'b');

/**
 * Check if Hoa was well-included.
 */
!(
    !defined('HOA') and define('HOA', true)
)
and
    exit('The Hoa framework main file (Framework.php) must be included once.');


/**
 * Class Hoa_Framework.
 *
 * Hoa_Framework is the framework package manager.
 * Each package must include Hoa_Framework, because it is the “taproot” of the
 * framework.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.5
 * @package     Hoa_Framework
 */

class Hoa_Framework implements Hoa_Framework_Parameterizable {

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
     * Parameters of Hoa_Framework.
     *
     * @var Hoa_Framework_Parameter object
     */
    protected $_parameters       = null;

    /**
     * Singleton.
     *
     * @var Hoa_Framework object
     */
    private static $_instance    = null;

    /**
     * Last imported file path.
     *
     * @var Hoa_Framework array
     */
    private static $_lastImport  = array();



    /**
     * Singleton.
     *
     * @access  public
     * @return  Hoa_Framework
     */
    public static function getInstance ( ) {

        if(null === self::$_instance)
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Initialize the framework.
     *
     * @access  public
     * @param   array   $parameters    Parameters of Hoa_Framework.
     * @return  Hoa_Framework
     */
    public function initialize ( Array $parameters = array() ) {

        $root              = dirname(dirname(__FILE__));
        $this->_parameters = new Hoa_Framework_Parameter(
            $this,
            array(
                'root.ofFrameworkDirectory' => $root
            ),
            array(
                'root'               => '(:root.ofFrameworkDirectory:)',
                'root.framework'     => '(:%root:)',
                'root.data'          => '(:%root:h:)' . DS . 'Data',
                'root.application'   => '(:%root:h:)' . DS . 'Application',

                'framework.core'     => '(:%root.framework:)' . DS . 'Core',
                'framework.library'  => '(:%root.framework:)' . DS . 'Library',
                'framework.module'   => '(:%root.framework:)' . DS . 'Module',
                'framework.optional' => '(:%root.framework:)' . DS . 'Optional',

                'data.module'        => '(:%root.data:)' . DS . 'Module',
                'data.optional'      => '(:%root.data:)' . DS . 'Optional',

                'protocol.Application'            => '(:%root.application:)' . DS,
                'protocol.Data'                   => '(:%root.data:)' . DS,
                'protocol.Data/Etc'               => '(:%protocol.Data:)' .
                                                     'Etc' . DS,
                'protocol.Data/Etc/Configuration' => '(:%protocol.Data/Etc:)' .
                                                     'Configuration' . DS,
                'protocol.Data/Etc/Locale'        => '(:%protocol.Data/Etc:)' .
                                                     'Locale' . DS,
                'protocol.Data/Lost+found'        => '(:%protocol.Data:)' .
                                                     'Lost+found' . DS,
                'protocol.Data/Module'            => '(:%data.module:)' . DS,
                'protocol.Data/Optional'          => '(:%data.module:)' . DS,
                'protocol.Data/Variable'          => '(:%protocol.Data:)' .
                                                     'Variable' . DS,
                'protocol.Data/Variable/Cache'    => '(:%protocol.Data/Variable:)' .
                                                     'Cache' . DS,
                'protocol.Data/Variable/Database' => '(:%protocol.Data/Variable:)' .
                                                     'Database' . DS,
                'protocol.Data/Variable/Log'      => '(:%protocol.Data/Variable:)' .
                                                     'Log' . DS,
                'protocol.Data/Variable/Private'  => '(:%protocol.Data/Variable:)' .
                                                     'Private' . DS,
                'protocol.Data/Variable/Test'     => '(:%protocol.Data/Variable:)' .
                                                     'Test' . DS,
                'protocol.Data'                   => '(:%root.data:)' . DS,
                'protocol.Framework'              => '(:%root.framework:)' . DS
            )
        );

        $this->_parameters->setKeyword(
            $this,
            'root.ofFrameworkDirectory',
            $root
        );

        $this->setParameters($parameters);

        return $this;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in    Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        $handle = $this->_parameters->setParameters($this, $in);
        $this->setProtocol();

        return $handle;
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value ) {

        $handle = $this->_parameters->setParameter($this, $key, $value);
        $this->setProtocol();

        return $handle;
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Set protocol according to the current parameter.
     *
     * @access  protected
     * @return  void
     */
    protected function setProtocol ( ) {

        $protocol     = $this->_parameters->unlinearizeBranche($this, 'protocol');
        $protocolRoot = self::getProtocol();

        foreach($protocol as $path => $reach)
            $protocolRoot->addComponentHelper(
                $path,
                $reach,
                Hoa_Framework_Protocol::OVERWRITE
            );

        return;
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

        return false;
    }

    /**
     * Import a file or a directory.
     * This method finds file, and write some informations (inode, path and
     * already imported or not) into an “import register”. If a file is not in
     * this register, the autoload will return an error.
     * This method is dependent of include_path (ini_set).
     *
     * @access  public
     * @param   string  $path    Path.
     * @param   string  $root    Root.
     * @return  bool
     * @throw   Hoa_Exception
     */
    protected static function _import ( $path = null, $root = null ) {

        static $back = null;
        static $last = null;

        if(null === $back) {

            self::$_lastImport = array();

            if(null === $root)
                $back = self::getInstance()
                            ->getFormattedParameter('framework.library');
            else
                $back = $root;
        }

        preg_match('#(?:(.*?)(?<!\\\)\.)|(.*)#', $path, $matches);

        $handle = !isset($matches[2]) ? $matches[1] : $matches[2];

        switch($handle) {

            case '~':

                $back .= DS . $last;
                $path  = substr($path, 2);

                if(   ($a = is_dir($back)           && !empty($path))
                   || ($b = is_file($back . '.php') &&  empty($path)))
                    self::_import($path);
                else {

                    $back = null;
                    $last = null;

                    if(false === $a) {
                        if(null !== $back)
                            throw new Hoa_Exception(
                                'Directory %s is not found, cannot look in it.',
                                0, $back);
                    }
                    else
                        throw new Hoa_Exception(
                            'File %s is not found.', 1, $back . '.php');
                }
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
                                self::_import(substr($path, strlen($handle) + 1));

                            elseif(is_file($found . '.php')) {

                                $back = $found . '\.php';
                                self::_import(null);
                            }

                            $back = $tmp;
                        }
                    }
                    else {

                        $back .= DS . $handle;
                        self::_import(null);
                    }

                    $back = null;
                    $last = null;
                }
                else {

                    $final = str_replace('\\.', '.', $back);

                    $back = null;
                    $last = null;

                    if(!file_exists($final))
                        $final .= '.php';

                    if(!file_exists($final))
                        throw new Hoa_Exception(
                            'File %s is not found.', 2, $final);

                    if(false === OS_WIN)
                        $inode = fileinode($final);
                    else
                        $inode = md5($final);

                    if(isset(self::$_importStack[$inode]))
                        return true;

                    self::$_lastImport[$inode]  = $final;
                    self::$_importStack[$inode] = array(
                        self::IMPORT_PATH => $final,
                        self::IMPORT_LOAD => false
                    );
                }
        }

        return true;
    }

    /**
     * Import a class of a package (it words like the self::_import() method).
     *
     * @access  public
     * @param   string  $path    Path.
     * @return  bool
     * @throw   Hoa_Exception
     */
    public static function import ( $path ) {

        return self::_import($path);
    }

    /**
     * Import a module (it works like the self::_import() method).
     *
     * @access  public
     * @param   string  $path    Path.
     * @return  bool
     * @throw   Hoa_Exception
     */
    public static function importModule ( $path ) {

        $i               = Hoa_Framework::getInstance();
        $frameworkModule = $i->getFormattedParameter('framework.module');
        $dataModule      = $i->getFormattedParameter('data.module');

        try {

            return self::_import($path, $dataModule);
        }
        catch ( Hoa_Exception $e ) {

            return self::_import($path, $frameworkModule);
        }
    }

    /**
     * Load lastest imported files.
     *
     * @access  public
     * @return  void
     */
    public static function load ( ) {

        if(empty(self::$_lastImport))
            return;

        foreach(self::$_lastImport as $inode => $import) {

            require $import;
            self::$_importStack[self::IMPORT_LOAD] = true;
        }

        self::$_lastImport = array();

        return;
    }

    /**
     * If file is imported (via self::_import()), the autoload method will
     * load the file that contains the class $className.
     *
     * @access  public
     * @param   string  $className    Class name.
     * @return  void
     */
    public static function autoload ( $className ) {

        $pos   = strpos($className, '_');
        $roots = array();

        switch(substr($className, 0, $pos)) {

            case 'Hoa':
                $roots[] = self::getInstance()
                               ->getFormattedParameter('framework.library');
              break;

            case 'Hoathis':
                $roots[] = self::getInstance()
                               ->getFormattedParameter('data.module');
                $roots[] = self::getInstance()
                               ->getFormattedParameter('framework.module');
              break;

            default:
                return;
        }

        $className = substr($className, $pos + 1);
        $gotcha    = null;

        foreach($roots as $i => $root) {

            $handle    = $className;
            $classPath = $root . DS . str_replace('_', DS, $handle) . '.php';

            // If it is an entry class.
            if(!file_exists($classPath)) {

                if(false !== strpos($handle, '_'))
                    $handle .= substr($handle, strrpos($handle, '_')); 
                else
                    $handle .= '_' . $handle;

                $classPath = $root . DS . str_replace('_', DS, $handle) . '.php';
            }

            if(file_exists($classPath)) {

                $gotcha = $classPath;
                break;
            }
        }

        if(null === $gotcha)
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
 * @return  bool
 * @throw   Hoa_Exception
 */
function import ( $path ) {

    return Hoa_Framework::import($path);
}

/**
 * Alias of Hoa_Framework::importModule().
 *
 * @access  public
 * @param   string  $path    Path.
 * @return  bool
 * @throw   Hoa_Exception
 */
function importModule ( $path ) {

    return Hoa_Framework::importModule($path);
}

/**
 * Alias of Hoa_Framework::load().
 *
 * @access  public
 * @return  void
 */
function load ( ) {

    return Hoa_Framework::load();
}


/**
 * Set the default autoload.
 */
spl_autoload_register(array('Hoa_Framework', 'autoload'));

/**
 * Then, initialize Hoa.
 */
Hoa_Framework::getInstance()->initialize();
