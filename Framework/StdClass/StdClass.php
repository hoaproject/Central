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
 * @package     Hoa_StdClass
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_StdClass_Exception
 */
import('StdClass.Exception');

/**
 * Class Hoa_StdClass.
 *
 * Manipulate StdClass, convert array to StdClass etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_StdClass
 */

class Hoa_StdClass implements Iterator, Countable, Serializable {

    protected $_array  = array();
    protected $_object = null;



    public function __construct ( $array ) {

        $this->_array = $array;
        $this->transform($this->_array);
    }

    protected function transform ( $array = array() ) {

        if(empty($array))
            $array = $this->_array;

        if(!is_array($array)) {

            $this->_object[$array] = $array;
            return;
        }

        foreach($array as $key => $value) {

            $key = str_replace('.', '_', $key);

            if(!preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $key))
                throw new Hoa_StdClass_Exception(
                    'Variable %s is not well formed.', 0, $key);
            $this->_object[$key] = new Hoa_StdClass($value);
        }
    }

    public function __set ( $name, $value ) {

        $this->_object[$name] = new Hoa_StdClass($value);
    }

    public function __get ( $name ) {

        if(isset($this->_object[$name]))
            return $this->_object[$name];

        return null;
    }

    public function __isset ( $name ) {

        return isset($this->_object[$name]);
    }

    public function __unset ( $name ) {

        unset($this->_object[$name]);
    }

    public function __toString ( ) {

        static $i = 0;

        if(is_string(current($this->_object)))
            return current($this->_object);

        $return = 'Hoa_StdClass (' . "\n";
        foreach($this->_object as $key => $value) {

            $i++;
            $return .= str_repeat("\t", $i) . '[' . $key . '] => ' . $value . "\n";
            $i--;
        }
        $return .= str_repeat("\t", $i) . ')';

        return $return;
    }

    public function toArray ( ) {

        return $this->_array;
    }

    public function toString ( ) {

        return key($this->_object);
    }

    public function toInt ( ) {

        return (int) $this->toString();
    }

    public function toBool ( ) {

        return (bool) $this->toString();
    }

    public function current ( ) {

        return current($this->_object);
    }

    public function key ( ) {

        return key($this->_object);
    }

    public function next ( ) {

        return next($this->_object);
    }

    public function rewind ( ) {

        return reset($this->_object);
    }

    public function valid ( ) {

        if(empty($this->_object))
            return false;

        $key    = key($this->_object);
        $return = (next($this->_object) ? true : false);
        prev($this->_object);

        if(false === $return) {

            end($this->_object);
            if($key === key($this->_object))
                $return = true;
        }

        return $return;
    }

    public function count ( ) {

        return count($this->_object);
    }

    public function serialize ( ) {

        return serialize($this->_object);
    }

    public function unserialize ( $serialized ) {

        return $this->_object = unserialize($serialized);
    }
}
