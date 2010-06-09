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
 * @subpackage  Hoa_Xml_Read
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Hoa_Xml
 */
import('Xml.~');

/**
 * Hoa_Stream_Io_In
 */
import('Stream.Io.In');

/**
 * Class Hoa_Xml_Read.
 *
 * File handler.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Read
 */

class          Hoa_Xml_Read
    extends    Hoa_Xml
    implements Hoa_Stream_Io_In {

    const NODE_ATTRIBUTE              = XMLReader::ATTRIBUTE;

    const NODE_CDATA                  = XMLReader::CDATA;

    const NODE_COMMENT                = XMLReader::COMMENT;

    const NODE_DOCUMENT               = XMLReader::DOC;

    const NODE_DOCUMENT_TYPE          = XMLReader::DOC_TYPE;

    const NODE_DOCUMENT_FRAGMENT      = XMLReader::DOC_FRAGMENT;

    const NODE_ELEMENT                = XMLReader::ELEMENT;

    const NODE_ELEMENT_END            = XMLReader::END_ELEMENT;

    const NODE_ENTITY                 = XMLReader::ENTITY;

    const NODE_ENTITY_END             = XMLReader::END_ENTITY;

    const NODE_ENTITY_REFERENCE       = XMLReader::ENTITY_REF;

    const NODE_NONE                   = XMLReader::NONE;

    const NODE_NOTATION               = XMLReader::NOTATION;

    const NODE_PROCESSING_INSTRUCTION = XMLReader::PI;

    const NODE_TEXT                   = XMLReader::TEXT;

    const NODE_WHITESPACE             = XMLReader::WHITESPACE;

    const NODE_WHITESPACE_SIGNIFICANT = XMLReader::SIGNIFICANT_WHITESPACE;

    const NODE_XML_DECLARATION        = XMLReader::XML_DECLARATION;

    private $_eof = false;

    public function __construct ( Hoa_Stream_Io_In $stream ) {

        parent::__construct(new XMLReader(), $stream);
    }

    public function getDepth ( ) {

        return $this->getStream()->depth;
    }

    public function hasAttributes ( ) {

        return $this->getStream()->hasAttributes;
    }

    public function hasValue ( ) {

        return $this->getStream()->hasValue;
    }

    public function isValueDefault ( ) {

        return $this->getStream()->isDefault;
    }

    public function getValue ( ) {

        return $this->getStream()->value;
    }

    public function isEmpty ( ) {

        return $this->getStream()->isEmptyElement;
    }

    public function getLocalName ( ) {

        return $this->getStream()->localName;
    }

    public function getName ( ) {

        return $this->getStream()->name;
    }

    public function getNamespaceURI ( ) {

        return $this->getStream()->namespaceURI;
    }

    public function getNodeType ( ) {

        return $this->getStream()->nodeType;
    }

    public function getNodeTypeAsString ( ) {

        static $_names = array(
            self::NODE_ATTRIBUTE              => 'ATTRIBUTE',
            self::NODE_CDATA                  => 'CDATA',
            self::NODE_COMMENT                => 'COMMENT',
            self::NODE_DOCUMENT               => 'DOCUMENT',
            self::NODE_DOCUMENT_TYPE          => 'DOCUMENT_TYPE',
            self::NODE_DOCUMENT_FRAGMENT      => 'DOCUMENT_FRAGMENT',
            self::NODE_ELEMENT                => 'ELEMENT',
            self::NODE_ELEMENT_END            => 'ELEMENT_END',
            self::NODE_ENTITY                 => 'ENTITY',
            self::NODE_ENTITY_END             => 'ENTITY_END',
            self::NODE_ENTITY_REFERENCE       => 'ENTITY_REFERENCE',
            self::NODE_NONE                   => 'NONE',
            self::NODE_NOTATION               => 'NOTATION',
            self::NODE_PROCESSING_INSTRUCTION => 'PROCESSING_INSTRUCTION',
            self::NODE_TEXT                   => 'TEXT',
            self::NODE_WHITESPACE             => 'WHITESPACE',
            self::NODE_WHITESPACE_SIGNIFICANT => 'WHITESPACE_SIGNIFICANT',
            self::NODE_XML_DECLARATION        => 'XML_DECLARATION'
        );

        return $_names[$this->getNodeType()];
    }

    public function getPrefix ( ) {

        return $this->getStream()->prefix;
    }

    public function getLang ( ) {

        return $this->getStream()->xmlLang;
    }

    public function next ( ) {

        if(true === $this->_eof)
            return false;

        $this->_eof = false === $this->getStream()->read();

        return true;
    }

    public function nextSibling ( ) {

        if(true === $this->_eof)
            return false;
                
        $this->_eof = false === $this->getStream()->next();

        return true;
    }

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

        return substr(trim($this->getStream()->readString()), 0, $length);
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

        return (int) $this->read(1);
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
     * Read an array.
     * Alias of the $this->scanf() method.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function readArray ( $format ) {

        return $this->scanf($format);
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

        return trim($this->getStream()->readString());
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

    public function readDOM ( ) {

        return $this->getStream()->expand();
    }

    public function readAttribute ( $name, $namespace = null ) {

        if(null === $namespace)
            return $this->getStream()->getAttribute($name);

        return $this->getStream()->getAttributeNs($name, $namespace);
    }

    public function readAttributes ( $namespace = null ) {

        if(false === $this->hasAttributes())
            return null;

        $i   = 0;
        $out = array();

        while('' !== $this->getStream()->getAttributeNo($i))
            $out[$i] = $this->getStream()->getAttributeNo($i++);

        return $out;
    }

    public function moveToElement ( $localName = null ) {

        if(null !== $localName) {

            do {

                $this->next();
            } while(   !$this->eof()
                    && (self::NODE_ELEMENT !== $this->getNodeType()
                    ||  $localName         !== $this->getLocalName()));

            return self::NODE_ELEMENT === $this->getNodeType();
        }

        return false;
    }

    public function moveToSiblingElement ( $localName = null ) {

        $this->nextSibling();
        return $this->moveToElement($localName);
    }
}
