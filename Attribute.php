<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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

from('Hoa')

/**
 * \Hoa\Xml\Exception
 */
-> import('Xml.Exception.~');

}

namespace Hoa\Xml {

/**
 * Class \Hoa\Xml\Attribute.
 *
 * Parse and manipulate attributes.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Attribute {

    /**
     * List of attributes.
     *
     * @var \Hoa\Xml\Attribute array
     */
    protected $_attributes = array();




    /**
     * Parse a string as attributes.
     *
     * @access  public
     * @param   string  $string    String that might represent attributes.
     * @return  void
     * @throw   \Hoa\Xml\Exception
     */
    public function __construct ( $string = null ) {
        if (null === $string) {
            return;
        }

        $out = preg_match_all(
            '#(\w+)\s*(=\s*(?<!\\\)(?:("|\')|)(?(3)(.*?)(?<!\\\)\3|(\w+))\s*)?#',
            trim($string),
            $attributes,
            PREG_SET_ORDER
        );

        if(0 === $out)
            throw new Exception(
                'The string %s does not represent attributes.', 0, $string);

        foreach($attributes as $i => $attribute)
            // Boolean: abc
            if(!isset($attribute[2]))
                $this->_attributes[$attribute[1]] = $attribute[1];

            // Quote: abc="def" or abc='def'
            elseif(!isset($attribute[5]))
                $this->_attributes[$attribute[1]] = str_replace(
                    '\\' . $attribute[3],
                    $attribute[3],
                    $attribute[4]
                );

            // No-quote: abc=def
            else
                $this->_attributes[$attribute[1]] = $attribute[5];
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        return $this->_attributes;
    }

    /**
     * Read a specific attribute.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute ( $name ) {

        if(false === $this->attributeExists($name))
            return null;

        return $this->_attributes[$name];
    }

    /**
     * Whether an attribute exists.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists ( $name ) {

        return true === array_key_exists($name, $this->_attributes);
    }

    /**
     * Read attributes value as a list.
     *
     * @access  public
     * @return  array
     */
    public function readAttributesAsList ( ) {

        $attributes = $this->_attributes;

        foreach($attributes as $name => &$value)
            $value = explode(' ', $value);

        return $attributes;
    }

    /**
     * Read a attribute value as a list.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  array
     */
    public function readAttributeAsList ( $name ) {

        return explode(' ', $this->readAttribute($name));
    }

    /**
     * Write attributes.
     * If an attribute does not exist, it will be created.
     *
     * @access public
     * @param array $attributes Attributes.
     * @return void
     */
    public function writeAttributes ( Array $attributes ) {

        foreach($attributes as $name => $value)
            $this->writeAttribute($name, $value);

        return;
    }

    /**
     * Write an attribute.
     * If the attribute does not exist, it will be created.
     *
     * @access public
     * @param string $name Name.
     * @param string $value Value.
     * @return void
     */
    public function writeAttribute ( $name, $value ) {

        $this->_attributes[$name] = $value;

        return;
    }

    /**
     * Remove an attribute.
     *
     * @access public
     * @param string $name Name.
     * @return void
     */
    public function removeAttribute ( $name ) {

        unset($this->_attributes[$name]);

        return;
    }

    /**
     * Format attributes.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = null;

        foreach($this->_attributes as $key => $value)
            $out .= $key . '="' . str_replace('"', '\"', $value) . '" ';

        return substr($out, 0, -1);
    }
}

}
