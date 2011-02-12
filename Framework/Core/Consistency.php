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
 */

namespace Hoa\Core {

/**
 * Class Hoa\Core\Consistency.
 *
 * This class manages all classes, importations etc.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Consistency {

    /**
     * One singleton by library family.
     *
     * @var \Hoa\Consistency array
     */
    private static $_multiton = array();

    /**
     * Library to considere.
     *
     * @var \Hoa\Consistency string
     */
    protected $_from          = 'Hoa';

    /**
     * Library's root to considere.
     *
     * @var \Hoa\Consistency string
     */
    protected $_root          = null;

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
     * Singleton to manage a library family.
     *
     * @access  public
     * @param   string  $from    Library family's name.
     * @return  void
     */
    private function __construct ( $from ) {

        $this->_from = $from;
        $this->_root = Core::getInstance()->getFormattedParameter(
                           'namespace.prefix.' . $from
                       ) ?: '/Flatland';

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

        if(!isset(self::$_multiton[$namespace]))
            self::$_multiton[$namespace] = new self($namespace);

        return self::$_multiton[$namespace];
    }

    /**
     * Import, i.e. pre-load, one or many classes. If $load parameters is set
     * to true, then pre-load is turned to direct-load.
     *
     * @access  public
     * @param   string  $path    Path.
     * @param   bool    $load    Whether loading directly or not.
     * @return  \Hoa\Consistency
     */
    public function import ( $path, $load = false ) {

        if(!empty($this->_from))
            $all = $this->_from . '.' . $path;
        else
            $all = $path;

        if(isset(self::$_cache[$all]) && false === $load)
            return $this;

        self::$_cache[$all] = true;
        $edited             = false;
        $explode            = explode('.', $all);
        $parts              = array();

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

            if(isset(self::$_cache[$all]) && false === $load)
                return $this;

            self::$_cache[$all] = true;
        }

        if(false !== strpos($all, '*')) {

            $backup     = $explode[0];
            $explode[0] = $this->_root;
            $countFrom  = strlen($this->_root) + 1;

            foreach(glob(implode('/', $explode) . '.php') as $value)
                $this->import(substr(
                    str_replace('/', '.', substr($value, 0, -4)),
                    $countFrom
                ), $load);

            self::$_cache[$all] = true;

            return $this;
        }

        if(false === $edited)
            $parts = $explode;

        $count    = count($parts);
        $backup   = $parts[0];
        $parts[0] = $this->_root;
        $path     = implode('/',  $parts) . '.php';

        if(!file_exists($path)) {

            $parts[] = $parts[$count - 1];
            $path    = implode('/',  $parts) . '.php';
            ++$count;

            if(!file_exists($path)) {

                array_pop($parts);
                throw new Exception(
                    'File %s does not exist.', 0, implode('/', $parts) . '.php');
            }
        }

        $parts[0] = $backup;
        $entry    = $parts[$count - 2] == $parts[$count - 1];
        $class    = implode('\\', $parts);
        $alias    = false;

        if(true === $entry) {

            array_pop($parts);
            $alias                 = implode('\\', $parts);
            self::$_class[$alias]  = $class;
            $this->__class[$alias] = &self::$_class[$alias];
        }

        self::$_class[$class]  = array(
            'path'     => $path,
            'alias'    => $alias,
            'imported' => false
        );
        $this->__class[$class] = &self::$_class[$class];

        if(false === $load)
            return $this;

        require $path;

        self::$_class[$class]['imported'] = true;

        if(true === $entry)
            class_alias($class, $alias);

        return $this;
    }

    /**
     * Set the root of the current library family.
     *
     * @access  public
     * @param   bool    $root    Root.
     * @return  \Hoa\Consistency
     */
    public function setRoot ( $root ) {

        $this->_root = $root;

        return $this;
    }

    /**
     * Get the root of the current library family.
     *
     * @access  public
     * @return  string
     */
    public function getRoot ( ) {

        return $this->_root;
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

        return self::$_class;
    }

    /**
     * Get the shortest name for a class, i.e. if an alias exists, return it,
     * else return the normal classname.
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @return  string
     * @throw   \Hoa\Core\Exception
     */
    public static function getClassShortestName ( $classname ) {

        if(!isset(self::$_class[$classname]))
            throw new Exception(
                'Class %s does not exist.', 1, $classname);

        if(is_string(self::$_class[$classname]))
            return $classname;

        return self::$_class[$classname]['alias'] ?: $classname;
    }

    /**
     * Autoloader.
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @return  bool
     */
    public static function autoload ( $classname ) {

        $classes = self::getAllImportedClasses();

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
     * Dynamic new, i.e. a little native factory (import + load + instance).
     *
     * @access  public
     * @param   string  $classname    Classname.
     * @param   array   $arguments    Constructor's arguments.
     * @return  object
     */
    public static function dnew ( $classname, Array $arguments = array() ) {

        if(!class_exists($classname))
            if(false === self::autoload($classname)) {

                $path = str_replace('\\', '.', $classname);
                self::from(substr($path, 0, strpos($path, '.')))
                    ->import(substr($path, strpos($path, '.') + 1), true);
            }

        $class = new \ReflectionClass($classname);

        return $class->newInstanceArgs($arguments);
    }
}

}

namespace {

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
 * Set autoloader.
 */
spl_autoload_register('\Hoa\Core\Consistency::autoload');

}
