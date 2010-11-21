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
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Attribute
 *
 */

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Class Hoa_Xml_Attribute.
 *
 * Parse and manipulate attributes.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Attribute
 */

class Hoa_Xml_Attribute {

    /**
     * List of attributes.
     *
     * @var Hoa_Xml_Attribute array
     */
    protected $_attributes = array();




    /**
     * Parse a string as attributes.
     *
     * @access  public
     * @param   string  $string    String that might represent attributes.
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( $string ) {

        $out = preg_match_all(
            '#(\w+)\s*(=\s*(?<!\\\)(?:("|\')|)(?(3)(.*?)(?<!\\\)\3|(\w+))\s*)?#',
            trim($string),
            $attributes,
            PREG_SET_ORDER
        );

        if(0 === $out)
            throw new Hoa_Xml_Exception(
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
