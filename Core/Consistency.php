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

namespace Hoa\Core\Consistency {

/**
 * Class Hoa\Core\Consistency.
 *
 * This class manages all classes, importations etc.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Consistency implements \ArrayAccess {

    /**
     * One singleton by library family.
     *
     * @var \Hoa\Consistency array
     */
    private static $_multiton = array();

    /**
     * Libraries to considere.
     *
     * @var \Hoa\Consistency array
     */
    protected $_from          = null;

    /**
     * Library's roots to considere.
     *
     * @var \Hoa\Consistency array
     */
    protected $_roots         = array();

    /**
     * Cache all imports.
     *
     * @var \Hoa\Consistency array
     */
    protected static $_cache  = array();

    /**
     * Cache all classes informations: path, alias and imported.
     *
     * @var \Hoa\Consistency array
     */
    protected static $_class  = array();

    /**
     * Cache all classes from the current library family.
     * It contains references to self:$_class.
     *
     * @var \Hoa\Consistency array
     */
    protected $__class        = array();

    /**
     * Whether autoload imported files or not.
     * Possible values:
     *     • 0, autoload (normal behavior);
     *     • 1, load (oneshot, back to autoload after loading files);
     *     • 2, load* (autoload for all imports).
     *
     * @var \Hoa\Consistency bool
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

        foreach($this->_from as $f)
            $this->setRoot(
                \Hoa\Core::getInstance()->getParameters()->getFormattedParameter(
                    'namespace.prefix.' . $f
                ) ?: '/Flatland',
                $f
            );

        return;
    }

    /**
     * Get the library family's singleton.
     *
     * @access  public
     * @param   string  $from    Library family's name.
     * @return  \Hoa\Consistency
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
     * @return  \Hoa\Consistency
     * @throw   \Hoa\Core\Exception
     */
    public function import ( $path, $load = null, &$family = null ) {

        $exception = null;
        $out       = false;

        if(   null === $load
           && 1    === $load = $this->getAutoload())
            $this->setAutoload(0);

        $load = (bool) $load;

        foreach($this->_from as $from)
            foreach($this->_roots[$from] as $root)
                try {

                    $classname = $from . '\\' . str_replace('.', '\\', $path);
                    $family    = $from;

                    if(class_exists($classname, false))
                        return $this;

                    $out = $this->_import($path, $load, $from, $root);

                    break 2;
                }
                catch ( \Hoa\Core\Exception\Idle $e ) {

                    $exception = $e;
                    $out       = false;
                }

        if(false === $out) {

            $trace = $exception->getTrace();
            $self  = get_class($this);

            do {

                $t = array_shift($trace);
            } while(isset($t['class']) && $t['class'] == $self);

            throw new \Hoa\Core\Exception(
                'The file %s need the class %s to work properly but this ' .
                'last one is not found. We have looked for in: %s family(ies).',
                0,
                array(@$t['file'], $path, implode(', ', $this->_from)),
                $exception
            );
        }

        return $out;
    }

    /**
     * Real import method for an one specific root.
     *
     * @access  protected
     * @param   string  $path    Path.
     * @param   bool    $load    Whether loading directly or not.
     * @param   string  $from    Library family's name.
     * @param   string  $root    Root.
     * @return  \Hoa\Consistency
     * @throw   \Hoa\Core\Exception\Idle
     */
    protected function _import ( $path, $load, $from, $root ) {

        if(!empty($from))
            $all = $from . '.' . $path;
        else
            $all = $path;

        if(isset(static::$_cache[$all]) && false === $load)
            return $this;

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

            if(isset(static::$_cache[$all]) && false === $load)
                return $this;

            static::$_cache[$all] = true;
            $uncache[]            = $all;
        }

        if(false !== strpos($all, '*')) {

            $backup     = $explode[0];
            $explode[0] = $root;
            $countFrom  = strlen($root) + 1;
            $glob       = glob(implode('/', $explode) . '.php');

            if(empty($glob)) {

                foreach($uncache as $un)
                    unset(static::$_cache[$un]);

                throw new \Hoa\Core\Exception\Idle(
                    'File %s does not exist.', 1, implode('/', $explode));
            }

            foreach($glob as $value)
                try {

                    $this->_import(
                        substr(
                            str_replace('/', '.', substr($value, 0, -4)),
                            $countFrom
                        ),
                        $load,
                        $from,
                        $root
                    );
                }
                catch ( \Hoa\Core\Exception\Idle $e ) {

                    foreach($uncache as $un)
                        unset(static::$_cache[$un]);

                    throw $e;
                }

            return $this;
        }

        if(false === $edited)
            $parts = $explode;

        $count    = count($parts);
        $backup   = $parts[0];
        $parts[0] = $root;
        $path     = implode('/',  $parts) . '.php';

        if(!file_exists($path)) {

            $parts[] = $parts[$count - 1];
            $path    = implode('/',  $parts) . '.php';
            ++$count;

            if(!file_exists($path)) {

                foreach($uncache as $un)
                    unset(static::$_cache[$un]);

                array_pop($parts);
                throw new \Hoa\Core\Exception\Idle(
                    'File %s does not exist.', 2, implode('/', $parts) . '.php');
            }
        }

        $parts[0] = $backup;
        $entry    = $parts[$count - 2] == $parts[$count - 1];
        $class    = implode('\\', $parts);
        $alias    = false;

        if(true === $entry) {

            array_pop($parts);
            $alias                  = implode('\\', $parts);
            static::$_class[$alias] = $class;
            $this->__class[$alias]  = &static::$_class[$alias];
        }

        static::$_class[$class]  = array(
            'path'     => $path,
            'alias'    => $alias,
            'imported' => false
        );
        $this->__class[$class] = &static::$_class[$class];

        if(false === $load)
            return $this;

        require $path;

        static::$_class[$class]['imported'] = true;

        if(true === $entry)
            class_alias($class, $alias);

        return $this;
    }

    /**
     * Set the root of the current library family.
     *
     * @access  public
     * @param   bool    $root    Root.
     * @param   string  $from    Library family's name (if null, first family
     *                           will be choosen).
     * @return  \Hoa\Consistency
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
     * Obvsiouly, we can combine options:
     *     [['load', 'root' => 'new/root']]
     *
     * @access  public
     * @param   mixed  $options    Options.
     * @return  \Hoa\Consistency
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
        $classes   = static::getAllImportedClasses();

        if(!isset($classes[$classname])) {

            $trace = debug_backtrace();

            if('unserialize' !== @$trace[2]['function'])
                return false;

            $head = trim(str_replace(
                        '\\',
                        '.',
                        substr($classname, 0, $pos = strpos($classname, '\\'))
                    ), '()');
            $tail = substr($classname, $pos + 1);

            static::from($head)
                ->import(str_replace('\\', '.', $tail), true);

            return true;
        }

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

        if(!class_exists($classname, false)) {

            $head = trim(str_replace(
                        '\\',
                        '.',
                        substr($classname, 0, $pos = strpos($classname, '\\'))
                    ), '()');
            $tail = substr($classname, $pos + 1);

            static::from($head)
                ->import(str_replace('\\', '.', $tail), true, $family);

            $classname = $family . '\\' . $tail;
        }

        $class = new \ReflectionClass($classname);

        if(empty($arguments) || false === $class->hasMethod('__construct'))
            return $class->newInstance();

        return $class->newInstanceArgs($arguments);
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
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
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
    public function distributesArguments ( Array $arguments ) {

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
    public function getValidCallback ( Array &$arguments ) {

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
 * Make the alias automatically (because it's not imported with the import()
 * function).
 */
class_alias('Hoa\Core\Consistency\Consistency', 'Hoa\Core\Consistency');

/**
 * Set autoloader.
 */
spl_autoload_register('\Hoa\Core\Consistency::autoload');

/**
 * Alias for \Hoa\Core\Consistency::from().
 *
 * @access  public
 * @param   string  $from    Library family's name.
 * @return  \Hoa\Consistency
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
 * @param   mixed  $callable    Callable (two parts).
 * @param   ...    ...          Arguments.
 * @return  \Closure
 */
if(!ƒ('curry_ref')) {
function curry_ref ( $callable, &$a = null, &$b = null, &$c = null, &$d = null,
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

    return function ( ) use ( $callable, &$arguments, $ii ) {

        return call_user_func_array(
            $callable,
            array_replace($arguments, array_combine($ii, func_get_args()))
        );
    };
}}

}
