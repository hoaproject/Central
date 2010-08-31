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
 * Hoa_Stream_Composite
 */
import('Stream.Composite');

/**
 * Hoa_Stream_Io_Structural
 */
import('Stream.Io.Structural');

/**
 * Class Hoa_Xml.
 *
 * 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xml
 */

abstract class Hoa_Xml
    extends    Hoa_Stream_Composite
    implements Hoa_Xml_Element,
               Hoa_Stream_Io_Structural,
               Countable,
               IteratorAggregate,
               ArrayAccess {

    /**
     * Constructor. Load the inner stream as a XML tree. If the inner stream is
     * empty (e.g. an empty new file), the XML tree will represent the following
     * XML code:
     *     <?xml version="1.0" encoding="utf-8"?>
     *
     *     <handler>
     *     </handler>
     *
     * @access  public
     * @param   string      $stream         Stream name to use.
     * @param   Hoa_Stream  $innerStream    Inner stream.
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( $stream, Hoa_Stream $innerStream ) {

        if(!function_exists('simplexml_load_file'))
            throw new Hoa_Xml_Exception(
                'SimpleXML must be enable for using %s.', 0, get_class($this));

        $streamName = $innerStream->getStreamName();
        $root       = @simplexml_load_file($streamName, $stream);

        if(false === $root) {

            if($innerStream instanceof Hoa_Stream_Io_In)
                $root = @simplexml_load_string($innerStream->readAll(), $stream);

            if(false === $root)
                $root = simplexml_load_string(
                    '<?xml version="1.0" encoding="utf-8"?' . ">\n\n" .
                    '<handler>' . "\n" . '</handler>',
                    $stream
                );
        }

        if(null === $root)
            throw new Hoa_Xml_Exception(
                'Failed to understand %s as a XML stream.',
                1, $streamName);

        $root->setSuperRoot($this);
        $root->setRoot($root);
        $this->setStream($root);
        $this->setInnerStream($innerStream);

        return;
    }

    /**
     * Select super root.
     *
     * @access  public
     * @return  Hoa_Xml
     */
    public function selectSuperRoot ( ) {

        return $this;
    }

    /**
     * Select root of the document: :root.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function selectRoot ( ) {

        return $this->getStream()->selectRoot();
    }

    /**
     * Select any element: *.
     *
     * @access  public
     * @return  array
     */
    public function selectAnyElement ( ) {

        return $this->getStream()->selectAnyElement();
    }

    /**
     * Select an element of type E: E.
     *
     * @access  public
     * @param   string  $E    Element E.
     * @return  array
     */
    public function selectElement ( $E = null ) {

        return $this->getStream()->selectElement($E);
    }

    /**
     * Select an F element descendant of an E element: E F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectDescendantElement ( $F = null ) {

        return $this->getStream()->selectDescendantElement($F);
    }

    /**
     * Select an F element child of an E element: E > F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectChildElement ( $F = null ) {

        return $this->getStream()->selectChildElement($F);
    }

    /**
     * Select an F element immediately preceded by an E element: E + F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  Hoa_Xml_Element
     */
    public function selectAdjacentSiblingElement ( $F ) {

        return $this->getStream()->selectAdjacentSiblingElement($F);
    }

    /**
     * Select an F element preceded by an E element: E ~ F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectSiblingElement ( $F = null ) {

        return $this->getStream()->selectSiblingElement($F);
    }

    /**
     * Execute a query selector and return the first result.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  Hoa_Xml_Element
     * @throw   Hoa_Compiler_Exception
     */
    public function querySelector ( $query ) {

        return $this->getStream()->querySelector($query);
    }

    /**
     * Execute a query selector and return one or many results.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  Hoa_Xml_Element
     * @throw   array
     */
    public function querySelectorAll ( $query ) {

        return $this->getStream()->querySelectorAll($query);
    }

    /**
     * Run a XPath query on this tree.
     *
     * @access  public
     * @param   string  $path    XPath query.
     * @return  array
     */
    public function xpath ( $path ) {

        return $this->getStream()->xpath($path);
    }

    /**
     * Transform this object to a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getStream()->__toString();
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        $handle = (array) $this->getStream()->attributes();

        if(!isset($handle['@attributes']))
            return array();

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
     * Whether an attribute exists.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists ( $name ) {

        return array_key_exists($name, $this->readAttributes());
    }

    /**
     * Read attributes value as a list.
     *
     * @access  public
     * @return  array
     */
    public function readAttributesAsList ( ) {

        $attributes = $this->readAttributes();

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
     * Read all with XML node.
     *
     * @access  public
     * @return  string
     */
    public function readXML ( ) {

        return $this->getStream()->asXML();
    }

    /**
     * Get the name of the XML element.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->getStream()->getName();
    }

    /**
     * Count children number.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return $this->getStream()->count();
    }

    /**
     * Get the iterator.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function getIterator ( ) {

        return $this->getStream();
    }

    /**
     * Set a child.
     *
     * @access  public
     * @param   string  $name     Child name.
     * @param   mixed   $value    Child value.
     * @return  void
     */
    public function __set ( $name, $value ) {

        $this->getStream()->$name = $value;

        return;
    }

    /**
     * Get a child.
     *
     * @access  public
     * @param   string  $name    Child value.
     * @return  mixed
     */
    public function __get ( $name ) {

        return $this->getStream()->$name;
    }

    /**
     * Check if an attribute exists.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return null !== $this->readAttribute($offset);
    }

    /**
     * Get an attribute.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  array
     */
    public function offsetGet ( $offset ) {

        return $this->readAttribute($offset);
    }

    /**
     * Set a value to the attribute.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @param   string  $value     Attribute value.
     * @return  void
     */
    public function offsetSet ( $offset, $value ) {

        $handle = $this->getStream();
        $handle[$offset] = $value;

        return;
    }

    /**
     * Remove an attribute.
     *
     * @access  public
     * @param   string  $offset    Attribute name.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        $handle = $this->getStream();
        unset($handle[$offset]);

        return;
    }
}
