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
 * @subpackage  Hoa_Xml_ReadWrite
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
 * Hoa_Xml
 */
import('Xml.~');

/**
 * Hoa_Stream_Io
 */
import('Stream.Io');

/**
 * Hoa_Xml_Element_ReadWrite
 */
import('Xml.Element.ReadWrite');

/**
 * Class Hoa_Xml_ReadWrite.
 *
 * Read/write a XML element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_ReadWrite
 */

class          Hoa_Xml_ReadWrite
    extends    Hoa_Xml
    implements Hoa_Stream_Io {

    /**
     * Start the stream reader/writer as if it is a XML document.
     *
     * @access  public
     * @param   Hoa_Stream_Io  $stream    Stream to read/write.
     * @return  void
     */
    public function __construct ( Hoa_Stream_Io $stream ) {

        parent::__construct('Hoa_Xml_Element_ReadWrite', $stream);

        event('hoa://Event/Stream/' . $stream->getStreamName() . ':close-before')
            ->attach($this, '_close');

        return;
    }

    /**
     * Do not use this method. It is called from the
     * hoa://Event/Stream/...:close-before event.
     * It transforms the XML tree as a XML string, truncates the stream to zero
     * and writes all this string into the stream.
     *
     * @access  public
     * @param   Hoa_Core_Event_Bucket  $bucket    Event's bucket.
     * @return  void
     */
    public function _close ( Hoa_Core_Event_Bucket $bucket ) {

        $handle = $this->getStream()->selectRoot()->asXML();

        if(true === $this->getInnerStream()->truncate(0))
            $this->getInnerStream()->writeAll($handle);

        return;
    }

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @return  bool
     */
    public function eof ( ) {

        return $this->getStream()->eof();
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

        return $this->getStream()->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString ( $length ) {

        return $this->getStream()->readString($length);
    }

    /**
     * Read a character.
     *
     * @access  public
     * @return  string
     */
    public function readCharacter ( ) {

        return $this->getStream()->readCharacter();
    }

    /**
     * Read a boolean.
     *
     * @access  public
     * @return  bool
     */
    public function readBoolean ( ) {

        return $this->getStream()->readBoolean();
    }

    /**
     * Read an integer.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger ( $length = 1 ) {

        return $this->getStream()->readInteger($length);
    }

    /**
     * Read a float.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat ( $length = 1 ) {

        return $this->getStream()->readFloat($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @access  public
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray ( $argument = null ) {

        return $this->getStream()->readArray($argument);
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        return $this->getStream()->readLine();
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        return $this->getStream()->readAll();
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf ( $format ) {

        return $this->getStream()->scanf($format);
    }

    /**
     * Read content as a DOM tree.
     *
     * @access  public
     * @return  DOMElement
     */
    public function readDOM ( ) {

        return $this->getStream()->readDOM();
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        return $this->getStream()->readAttributes();
    }

    /**
     * Read a specific attribute.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute ( $name ) {

        return $this->getStream()->readAttribute($name);;
    }

    /**
     * Read all with XML node.
     *
     * @access  public
     * @return  string
     */
    public function readXML ( ) {

        return $this->getStream()->readXML();
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

        return $this->getStream()->write($string, $length);
    }

    /**
     * Write a string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString ( $string ) {

        return $this->getStream()->writeString($string);
    }

    /**
     * Write a character.
     *
     * @access  public
     * @param   string  $char    Character.
     * @return  mixed
     */
    public function writeCharacter ( $char ) {

        return $this->getStream()->writeCharacter($char);
    }

    /**
     * Write a boolean.
     *
     * @access  public
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean ( $boolean ) {

        return $this->getStream()->writeBoolean($boolean);
    }

    /**
     * Write an integer.
     *
     * @access  public
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger ( $integer ) {

        return $this->getStream()->writeInteger($integer);
    }

    /**
     * Write a float.
     *
     * @access  public
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat ( $float ) {

        return $this->getStream()->writeFloat($float);
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray ( Array $array ) {

        return $this->getStream()->writeArray($array);
    }

    /**
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine ( $line ) {

        return $this->getStream()->writeLine($line);
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll ( $string ) {

        return $this->getStream()->writeAll($string);
    }

    /**
     * Truncate to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate ( $size ) {

        return $this->getStream()->truncate($size);
    }

    /**
     * Write a DOM tree.
     *
     * @access  public
     * @param   DOMNode  $dom    DOM tree.
     * @return  mixed
     */
    public function writeDOM ( DOMNode $dom ) {

        return $this->getStream()->writeDOM($dom);
    }

    /**
     * Write attributes.
     *
     * @access  public
     * @param   array   $attributes    Attributes.
     * @return  void
     */
    public function writeAttributes ( Array $attributes ) {

        return $this->getStream()->writeAttributes($attributes);
    }

    /**
     * Write an attribute.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  void
     */
    public function writeAttribute ( $name, $value ) {

        return $this->getStream()->writeAttribute($name, $value);
    }
}
