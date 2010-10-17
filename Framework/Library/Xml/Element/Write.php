<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * @subpackage  Hoa_Xml_Element_Write
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Hoa_Xml_Element_Basic
 */
import('Xml.Element.Basic') and load();

/**
 * Hoa_Stream_Io_Out
 */
import('Stream.Io.Out') and load();

/**
 * Hoa_StringBuffer_ReadWrite
 */
import('StringBuffer.ReadWrite');

/**
 * Class Hoa_Xml_Element_Write.
 *
 * Write a XML element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Element_Write
 */

class          Hoa_Xml_Element_Write
    extends    Hoa_Xml_Element_Basic
    implements Hoa_Stream_Io_Out {

    /**
     * Write n characters.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throw   Hoa_Xml_Exception
     */
    public function write ( $string, $length ) {

        if($length <= 0)
            throw new Hoa_Xml_Exception(
                'Length must be greather than 0, given %d.', 0, $length);

        if(null === parent::$_buffer) {

            parent::$_buffer = new Hoa_StringBuffer_ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        $l = parent::$_buffer->write($string, $length);

        if($l !== $length)
            return false;

        $this[0] = parent::$_buffer->readAll();

        return $l;
    }

    /**
     * Write a string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString ( $string ) {

        $string = (string) $string;

        return $this->write($string, strlen($string));
    }

    /**
     * Write a character.
     *
     * @access  public
     * @param   string  $char    Character.
     * @return  mixed
     */
    public function writeCharacter ( $char ) {

        return $this->write((string) $char[0], 1);
    }

    /**
     * Write a boolean.
     *
     * @access  public
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean ( $boolean ) {

        return $this->write((string) (bool) $boolean, 1);
    }

    /**
     * Write an integer.
     *
     * @access  public
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger ( $integer ) {

        $integer = (string) (int) $integer;

        return $this->write($integer, strlen($integer));
    }

    /**
     * Write a float.
     *
     * @access  public
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat ( $float ) {

        $float = (string) (float) $float;

        return $this->write($float, strlen($float));
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray ( Array $array ) {

        foreach($array as $element => $value)
            if(is_array($value)) {

                foreach($value as $i => $in)
                    if(is_array($in) && is_int($i)) {
                        
                        $handle = $this->addChild($element);
                        $handle->writeArray($in);
                    }
                    elseif(is_int($i))
                        $handle = $this->addChild($element, $in);
                    else
                        $handle = $this->addChild($i, $in);

                if(array_key_exists('@attributes', $value))
                    $handle->writeAttributes($value['@attributes']);
            }
            else
                $this->addChild($element, $value);

        return;
    }

    /**
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine ( $line ) {

        if(false === $n = strpos($line, "\n"))
            return $this->write($line . "\n", strlen($line) + 1);

        $n++;

        return $this->write(substr($line, 0, $n), $n);
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll ( $string ) {

        return $this->write($string, strlen($string));
    }

    /**
     * Truncate to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate ( $size ) {

        return parent::$_buffer->truncate($size);
    }

    /**
     * Write a DOM tree.
     *
     * @access  public
     * @param   DOMNode  $dom    DOM tree.
     * @return  mixed
     */
    public function writeDOM ( DOMNode $dom ) {

        $sx = simplexml_import_dom($dom, __CLASS__);

        throw new Hoa_Xml_Exception(
            'Hmm, TODO?', 42);

        return true;
    }

    /**
     * Write attributes.
     * If an attribute does not exist, it will be created.
     *
     * @access  public
     * @param   array   $attributes    Attributes.
     * @return  void
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
     * @access  public
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  void
     */
    public function writeAttribute ( $name, $value ) {

        $this[$name] = $value;

        return;
    }
}
