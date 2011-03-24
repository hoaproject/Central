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
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
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
