<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Core\Consistency {

/**
 * Hard-preload.
 */
$path = __DIR__ . DIRECTORY_SEPARATOR;
define('PATH_EVENT',     $path . 'Event.php');
define('PATH_EXCEPTION', $path . 'Exception.php');
define('PATH_DATA',      $path . 'Data.php');

/**
 * Class Hoa\Core\Consistency.
 *
 * This class manages all classes, importations etc.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Consistency implements \ArrayAccess {

    /**
     * One singleton by library family.
     *
     * @var \Hoa\Core\Consistency array
     */
    private static $_multiton = array();

    /**
     * Libraries to considere.
     *
     * @var \Hoa\Core\Consistency array
     */
    protected $_from          = null;

    /**
     * Library's roots to considere.
     *
     * @var \Hoa\Core\Consistency array
     */
    protected $_roots         = array();

    /**
     * Cache all imports.
     *
     * @var \Hoa\Core\Consistency array
     */
    protected static $_cache  = array();

    /**
     * Cache all classes informations: path, alias and imported.
     *
     * @var \Hoa\Core\Consistency array
     */
    protected static $_class  = array(
        // Hard-preload.
        'Hoa\Core\Event' => array(
            'path'     => PATH_EVENT,
            'alias'    => false,
            'imported' => false
        ),
        'Hoa\Core\Exception' => array(
            'path'     => PATH_EXCEPTION,
            'alias'    => false,
            'imported' => false
        ),
        'Hoa\Core\Data' => array(
            'path'     => PATH_DATA,
            'alias'    => false,
            'imported' => false
        ),
    );

    /**
     * Cache all classes from the current library family.
     * It contains references to self:$_class.
     *
     * @var \Hoa\Core\Consistency array
     */
    protected $__class        = array();

    /**
     * Whether autoload imported files or not.
     * Possible values:
     *     • 0, autoload (normal behavior);
     *     • 1, load (oneshot, back to autoload after loading files);
     *     • 2, load* (autoload for all imports).
     *
     * @var \Hoa\Core\Consistency bool
     */
    protected $_autoload      = false;



    /**
     * Singleton to manage a library family.
     *
     * @access  public
     * @param   string  $from    Library family's name.
     * @return  void
     */
    private function __construct ( $from ) {

        $this->_from = preg_split('#\s*(,|or)\s*#', $from);
        $parameters  = \Hoa\Core::getInstance()->getParameters();
        $wildcard    = $parameters->getFormattedParameter('namespace.prefix.*');

        foreach($this->_from as $f)
            $this->setRoot(
                $parameters->getFormattedParameter('namespace.prefix.' . $f)
                ?: $wildcard,
                $f
            );

        return;
    }

    /**
     * Get the library family's singleton.
     *
     * @access  public
     * @param   string  $from    Library family's name.
     * @return  \Hoa\Core\Consistency
     */
    public static function from ( $namespace ) {

        if(!isset(static::$_multiton[$namespace]))
            static::$_multiton[$namespace] = new static($namespace);

        return static::$_multiton[$namespace];
    }

    /**
     * Import, i.e. pre-load, one or many classes. If $load parameters is set
     * to true, then pre-load is turned to direct-load.
     *
     * @access  public
     * @param   string  $path       Path.
     * @param   bool    $load       Whether loading directly or not.
     * @param   string  &$family    Finally choosen family.
     * @return  \Hoa\Core\Consistency
     * @throw   \Hoa\Core\Exception
     */
    public function import ( $path, $load = null, &$family = null ) {

        $out = false;

        if(   null === $load
           &&    1 === $load = $this->getAutoload())
            $this->setAutoload(0);

        $load = (bool) $load;

        foreach($this->_from as $from)
            foreach($this->_roots[$from] as $root) {

                $family = $from;
                $out    = $this->_import($path, $load, $from, $root);

                if(true === $out)
                    break 2;
            }

        if(false === $out) {

            $trace = debug_backtrace();
            $file  = $trace[0]['file'];

            foreach(static::$_class as $_ => $bucket)
                if(is_array($bucket) && $file === $bucket['path'])
                    break;

            throw new \Hoa\Core\Exception(
                'Class %s does not exist. This file is required by %s.',
                0, array(
                    (1 === count($this->_from)
                        ? $this->_from[0]
                        : '(' .  implode(' or ', $this->_from) . ')') .
                    '\\' . str_replace('.', '\\', $path),
                    isset($bucket)
                        ? $bucket['alias'] ?: $_
                        : $file
                ));
        }

        return $this;
    }

    /**
     * Real import method for an one specific root.
     *
     * @access  protected
     * @param   string    $path        Path.
     * @param   bool      $load        Whether loading directly or not.
     * @param   string    $from        Library family's name.
     * @param   string    $root        Root.
     * @param   callable  $callback    Callback (also disable cache).
     * @return  bool
     */
    protected function _import ( $path, $load, $from, $root, $callback = null ) {

        if(!empty($from))
            $all = $from . '.' . $path;
        else
            $all = $path;

        if(isset(static::$_cache[$all]) && null === $callback) {

            if(false === $load)
                return true;

            $class = str_replace('.', '\\', $all);

            if(isset(static::$_class[$class])) {

                $alias = static::$_class[$class]['alias'];

                return $this->_load($class, false !== $alias, $alias);
            }
        }

        static::$_cache[$all] = true;
        $uncache              = array($all);
        $edited               = false;
        $explode              = explode('.', $all);
        $parts                = array();

        if(false !== strpos($all, '~')) {

            $handle  = array_shift($explode);
            $parts[] = $handle;

            foreach($explode as $value)
                if(false !== strpos($value, '~'))
                    $parts[] = $handle = str_replace('~', $handle, $value);
                else
                    $parts[] = $handle = $value;

            $all     = implode('.', $parts);
            $explode = $parts;
            $edited  = true;

            if(isset(static::$_cache[$all]) && null === $callback) {

                if(false === $load)
                    return true;

                $class = str_replace('.', '\\', $all);
                $alias = static::$_class[$class]['alias'];

                return $this->_load($class, false !== $alias, $alias);
            }

            static::$_cache[$all] = true;
            $uncache[]            = $all;
        }

        if(false !== strpos($all, '*')) {

            $backup = array($explode[0], $explode[1]);

            if(WITH_COMPOSER) {

                $explode[0] = strtolower($explode[0]);
                $explode[1] = strtolower($explode[1]);
            }

            $explode[0] = $root . $explode[0];
            $countFrom  = strlen($explode[0]) + 1;
            $glob       = glob(implode('/', $explode) . '.php');

            if(empty($glob)) {

                foreach($uncache as $un)
                    unset(static::$_cache[$un]);

                return false;
            }

            foreach($glob as $value) {

                $path = substr(
                    str_replace('/', '.', substr($value, 0, -4)),
                    $countFrom
                );
                $path = $backup[1] . substr($path, strpos($path, '.'));
                $out  = $this->_import(
                    $path,
                    $load,
                    $from,
                    $root,
                    $callback
                );

                if(false === $out) {

                    foreach($uncache as $un)
                        unset(static::$_cache[$un]);

                    return false;
                }
            }

            return true;
        }

        if(false === $edited)
            $parts = $explode;

        $count  = count($parts);
        $backup = array($parts[0], $parts[1]);

        if(WITH_COMPOSER) {

            $parts[0] = strtolower($parts[0]);
            $parts[1] = strtolower($parts[1]);
        }

        $parts[0] = $root . $parts[0];
        $path     = implode('/',  $parts) . '.php';

        if(!file_exists($path)) {

            $parts[] = $parts[$count - 1];
            $path    = implode('/',  $parts) . '.php';
            ++$count;

            if(!file_exists($path)) {

                foreach($uncache as $un)
                    unset(static::$_cache[$un]);

                return false;
            }
        }

        $parts[0] = $backup[0];
        $parts[1] = $backup[1];
        $entry    = $parts[$count - 2] == $parts[$count - 1];
        $class    = implode('\\', $parts);
        $alias    = false;

        if(isset(static::$_class[$class])) {

            if(null !== $callback)
                $callback($class);

            return true;
        }

        if(true === $entry) {

            array_pop($parts);
            $alias                  = implode('\\', $parts);
            static::$_class[$alias] = $class;
            $this->__class[$alias]  = &static::$_class[$alias];
        }

        static::$_class[$class] = array(
            'path'     => $path,
            'alias'    => $alias,
            'imported' => false
        );
        $this->__class[$class]  = &static::$_class[$class];

        if(false === $load) {

            if(null !== $callback)
                $callback($class);

            return true;
        }

        $out = $this->_load($class, $entry, $alias);

        if(null !== $callback)
            $callback($class);

        return $out;
    }

    /**
     * Load a class.
     *
     * @access  protected
     * @param   string    $class    Classname.
     * @param   bool      $entry    Whether it is an entry class.
     * @param   string    $alias    Alias classname.
     * @return  bool
     */
    protected function _load ( $class, $entry = false, $alias = false ) {

        $bucket = &static::$_class[$class];

        if(true === $bucket['imported'])
            return true;

        require $bucket['path'];

        $bucket['imported'] = true;

        if(true === $entry && false !== $alias)
            class_alias($class, $alias);

        return true;
    }

    /**
     * Iterate over each solution found by an import.
     *
     * @access  public
     * @param   string    $path        Path.
     * @param   callable  $callback    Callback (also disable cache).
     * @return  void
     */
    public function foreachImport ( $path, $callback ) {

        foreach($this->_from as $from)
            foreach($this->_roots[$from] as $root) {

                $family = $from;
                $out    = $this->_import($path, false, $from, $root, $callback);
            }

        return;
    }

    /**
     * Set the root of the current library family.
     *
     * @access  public
     * @param   bool    $root    Root.
     * @param   string  $from    Library family's name (if null, first family
     *                           will be choosen).
     * @return  \Hoa\Core\Consistency
     */
    public function setRoot ( $root, $from = null ) {

        if(null === $from)
            $from = $this->_from[0];

        $this->_roots[$from] = preg_split('#(?<!\\\);#', $root);

        foreach($this->_roots[$from] as &$freshroot)
            $freshroot = str_replace('\;', ';', $freshroot);

        return $this;
    }

    /**
     * Get roots of the current library family.
     *
     * @access  public
     * @return  array
     */
    public function getRoot ( ) {

        return $this->_roots;
    }

    /**
     * To be conform with \ArrayAccess.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return false;
    }

    /**
     * Use options in the importation flow.
     * E.g:
     *     from('Hoa')
     *
     *     ['load']
     *     -> import('Cache.Memoize');
     * is strictly equivalent to:
     *     from('Hoa')
     *     -> import('Cache.Memoize', true);
     * It's just funnier and more beautiful. Easter egg \o/.
     * Options could be a string or an array. Current recognized options are:
     *     • 'root' => 'new/root', equivalent to setRoot('new/root');
     *     • 'load', equivalent to setAutoload(1);
     *     • 'load*', equivalent to setAutoload(2);
     *     • 'autoload', equivalent to setAutoload(0);
     *     • '…' (unrecognized option), equivalent to setRoot(…).
     * Obviously, we can combine options:
     *     [['load', 'root' => 'new/root']]
     *
     * @access  public
     * @param   mixed  $options    Options.
     * @return  \Hoa\Core\Consistency
     */
    public function offsetGet ( $options ) {

        foreach((array) $options as $option => $value)
            switch("$option") {

                case 'root':
                    $this->setRoot($value);
                  break;

                default:
                    switch($value) {

                        case 'load':
                            $this->setAutoload(1);
                          break;

                        case 'load*':
                            $this->setAutoload(2);
                          break;

                        case 'autoload':
                            $this->setAutoload(0);
                          break;

                        default:
                            $this->setRoot($value);
                    }
            }

        return $this;
    }

    /**
     * To be conform with \ArrayAccess.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @param   mixed  $offset    Value.
     * @return  bool
     */
    public function offsetSet ( $offset, $value ) {

        return false;
    }

    /**
     * To be conform with \ArrayAccess.
     *
     * @access  public
     * @param   mixed  $offset    Offset.
     * @return  bool
     */
    public function offsetUnset ( $offset ) {

        return false;
    }

    /**
     * Set autoload.
     *
     * @access  public
     * @param   bool  $autoload    Autoload.
     * @return  bool
     */
    public function setAutoload ( $autoload ) {

        $old             = $this->_autoload;
        $this->_autoload = $autoload;

        return $old;
    }

    /**
     * Get autoload.
     *
     * @access  public
     * @return  bool
     */
    public function getAutoload ( ) {

        return $this->_autoload;
    }

    /**
     * Get imported classes from the current library family.
     *
     * @access  public
     * @return  array
     */
    public function getImportedClasses ( ) {

        return $this->__class;
    }

    /**
     * Get imported classes from all library families.
     *
     * @access  public
     * @return  array
     */
    public static function getAllImportedClasses ( ) {

        return static::$_class;
    }

    /**
     * Get the shortest name for a class, i.e. if an alias exists, return it,
     * else return the normal classname.
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @return  string
     */
    public static function getClassShortestName ( $classname ) {

        if(!isset(static::$_class[$classname]))
            return $classname;

        if(is_string(static::$_class[$classname]))
            return $classname;

        return static::$_class[$classname]['alias'] ?: $classname;
    }

    /**
     * Autoloader.
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @return  bool
     */
    public static function autoload ( $classname ) {

        $classname = ltrim($classname, '\\');

        // Hard-preload.
        if(   'Hoa\Core' === substr($classname, 0, 8)
           &&      false !== $pos = strpos($classname, '\\', 10))
            $classname = substr($classname, 0, $pos);

        $classes = static::getAllImportedClasses();

        if(!isset($classes[$classname]))
            return false;

        $class = &$classes[$classname];

        if(is_string($class)) {

            $classname = $class;
            $class     = &$classes[$class];
        }

        require $class['path'];

        $class['imported'] = true;

        if(false !== $class['alias'])
            class_alias($classname, $class['alias']);

        return true;
    }

    /**
     * Load a class from its classname.
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @return  string
     * @throw   \Hoa\Core\Exception
     */
    public static function autoloadFromClass ( $classname ) {

        $head = trim(str_replace(
                    '\\',
                    '.',
                    substr($classname, 0, $pos = strpos($classname, '\\'))
                ), '()');
        $tail = substr($classname, $pos + 1);

        static::from($head)
            ->import(str_replace('\\', '.', $tail), true, $family);

        return $family . '\\' . $tail;
    }

    /**
     * Dynamic new, i.e. a native factory (import + load + instance).
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @param   array   $arguments    Constructor's arguments.
     * @return  object
     * @throw   \Hoa\Core\Exception
     */
    public static function dnew ( $classname, Array $arguments = array() ) {

        $classname = ltrim($classname, '\\');

        if(!class_exists($classname, false))
            $classname = static::autoloadFromClass($classname);

        $class = new \ReflectionClass($classname);

        if(empty($arguments) || false === $class->hasMethod('__construct'))
            return $class->newInstance();

        return $class->newInstanceArgs($arguments);
    }

    /**
     * Enable import when unserializing.
     *
     * When unserializing an object, if the class is not imported, it will
     * fails. This method will automatically import class when needed.
     * Note: for now, this is only restricted to Hoa autoloader, i.e. it does
     * not take into account other existing autoloader.
     *
     * @access  public
     * @return  void
     */
    public static function enableImportWhenUnserializing ( ) {

        ini_set(
            'unserialize_callback_func',
            get_called_class() . '::autoloadFromClass'
        );

        return;
    }

    /**
     * Disable import when unserializing.
     *
     * @access  public
     * @return  void
     */
    public static function disableImportWhenUnserializing ( ) {

        ini_restore('unserialize_callback_func');

        return;
    }

    /**
     * Whether a word is reserved or not.
     *
     * @access  public
     * @param   string  $word    Word.
     * @return  void
     */
    public static function isKeyword ( $word ) {

        static $_list = array(
            // PHP keywords.
            '__halt_compiler', 'abstract',     'and',           'array',
            'as',              'break',        'callable',      'case',
            'catch',           'class',        'clone',         'const',
            'continue',        'declare',      'default',       'die',
            'do',              'echo',         'else',          'elseif',
            'empty',           'enddeclare',   'endfor',        'endforeach',
            'endif',           'endswitch',    'endwhile',      'eval',
            'exit',            'extends',      'final',         'for',
            'foreach',         'function',     'global',        'goto',
            'if',              'implements',   'include',       'include_once',
            'instanceof',      'insteadof',    'interface',     'isset',
            'list',            'namespace',    'new',           'or',
            'print',           'private',      'protected',     'public',
            'require',         'require_once', 'return',        'static',
            'switch',          'throw',        'trait',         'try',
            'unset',           'use',          'var',           'while',
            'xor',
            // Compile-time constants.
            '__class__',       '__dir__',      '__file__',      '__function__',
            '__line__',        '__method__',   '__namespace__', '__trait__'
        );

        return in_array(strtolower($word), $_list);
    }

}

/**
 * Class Hoa\Core\Consistency\Xcallable.
 *
 * Build a callable object, i.e. function, class::method, object->method or
 * closure, they all have the same behaviour. This callable is an extension of
 * native PHP callable (aka callback) to integrate Hoa's structures.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Xcallable {

    /**
     * Callback, with the PHP format.
     *
     * @var \Hoa\Core\Consistency\Xcallable mixed
     */
    protected $_callback = null;

    /**
     * Callable hash.
     *
     * @var \Hoa\Core\Consistency\Xcallable string
     */
    protected $_hash     = null;



    /**
     * Build a callback.
     * Accepted forms:
     *     * 'function';
     *     * 'class::method';
     *     * 'class', 'method';
     *     * $object, 'method';
     *     * $object, '';
     *     * function ( … ) { … }.
     *
     * @access  public
     * @param   mixed   $call    First callable part.
     * @param   mixed   $able    Second callable part (if needed).
     * @return  mixed
     */
    public function __construct ( $call, $able = '' ) {

        if(null === $call)
            return null;

        if($call instanceof \Closure) {

            $this->_callback = $call;

            return;
        }

        if(!is_string($able))
            throw new \Hoa\Core\Exception(
                'Bad callback form.', 0);

        if('' === $able)
            if(is_string($call)) {

                if(false === strpos($call, '::')) {

                    $this->_callback = $call;

                    return;
                }

                list($call, $able) = explode('::', $call);
            }
            elseif(   is_object($call)
                   && $call instanceof \Hoa\Stream\IStream\Out)
                $able = null;
            else
                throw new \Hoa\Core\Exception(
                    'Bad callback form.', 1);

        $this->_callback = array($call, $able);

        return;
    }

    /**
     * Call the callable.
     *
     * @access  public
     * @param   ...
     * @return  mixed
     */
    public function __invoke ( ) {

        $arguments = func_get_args();
        $valid     = $this->getValidCallback($arguments);

        return call_user_func_array($valid, $arguments);
    }

    /**
     * Distribute arguments according to an array.
     *
     * @access  public
     * @param   array  $arguments    Arguments.
     * @return  mixed
     */
    public function distributeArguments ( Array $arguments ) {

        return call_user_func_array(array($this, '__invoke'), $arguments);
    }

    /**
     * Get a valid callback in the PHP meaning.
     *
     * @access  public
     * @param   array   &$arguments    Arguments (could determine method on an
     *                                 object if not precised).
     * @return  mixed
     */
    public function getValidCallback ( Array &$arguments = array() ) {

        $callback = $this->_callback;
        $head     = null;

        if(isset($arguments[0]))
            $head = &$arguments[0];

        // If method is undetermined, we find it (we understand event bucket and
        // stream).
        if(   null !== $head
           && is_array($callback)
           && null === $callback[1]) {

            if($head instanceof \Hoa\Core\Event\Bucket)
                $head = $head->getData();

            switch($type = gettype($head)) {

                case 'string':
                    if(1 === strlen($head))
                        $method = 'writeCharacter';
                    else
                        $method = 'writeString';
                  break;

                case 'boolean':
                case 'integer':
                case 'array':
                    $method = 'write' . ucfirst($type);
                  break;

                case 'double':
                    $method = 'writeFloat';
                  break;

                default:
                    $method = 'writeAll';
                    $head   = $head . "\n";
            }

            $callback[1] = $method;
        }

        return $callback;
    }

    /**
     * Get hash.
     * Will produce:
     *     * function#…;
     *     * class#…::…;
     *     * object(…)#…::…;
     *     * closure(…).
     *
     * @access  public
     * @return  string
     */
    public function getHash ( ) {

        if(null !== $this->_hash)
            return $this->_hash;

        $_ = &$this->_callback;

        if(is_string($_))
            return $this->_hash = 'function#' . $_;

        if(is_array($_))
            return $this->_hash =
                       (is_object($_[0])
                           ? 'object(' . spl_object_hash($_[0]) . ')' .
                             '#' . get_class($_[0])
                           : 'class#' . $_[0]) .
                       '::' .
                       (null !== $_[1]
                           ? $_[1]
                           : '???');

        return $this->_hash = 'closure(' . spl_object_hash($_) . ')';
    }

    /**
     * Get appropriated reflection instance.
     *
     * @access  public
     * @param   ...
     * @return  \Reflector
     */
    public function getReflection ( ) {

        $arguments = func_get_args();
        $valid     = $this->getValidCallback($arguments);

        if(is_string($valid))
            return new \ReflectionFunction($valid);

        if($valid instanceof \Closure)
            return new \ReflectionFunction($valid);

        if(is_array($valid)) {

            if(is_string($valid[0]))
                return new \ReflectionMethod($valid[0], $valid[1]);

            $object = new \ReflectionObject($valid[0]);

            if(null === $valid[1])
                return $object;

            return $object->getMethod($valid[1]);
        }
    }

    /**
     * Return the hash.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getHash();
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
 * Alias for \Hoa\Core\Consistency::from().
 *
 * @access  public
 * @param   string  $from    Library family's name.
 * @return  \Hoa\Core\Consistency
 */
if(!ƒ('from')) {
function from ( $namespace ) {

    return \Hoa\Core\Consistency::from($namespace);
}}

/**
 * Alias of \Hoa\Core\Consistency::dnew().
 *
 * @access  public
 * @param   string  $classname    Classname.
 * @param   array   $arguments    Constructor's arguments.
 * @return  object
 */
if(!ƒ('dnew')) {
function dnew ( $classname, Array $arguments = array() ) {

    return \Hoa\Core\Consistency::dnew($classname, $arguments);
}}

/**
 * Alias of \Hoa\Core\Consistency\Xcallable.
 *
 * @access  public
 * @param   mixed   $call    First callable part.
 * @param   mixed   $able    Second callable part (if needed).
 * @return  mixed
 */
if(!ƒ('xcallable')) {
function xcallable ( $call, $able = '' ) {

    if($call instanceof \Hoa\Core\Consistency\Xcallable)
        return $call;

    return new \Hoa\Core\Consistency\Xcallable($call, $able);
}}

/**
 * Curry.
 * Example:
 *     $c = curry('str_replace', …, …, 'foobar');
 *     var_dump($c('foo', 'baz')); // bazbar
 *     $c = curry('str_replace', 'foo', 'baz', …);
 *     var_dump($c('foobarbaz')); // bazbarbaz
 * Nested curries also work:
 *     $c1 = curry('str_replace', …, …, 'foobar');
 *     $c2 = curry($c1, 'foo', …);
 *     var_dump($c2('baz')); // bazbar
 * Obviously, as the first argument is a callable, we can combine this with
 * \Hoa\Core\Consistency\Xcallable ;-).
 * The “…” character is the HORIZONTAL ELLIPSIS Unicode character (Unicode:
 * 2026, UTF-8: E2 80 A6).
 *
 * @access  public
 * @param   mixed  $callable    Callable (two parts).
 * @param   ...    ...          Arguments.
 * @return  \Closure
 */
if(!ƒ('curry')) {
function curry ( $callable ) {

    $arguments = func_get_args();
    array_shift($arguments);
    $ii        = array_keys($arguments, …, true);

    return function ( ) use ( $callable, $arguments, $ii ) {

        return call_user_func_array(
            $callable,
            array_replace($arguments, array_combine($ii, func_get_args()))
        );
    };
}}

/**
 * Same as curry() but where all arguments are references.
 *
 * @access  public
 * @param   mixed  &$callable    Callable (two parts).
 * @param   ...    &...          Arguments.
 * @return  \Closure
 */
if(!ƒ('curry_ref')) {
function curry_ref ( &$callable, &$a = null, &$b = null, &$c = null, &$d = null,
                                 &$e = null, &$f = null, &$g = null, &$h = null,
                                 &$i = null, &$j = null, &$k = null, &$l = null,
                                 &$m = null, &$n = null, &$o = null, &$p = null,
                                 &$q = null, &$r = null, &$s = null, &$t = null,
                                 &$u = null, &$v = null, &$w = null, &$x = null,
                                 &$y = null, &$z = null ) {

    $handle    = func_get_args();
    $arguments = array();

    for($i = 0, $max = func_num_args() - 1; $i < $max; ++$i)
        $arguments[] = &${chr(97 + $i)};

    $ii        = array_keys($arguments, …, true);

    return function ( ) use ( &$callable, &$arguments, $ii ) {

        return call_user_func_array(
            $callable,
            array_replace($arguments, array_combine($ii, func_get_args()))
        );
    };
}}

/**
 * Make the alias automatically (because it's not imported with the import()
 * function).
 */
class_alias('Hoa\Core\Consistency\Consistency', 'Hoa\Core\Consistency');

/**
 * Set autoloader.
 */
spl_autoload_register('\Hoa\Core\Consistency::autoload');

/**
 * Auto-enable import when unserializing.
 */
Hoa\Core\Consistency::enableImportWhenUnserializing();

}
