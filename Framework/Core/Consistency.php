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

    private static $_multiton = array();
    protected $_from          = 'Hoa';
    protected $_root          = '/Users/hywan/Development/Hoa/Project/Framework/Library/';
    protected static $_cache  = array();
    protected static $_class  = array();
    protected $__class        = array();

    private function __construct ( $from ) {

        $this->_from = $from;

        print_r(\Hoa\Core::getInstance()->getParameters());

        return;
    }

    public static function from ( $namespace ) {

        if(!isset(self::$_multiton[$namespace]))
            self::$_multiton[$namespace] = new self($namespace);

        return self::$_multiton[$namespace];
    }

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
                var_dump('FILE DOES NOT EXIST! ' . implode('/', $parts) . '.php');

                return;
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

    public function setRoot ( $root ) {

        $this->_root = $root;

        return $this;
    }

    public function getRoot ( ) {

        return $this->_root;
    }

    public function getImportedClasses ( ) {

        return $this->__class;
    }

    public static function getAllImportedClasses ( ) {

        return self::$_class;
    }

    public static function getClassShortestName ( $class ) {

        if(!isset(self::$_class[$class]))
            throw new Exception(
                'Class %s does not exist.', 0, $class);

        if(is_string(self::$_class[$class]))
            return $class;

        return self::$_class[$class]['alias'] ?: '';
    }

    public static function autoload ( $classname ) {

        $classes = self::getAllImportedClasses();

        if(!isset($classes[$classname]))
            return false;

        $class = &$classes[$classname];

        if(is_string($class)) {

            $classname = $class;
            $class     = &$classes[$class];
        }

        require_once $class['path'];

        $class['imported'] = true;

        if(false !== $class['alias'])
            class_alias($classname, $class['alias']);

        return true;
    }

    public static function dnew ( $classname, Array $arguments = array() ) {

        if(!class_exists($classname))
            if(false === self::autoload($classname))
                self::from('')
                    ->import(str_replace('\\', '.', $classname), true);

        $class = new \ReflectionClass($classname);

        return $class->newInstanceArgs($arguments);
    }
}

}

namespace {

/**
 *
 */
if(!ƒ('from')) {
function from ( $namespace ) {

    return \Hoa\Core\Consistency::from($namespace);
}}

/**
 *
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
