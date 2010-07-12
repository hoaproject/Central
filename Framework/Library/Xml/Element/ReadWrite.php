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
 * @subpackage  Hoa_Xml_Element_ReadWrite
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
 * Hoa_Xml_Element
 */
import('Xml.Element');

/**
 * Hoa_Stream_Io
 */
import('Stream.Io');

/**
 * Hoa_StringBuffer_ReadWrite
 */
import('StringBuffer.ReadWrite');

/**
 * Class Hoa_Xml_Element_ReadWrite.
 *
 * Read/write a XML element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Element_ReadWrite
 */

class          Hoa_Xml_Element_ReadWrite
    extends    Hoa_Xml_Element
    implements Hoa_Stream_Io {

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @return  bool
     */
    public function eof ( ) {

        return $this->_eof;
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function read ( $length ) {

        if($length <= 0)
            throw new Hoa_Xml_Exception(
                'Length must be greather than 0, given %d.', 0, $length);

        if(null === parent::$_buffer) {

            parent::$_buffer = new Hoa_StringBuffer_ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        return parent::$_buffer->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString ( $length ) {

        return $this->read($length);
    }

    /**
     * Read a character.
     *
     * @access  public
     * @return  string
     */
    public function readCharacter ( ) {

        return $this->read(1);
    }

    /**
     * Read a boolean.
     *
     * @access  public
     * @return  bool
     */
    public function readBoolean ( ) {

        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger ( $length = 1 ) {

        return (int) $this->read($length);
    }

    /**
     * Read a float.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat ( $length = 1 ) {

        return (float) $this->read($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @access  public
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray ( $argument = null ) {

        return (array) $this;
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        $handle = $this->readAll();
        $n      = strpos($handle, "\n");

        if(false === $n)
            return $handle;

        return substr($handle, 0, $n);
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        return $this->__toString();
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf ( $format ) {

        return sscanf($this->readAll(), $format);
    }

    /**
     * Read content as a DOM tree.
     *
     * @access  public
     * @return  DOMElement
     */
    public function readDOM ( ) {

        return dom_import_simplexml($this);
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        $handle = (array) $this->attributes();

        return $handle['@attributes'];
    }

    /**
     * Read a specific attribute.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute ( $name ) {

        $attributes = $this->readAttributes();

        if(false === array_key_exists($name, $attributes))
            return null;

        return $attributes[$name];
    }

    /**
     * Read all with XML node.
     *
     * @access  public
     * @return  string
     */
    public function readXML ( ) {

        return $this->asXML();
    }

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

        $sx = simplexml_import_dom($dom, get_class($this));

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
