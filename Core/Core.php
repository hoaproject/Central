<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

/**
 * Check if Hoa was well-included.
 */
!(
    !defined('HOA') and define('HOA', true)
)
and
    exit('The Hoa framework main file (Core.php) must be included once.');


/**
 * \Hoa\Core\Consistency
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Consistency.php';

/**
 * \Hoa\Core\Event
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Event.php';

/**
 * \Hoa\Core\Exception
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Exception.php';

/**
 * \Hoa\Core\Parameter
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Parameter.php';

/**
 * \Hoa\Core\Protocol
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Protocol.php';

/**
 * \Hoa\Core\Data
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Data.php';

}

namespace Hoa\Core {

/**
 * Class \Hoa\Core.
 *
 * \Hoa\Core is the framework package manager.
 * Each package must include \Hoa\Core, because it is the “taproot” of the
 * framework.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Core implements Parameterizable {

    /**
     * Stack of all registered shutdown function.
     *
     * @var \Hoa\Core array
     */
    private static $_rsdf     = array();

    /**
     * Tree of components, starts by the root.
     *
     * @var \Hoa\Core\Protocol\Root object
     */
    private static $_root     = null;

    /**
     * Parameters of \Hoa\Core.
     *
     * @var \Hoa\Core\Parameter object
     */
    protected $_parameters    = null;

    /**
     * Singleton.
     *
     * @var \Hoa\Core object
     */
    private static $_instance = null;

    /**
     * Whether the debugger is started or not.
     *
     * @var \Hoa\Core bool
     */
    private static $_debugger = false;



    /**
     * Singleton.
     *
     * @access  private
     * @return  void
     */
    private function __construct ( ) {

        !defined('SUCCEED')   and define('SUCCEED', true);
        !defined('FAILED')    and define('FAILED', false);
        !defined('DS')        and define('DS', DIRECTORY_SEPARATOR);
        !defined('PS')        and define('PS', PATH_SEPARATOR);
        !defined('CRLF')      and define('CRLF', "\r\n");
        !defined('OS_WIN')    and define('OS_WIN', !strncasecmp(PHP_OS, 'win', 3));
        !defined('S_64_BITS') and define('S_64_BITS', PHP_INT_SIZE == 8);
        !defined('S_32_BITS') and define('S_32_BITS', !S_64_BITS);
        !defined('void')         and define('void', (unset) null);
        !defined('_public')      and define('_public', 1);
        !defined('_protected')   and define('_protected', 2);
        !defined('_private')     and define('_private', 4);
        !defined('_static')      and define('_static', 8);
        !defined('_abstract')    and define('_abstract', 16);
        !defined('_pure')        and define('_pure', 32);
        !defined('_final')       and define('_final', 64);
        !defined('_dynamic')     and define('_dynamic', ~_static);
        !defined('_concrete')    and define('_concrete',~_abstract);
        !defined('_overridable') and define('_overridable', ~_final);
        !defined('HOA_VERSION_MAJOR')   and define('HOA_VERSION_MAJOR',   1);
        !defined('HOA_VERSION_MINOR')   and define('HOA_VERSION_MINOR',   0);
        !defined('HOA_VERSION_RELEASE') and define('HOA_VERSION_RELEASE', 0);
        !defined('HOA_VERSION_STATUS')  and define('HOA_VERSION_STATUS',  'b');
        !defined('HOA_REVISION')        and define('HOA_REVISION',        998);
        !defined('HOA_REVISION_PREV')   and define('HOA_REVISION_PREV',   600);

        if(false !== $wl = ini_get('suhosin.executor.include.whitelist'))
            if(false === in_array('hoa', explode(',', $wl)))
                throw new Exception(
                    'The URL scheme hoa:// is not authorized by Suhosin. ' .
                    'You must add this to your php.ini or suhosin.ini: ' .
                    'suhosin.executor.include.whitelist="%s", thanks :-).',
                    0, implode(',', array_merge(
                        preg_split('#,#', $wl, -1, PREG_SPLIT_NO_EMPTY),
                        array('hoa')
                    )));

        $date = ini_get('date.timezone');

        if(empty($date))
            ini_set('date.timezone', 'Europe/Paris');

        return;
    }

    /**
     * Singleton.
     *
     * @access  public
     * @return  \Hoa\Core
     */
    public static function getInstance ( ) {

        if(null === static::$_instance)
            static::$_instance = new self();

        return static::$_instance;
    }

    /**
     * Initialize the framework.
     *
     * @access  public
     * @param   array   $parameters    Parameters of \Hoa\Core.
     * @return  \Hoa\Core
     */
    public function initialize ( Array $parameters = array() ) {

        $root              = dirname(__DIR__);
        $this->_parameters = new Parameter(
            $this,
            array(
                'root.ofFrameworkDirectory' => $root
            ),
            array(
                'root'               => '(:root.ofFrameworkDirectory:)',
                'root.framework'     => '(:%root:)',
                'root.data'          => '(:%root:h:)/Data',
                'root.application'   => '(:%root.data:)/../Application',

                'framework.core'     => '(:%root.framework:)/Core',
                'framework.library'  => '(:%root.framework:)/Library',
                'framework.module'   => '(:%root.framework:)/Module',

                'data.module'        => '(:%root.data:)/Module',

                'protocol.Application'            => '(:%root.application:)/',
                'protocol.Application/Public'     => '(:%protocol.Application:)Public/',
                'protocol.Data'                   => '(:%root.data:)/',
                'protocol.Data/Etc'               => '(:%protocol.Data:)Etc/',
                'protocol.Data/Etc/Configuration' => '(:%protocol.Data/Etc:)Configuration/',
                'protocol.Data/Etc/Locale'        => '(:%protocol.Data/Etc:)Locale/',
                'protocol.Data/Lost+found'        => '(:%protocol.Data:)Lost+found/',
                'protocol.Data/Module'            => '(:%data.module:)/',
                'protocol.Data/Temporary'         => '(:%protocol.Data:)Temporary/',
                'protocol.Data/Variable'          => '(:%protocol.Data:)Variable/',
                'protocol.Data/Variable/Cache'    => '(:%protocol.Data/Variable:)Cache/',
                'protocol.Data/Variable/Database' => '(:%protocol.Data/Variable:)Database/',
                'protocol.Data/Variable/Log'      => '(:%protocol.Data/Variable:)Log/',
                'protocol.Data/Variable/Private'  => '(:%protocol.Data/Variable:)Private/',
                'protocol.Data/Variable/Test'     => '(:%protocol.Data/Variable:)Test/',
                'protocol.Data'                   => '(:%root.data:)/',
                'protocol.Library'                => '(:%framework.library:)/',

                'namespace.prefix.Hoa'     => '(:%framework.library:)',
                'namespace.prefix.Hoathis' => '(:%data.module:);(:%framework.module:)'
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
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
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
                Protocol::OVERWRITE
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
    public static function _define ( $name, $value, $case = false) {

        if(!defined($name))
            return define($name, $value, $case);

        return false;
    }

    /**
     * Get protocol's root.
     *
     * @access  public
     * @return  \Hoa\Core\Protocol\Root
     */
    public static function getProtocol ( ) {

        if(null === self::$_root)
            self::$_root = new Protocol\Root();

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

    /**
     * Start the debugger.
     *
     * @access  public
     * @param   \Hoa\Socket\Socketable  $socket    Socket.
     * @return  void
     */
    public static function startDebugger ( \Hoa\Socket\Socketable $socket = null ) {

        from('Hoa')
        -> import('Socket.Internet.DomainName')
        -> import('Socket.Connection.Client');

        if(null === $socket)
            $socket = new \Hoa\Socket\Internet\DomainName(
                'localhost', 57005, 'tcp'
            );

        try {

            $client = new \Hoa\Socket\Connection\Client($socket);
            $client->connect();
            self::$_debugger = true;
        }
        catch ( \Hoa\Core\Exception $e ) {

            throw new \Hoa\Core\Exception(
                'Cannot start the debugger because the server is not ' .
                'listening.', 0);
        }

        $client->writeLine('open');

        event('hoa://Event/Exception')
            ->attach(function ( \Hoa\Core\Event\Bucket $event) use ( $client ) {

                $exception = $event->getData();

                try {

                    $client->writeLine(serialize($exception));
                }
                catch ( \Exception $e ) {

                    $client->writeLine('error serialize');
                }
            });

        return;
    }

    /**
     * Dump a data to the debugger.
     *
     * @access  public
     * @param   mixed   $data    Data.
     * @return  void
     */
    public static function dump ( $data ) {

        if(false === self::$_debugger)
            self::startDebugger();

        try {

            $trace = debug_backtrace();

            ob_start();
            debug_zval_dump($data);
            $data = trim(ob_get_contents());
            ob_end_clean();

            throw new Exception\Error(
                $data, 0, $trace[0]['file'], $trace[0]['line'], $trace);
        }
        catch ( Exception $e ) { }

        return;
    }

    /**
     * Return the copyright and license of Hoa.
     *
     * @access  public
     * @return  string
     */
    public static function © ( ) {

        return 'Copyright © 2007-2011 Ivan Enderlin. All rights reserved.' . "\n" .
               'New BSD License.';
    }
}

}

namespace {

/**
 * Alias of function_exists().
 *
 * @access  public
 * @param   string  $name    Name.
 * @return  bool
 */
function ƒ ( $name ) {

    return function_exists($name);
}

/**
 * Alias of \Hoa\Core::_define().
 *
 * @access  public
 * @param   string  $name     The name of the constant.
 * @param   string  $value    The value of the constant.
 * @param   bool    $case     True set the case-insentisitve.
 * @return  bool
 */
if(!ƒ('_define')) {
function _define ( $name, $value, $case = false ) {

    return \Hoa\Core::_define($name, $value, $case);
}}

/**
 * Alias of \Hoa\Core::dump().
 *
 * @access  public
 * @param   mixed   $data    Data.
 * @return  void
 */
if(!ƒ('dump')) {
function dump ( $message ) {

    return \Hoa\Core::dump($message);
}}

/**
 * Alias.
 */
class_alias('Hoa\Core\Core', 'Hoa\Core');

/**
 * Then, initialize Hoa.
 */
\Hoa\Core::getInstance()->initialize();

}
