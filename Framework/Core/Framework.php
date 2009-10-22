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
 * Hoa_Framework_Parameter
 */
require_once 'Parameter.php';

/**
 * Hoa_Framework_Protocol
 */
require_once 'Protocol.php';

/**
 * Some usefull constants, …
 */
!defined('DS')              and define('DS'    ,    DIRECTORY_SEPARATOR);
!defined('PS')              and define('PS'    ,    PATH_SEPARATOR);
!defined('CRLF')            and define('CRLF'  ,    "\r\n");
!defined('OS_WIN')          and define('OS_WIN',    !strncasecmp(PHP_OS, 'win', 3));
!defined('S_64_BITS')       and define('S_64_BITS', PHP_INT_SIZE == 8);
!defined('S_32_BITS')       and define('S_32_BITS', !S_64_BITS);
!defined('SUCCEED')         and define('SUCCEED',   true);
!defined('FAILED')          and define('FAILED',    false);
!defined('PHP_VERSION_ID')  and $v = PHP_VERSION
                            and define('PHP_VERSION_ID',   $v{0} * 10000
                                                         + $v{2} * 100
                                                         + $v{4});

/**
 * … and type.
 */
!defined('void')            and define('void',      (unset) null);

/**
 * Check if Hoa was well-included.
 */
!(
    !defined('HOA')         and define('HOA', true)
)
and
    exit('The Hoa framework main file (Framework.php) must be included once.');


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
            array(),
            array(
                'root.framework'     => $root,
                'root.data'          => dirname($root) . DS . 'Data',
                'root.application'   => dirname($root) . DS . 'Application',

                'framework.core'     => '(:%root.framework:)' . DS . 'Core',
                'framework.library'  => '(:%root.framework:)' . DS . 'Library',
                'framework.module'   => '(:%root.framework:)' . DS . 'Module',
                'framework.optional' => '(:%root.framework:)' . DS . 'Optional',

                'data.module'        => '(:%root.data:)' . DS . 'Module',
                'data.optional'      => '(:%root.data:)' . DS . 'Optional',

                'protocol.Application'            => '(:%root.application:)' . DS,
                'protocol.Data'                   => '(:%root.data:)' . DS,
                'protocol.Data/Etc'               => '(:%protocol.Data:)' . DS .
                                                     'Etc' . DS,
                'protocol.Data/Etc/Configuration' => '(:%protocol.Data/Etc:)' . DS .
                                                     'Configuration' . DS,
                'protocol.Data/Etc/Locale'        => '(:%protocol.Data/Etc:)' . DS .
                                                     'Locale' . DS,
                'protocol.Data/Lost+found'        => '(:%protocol.Data:)' . DS .
                                                     'Lost+found' . DS,
                'protocol.Data/Module'            => '(:%data.module:)' . DS,
                'protocol.Data/Optional'          => '(:%data.module:)' . DS,
                'protocol.Data/Variable'          => '(:%protocol.Data:)' . DS .
                                                     'Variable' . DS,
                'protocol.Data/Variable/Cache'    => '(:%protocol.Data/Variable:)' . DS .
                                                     'Cache' . DS,
                'protocol.Data/Variable/Database' => '(:%protocol.Data/Variable:)' . DS .
                                                     'Database' . DS,
                'protocol.Data/Variable/Log'      => '(:%protocol.Data/Variable:)' . DS .
                                                     'Log' . DS,
                'protocol.Data/Variable/Private'  => '(:%protocol.Data/Variable:)' . DS .
                                                     'Private' . DS,
                'protocol.Data/Variable/Test'     => '(:%protocol.Data/Variable:)' . DS .
                                                     'Test' . DS,
                'protocol.Data'                   => '(:%root.data:)' . DS,
                'protocol.Framework'              => '(:%root.framework:)' . DS
            )
        );

        $this->setParameters($parameters);

        return $this;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        $handle = $this->_parameters->setParameters($this, $in);

        if(true === $this->_parameters->brancheExists($this, 'protocol', $in))
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

        return $handle;
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key      Key.
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
            $protocolRoot->addComponentHelper($path, $reach);

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
     * This method find file, and write some informations (inode, path, and
     * already imported or not) into an “import register”. If a file is not in
     * this register, the autoload will return an error.
     * This method is dependent of include_path (ini_set).
     *
     * @access  public
     * @param   string  $path    Path.
     * @param   bool    $load    Load file when over.
     * @param   string  $root    Root.
     * @return  void
     * @throw   Hoa_Exception
     */
    public static function import ( $path = null, $load = false, $root = null ) {

        static $back = null;
        static $last = null;

        if(null === $back)
            if(null === $root)
                $back = self::getInstance()
                            ->getFormattedParameter('framework.library');
            else
                $back = $root;

        preg_match('#(?:(.*?)(?<!\\\)\.)|(.*)#', $path, $matches);

        $handle = !isset($matches[2]) ? $matches[1] : $matches[2];

        switch($handle) {

            case '~':

                $back .= DS . $last;
                $path  = substr($path, 2);

                if(   ($a = is_dir($back)           && !empty($path))
                   || ($b = is_file($back . '.php') &&  empty($path)))
                    self::import($path, $load);
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

    public static function importModule ( $path, $load = false ) {

        $i               = Hoa_Framework::getInstance();
        $frameworkModule = $i->getFormattedParameter('framework.module');
        $dataModule      = $i->getFormattedParameter('data.module');

        try {

            self::import($path, $load, $dataModule);
        }
        catch ( Hoa_Exception $e ) {

            self::import($path, $load, $frameworkModule);
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
 * @param   bool    $load    Load file when over.
 * @return  bool
 */
function import ( $path, $load = false ) {

    return Hoa_Framework::import($path, $load);
}

function importModule ( $path, $load = false ) {

    return Hoa_Framework::importModule($path, $load);
}


/**
 * Set the default autoload.
 */
spl_autoload_register(array('Hoa_Framework', 'autoload'));

/**
 * Catch uncaught exception.
 */
set_exception_handler(array('Hoa_Exception', 'handler'));

/**
 * Then, initialize Hoa.
 */
Hoa_Framework::getInstance()->initialize();
