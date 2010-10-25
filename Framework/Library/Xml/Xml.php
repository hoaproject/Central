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
import('Xml.Element') and load();

/**
 * Hoa_Stream_Composite
 */
import('Stream.Composite') and load();

/**
 * Hoa_Stream_Io_Structural
 */
import('Stream.Io.Structural') and load();

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
     * Cache of namespaces.
     *
     * @var Hoa_Xml array
     */
    protected $_namespaces = null;



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

        libxml_use_internal_errors(true);

        $streamName = $innerStream->getStreamName();
        $root       = @simplexml_load_file($streamName, $stream);

        if(false === $root) {

            if($innerStream instanceof Hoa_Stream_Io_In)
                $root = @simplexml_load_string($innerStream->readAll(), $stream);

            if(false === $root) {

                if(true === $this->hasError()) {

                    $errors   = $this->getErrors();
                    $first    = array_shift($errors);
                    $message  = '  * ' . trim(ucfirst($first->message)) .
                                ' (at line ' . $first->line .
                                ', column ' . $first->column . ')';

                    foreach($errors as $error)
                        $message .= ';' . "\n" .
                                    '  * ' . trim(ucfirst($error->message)) .
                                    ' (at line ' . $error->line .
                                    ', column ' . $error->column . ')';

                    $message .= '.' . "\n";
                    $xml      = explode("\n", $innerStream->readAll());

                    if(!empty($xml[0])) {

                        $message .= "\n" . 'You should take a look at this ' .
                                    'piece of code: ' . "\n";
                        $lines    = count($xml) - 1;
                        $line     = $first->line;
                        $foo      = strlen((string) ($line + 3));

                        for($i = max(1, $line - 3), $m = min($lines, $line + 3);
                            $i <= $m;
                            ++$i) {

                            $message .= sprintf('%' . $foo . 'd', $i) . ' ';

                            if($i == $line)
                                $message .= '➜ ';
                            else
                                $message .= '  ';

                            $message .= $xml[$i - 1] . "\n";
                        }
                    }

                    throw new Hoa_Xml_Exception(
                        'Errors occured while parsing the XML document %s:' .
                        "\n" . '%s',
                        1, array($innerStream->getStreamName(), $message));
                }

                if(!($innerStream instanceof Hoa_Stream_Io_Out))
                    throw new Hoa_Xml_Exception(
                        'Failed to open the XML document %s.',
                        1, $innerStream->getStreamName());
                else
                    $root = simplexml_load_string(
                        '<?xml version="1.0" encoding="utf-8"?' . ">\n\n" .
                        '<handler>' . "\n" . '</handler>',
                        $stream
                    );
            }
        }

        if(null === $root)
            throw new Hoa_Xml_Exception(
                'Failed to understand %s as a XML stream.',
                2, $streamName);

        $this->setStream($root);
        $this->setInnerStream($innerStream);
        $this->initializeNamespaces();

        return;
    }

    /**
     * Initialize namespaces.
     *
     * @access  protected
     * @return  void
     */
    public function initializeNamespaces ( ) {

        $stream            = $this->getStream();
        $this->_namespaces = $stream->getDocNamespaces();

        if(empty($this->_namespaces))
            throw new Hoa_Xml_Exception(
                'The XML document %s must have a default namespace at least.',
                3, $this->getInnerStream()->getStreamName());

        if(1 == count($this->_namespaces))
            $stream->registerXPathNamespace(
                '__current_ns',
                current($this->_namespaces)
            );
        else
            foreach($this->_namespaces as $prefix => $namespace) {

                if('' == $prefix)
                    $prefix = '__current_ns';

                $stream->registerXPathNamespace($prefix, $namespace);
            }

        return;
    }

    /**
     * Whether a namespace exists.
     *
     * @access  public
     * @param   string  $namespace    Namespace.
     * @return  bool
     */
    public function namespaceExists ( $namespace ) {

        return false !== array_search($namespace, $this->_namespaces);
    }

    /**
     * Use a specific namespace.
     *
     * @access  public
     * @param   string  $namespace    Namespace.
     * @return  Hoa_Xml
     * @throw   Hoa_Xml_Exception
     */
    public function useNamespace ( $namespace ) {

        if(null === $this->_namespaces)
            $this->initializeNamespaces();

        if(false === $prefix = array_search($namespace, $this->_namespaces))
            throw new Hoa_Xml_Exception(
                'The namespace %s does not exist in the document %s.',
                4, array($namespace, $this->getInnerStream()->getStreamName()));

        $this->getStream()->registerXPathNamespace('__current_ns', $namespace);

        return $this;
    }

    /**
     * Get namespace prefix.
     *
     * @access  public
     * @param   string  $namespace    Namespace.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function getPrefix ( $namespace ) {

        if(false === $prefix = array_search($namespace, $this->_namespaces))
            throw new Hoa_Xml_Exception(
                'The namespace %s does not exist in the document %s.',
                5, array($namespace, $this->getInnerStream()->getStreamName()));

        return $prefix;
    }

    /**
     * Get declared namespaces.
     *
     * @access  public
     * @return  array
     */
    public function getNamespaces ( ) {

        if(null === $this->_namespaces)
            $this->initializeNamespaces();

        return $this->_namespaces;
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
     * Select any elements: *.
     *
     * @access  public
     * @return  array
     */
    public function selectAnyElements ( ) {

        return $this->getStream()->selectAnyElements();
    }

    /**
     * Select elements of type E: E.
     *
     * @access  public
     * @param   string  $E    Element E.
     * @return  array
     */
    public function selectElements ( $E = null ) {

        return $this->getStream()->selectElements($E);
    }

    /**
     * Select F elements descendant of an E element: E F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectDescendantElements ( $F = null ) {

        return $this->getStream()->selectDescendantElements($F);
    }

    /**
     * Select F elements children of an E element: E > F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectChildElements ( $F = null ) {

        return $this->getStream()->selectChildElements($F);
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
     * Select F elements preceded by an E element: E ~ F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectSiblingElements ( $F = null ) {

        return $this->getStream()->selectSiblingElements($F);
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

        return true === array_key_exists($name, $this->readAttributes());
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
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out = null;

        foreach($this->readAttributes() as $name => $value)
            $out .= ' ' . $name . '="' . str_replace('"', '\"', $value) . '"';

        return $out;
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
     * Read content as a DOM tree.
     *
     * @access  public
     * @return  DOMElement
     */
    public function readDOM ( ) {

        return dom_import_simplexml($this->getStream());
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

    /**
     * Whether an error occured or not.
     *
     * @access  public
     * @return  boolean
     */
    public function hasError ( ) {

        $errors = $this->getErrors();

        return !empty($errors);
    }

    /**
     * Get all errors (as an array of libXMLError structures).
     *
     * @access  public
     * @return  array
     */
    public function getErrors ( ) {

        return libxml_get_errors();
    }
}
