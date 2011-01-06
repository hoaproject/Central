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
 * @package     Hoa_StdClass
 *
 */

/**
 * Hoa_StdClass_Exception
 */
import('StdClass.Exception');

/**
 * Class Hoa_StdClass.
 *
 * Alternative to StdClass: more possibilities in manipulation.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_StdClass
 */

class Hoa_StdClass implements Iterator, Countable, Serializable, ArrayAccess {

    /**
     * Data.
     *
     * @var Hoa_StdClass array
     */
    protected $_data = array();



    /**
     * Build the standard class.
     *
     * @access  public
     * @param   mixed   $array    Array that contains values.
     * @return  void
     */
    public function __construct ( $array = array() ) {

        $this->transform($array);
    }

    /**
     * Transform an array to a standard class.
     *
     * @access  public
     * @param   mixed   $array    Array that contains values.
     * @return  void
     */
    protected function transform ( $array = array() ) {

        if(!is_array($array)) {

            $this->_data[$array] = $array;

            return;
        }

        foreach($array as $key => $value)
            $this->_data[$key] = new Hoa_StdClass($value);

        return;
    }

    /**
     * Check if tree is empty.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty ( ) {

        return    false === $this->isRecursive()
               && false === $this->current();
    }

    /**
     * Dynamic setter.
     *
     * @access  public
     * @param   string  $name     Attribute name.
     * @param   mixed   $value    Attribute value.
     * @return  void
     */
    public function __set ( $name, $value ) {

        $this->_data[$name] = new Hoa_StdClass($value);

        return;
    }

    /**
     * Dynamic getter.
     *
     * @access  public
     * @param   string  $name    Attribute name.
     * @return  mixed
     */
    public function __get ( $name ) {

        if(!isset($this->_data[$name]))
            return null;

        return $this->_data[$name];
    }

    /**
     * Dynamic isset.
     *
     * @access  public
     * @param   string  $name    Attribute name.
     * @return  bool
     */
    public function __isset ( $name ) {

        return array_key_exists($name, $this->_data);
    }

    /**
     * Dynamic unset.
     *
     * @access  public
     * @param   string  $name    Attribute name.
     * @return  void
     */
    public function __unset ( $name ) {

        unset($this->_data[$name]);

        return;
    }

    /**
     * Transform object to string (echo $this is equivalent to
     * print_r(array(…))).
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        static $i = 0;

        if(false === $this->isRecursive()) {

            if(empty($this->_data))
                return 'array()';

            $handle = $this->current();

            if(true === $handle)
                $handle = 'true';
            elseif(false === $handle)
                $handle = 'false';
            elseif(null === $handle)
                $handle = 'null';

            return '' . $handle;
        }

        $out = 'Hoa_StdClass (' . "\n";

        foreach($this->_data as $key => $value) {

            $i++;
            $out .= str_repeat('    ', $i) . '[' . $key . '] => ' . $value . "\n";
            $i--;
        }

        $out .= str_repeat('    ', $i) . ')';

        $this->rewind();

        return $out;
    }

    /**
     * Transform object to array.
     *
     * @access  public
     * @return  array
     */
    public function toArray ( ) {

        if(false === $this->isRecursive()) {

            if(empty($this->_data))
                return $this->_data;

            return $this->current();
        }

        $out = array();

        foreach($this->_data as $key => $value)
            $out[$key] = $value->toArray();

        $this->rewind();

        return $out;
    }

    /**
     * Transform to string.
     *
     * @access  public
     * @return  string
     */
    public function toString ( ) {

        return $this->__toString();
    }

    /**
     * Transform to integer.
     *
     * @access  public
     * @return  int
     */
    public function toInteger ( ) {

        if(false === $this->isRecursive())
            return (int) $this->current();

        return 0;
    }

    /**
     * Transform to float.
     *
     * @access  public
     * @return  float
     */
    public function toFloat ( ) {

        if(false === $this->isRecursive())
            return (float) $this->current();

        return 0.0;
    }

    /**
     * Transform to bool.
     *
     * @access  public
     * @return  bool
     */
    public function toBoolean ( ) {

        if(false === $this->isRecursive())
            return (bool) $this->current();

        return true;
    }

    /**
     * Transform to JSON.
     *
     * @access  public
     * @param   mixed   $dummy    Dummy argument (to be compatible with
     *                            Hoa_Json::toJson() method). Not used in this
     *                            method.
     * @return  string
     * @throw   Hoa_StdClass_Exception
     */
    public function toJson ( $dummy = null ) {

        if(false === function_exists('json_encode'))
            if(PHP_VERSION_ID < 50200)
                throw new Hoa_StdClass_Exception(
                    'JSON extension is available since PHP 5.2.0; ' .
                    'current version is %.', 0, PHP_VERSION);
            else
                throw new Hoa_StdClass_Exception(
                    'JSON extension is disabled.', 1);

        return json_encode($this->toArray());
    }

    /**
     * Get the current collection for the iterator.
     *
     * @access  public
     * @return  mixed
     */
    public function current ( ) {

        return current($this->_data);
    }

    /**
     * Get the current collection name for the iterator.
     *
     * @access  public
     * @return  mixed
     */
    public function key ( ) {

        return key($this->_data);
    }

    /**
     * Advance the internal collection pointer, and return the current
     * collection.
     *
     * @access  public
     * @return  mixed
     */
    public function next ( ) {

        return next($this->_data);
    }

    /**
     * Rewind the internal collection pointer, and return the first collection.
     *
     * @access  public
     * @return  mixed
     */
    public function rewind ( ) {

        return reset($this->_data);
    }

    /**
     * Check if there is a current element after calls to the rewind or the next
     * methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty($this->_data))
            return false;

        $key =  key ($this->_data);
        $out = (next($this->_data) ? true : false);
        prev($this->_data);

        if(false === $out) {

            end($this->_data);
            if($key === key($this->_data))
                $out = true;
        }

        return $out;
    }

    /**
     * Count number of elements in collection.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_data);
    }

    /**
     * Serialize this collection.
     *
     * @access  public
     * @return  string
     */
    public function serialize ( ) {

        return serialize($this->_data);
    }

    /**
     * Replace current collection by new unserialized collection.
     *
     * @access  public
     * @param   string  $serialized    Serialized collection.
     * @return  mixed
     */
    public function unserialize ( $serialized ) {

        return $this->_data = unserialize($serialized);
    }

    /**
     * Whether the offset exists.
     *
     * @access  public
     * @param   mixed   $offset    Offset to check.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return $this->__isset($offset);
    }

    /**
     * Value at given offset.
     *
     * @access  public
     * @param   mixed   $offset    Offset to retrive.
     * @return  mixed
     */
    public function offsetGet ( $offset ) {

        return $this->__get($offset);
    }

    /**
     * Set a new value to offset.
     *
     * @access  public
     * @param   mixed   $offset    Offset to modify.
     * @param   mixed   $value     New value.
     * @return  void
     */
    public function offsetSet ( $offset, $value ) {

        $this->__set($offset, $value);

        return;
    }

    /**
     * Unset an offset.
     *
     * @access  public
     * @param   mixed   $offset    Offset to unset.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        $this->__unset($offset);

        return;
    }

    /**
     * Check if we go a recursivity (i.e. a level down).
     *
     * @access  protected
     * @return  bool
     */
    protected function isRecursive ( ) {

        return $this->current() instanceof Hoa_StdClass;
    }
}
