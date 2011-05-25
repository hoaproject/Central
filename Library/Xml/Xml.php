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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Xml\Exception
 */
-> import('Xml.Exception.~')

/**
 * \Hoa\Xml\Exception\NamespaceMissing
 */
-> import('Xml.Exception.NamespaceMissing')

/**
 * \Hoa\Xml\Element
 */
-> import('Xml.Element.~')

/**
 * \Hoa\Stream\Composite
 */
-> import('Stream.Composite')

/**
 * \Hoa\Stream\I~\Structural
 */
-> import('Stream.I~.Structural');

}

namespace Hoa\Xml {

/**
 * Class \Hoa\Xml.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Xml
    extends    \Hoa\Stream\Composite
    implements Element,
               \Hoa\Stream\IStream\Structural,
               \Countable,
               \IteratorAggregate,
               \ArrayAccess {

    /**
     * Cache of namespaces.
     *
     * @var \Hoa\Xml array
     */
    protected $_namespaces = null;

    /**
     * Errors.
     *
     * @var \Hoa\Xml array
     */
    protected $_errors     = null;



    /**
     * Constructor. Load the inner stream as a XML tree.
     * If we cannot load the inner stream and if it is a \Hoa\Stream\IStream\In
     * stream and if its content is empty, we use the follow default XML value:
     *     <?xml version="1.0" encoding="utf-8"?>
     *
     *     <handler xmlns="http://hoa-project.net/ns/xml/default">
     *     </handler>
     *
     * @access  public
     * @param   string       $stream                 Stream name to use.
     * @param   \Hoa\Stream  $innerStream            Inner stream.
     * @param   bool         $initializeNamespace    Whether we initialize
     *                                               namespaces.
     * @return  void
     * @throw   \Hoa\Xml\Exception
     * @throw   \Hoa\Xml\Exception\NamespaceMissing
     */
    public function __construct ( $stream, \Hoa\Stream $innerStream,
                                  $initializeNamespace = true ) {

        if(!function_exists('simplexml_load_file'))
            throw new Exception(
                'SimpleXML must be enable for using %s.', 0, get_class($this));

        libxml_use_internal_errors(true);

        $root = @simplexml_load_file($innerStream->getStreamName(), $stream);

        if(   false === $root
           && $innerStream instanceof \Hoa\Stream\IStream\In) {

            $handle = $innerStream->readAll();

            if(empty($handle)) {

                $this->clearErrors();
                $handle = '<?xml version="1.0" encoding="utf-8"?' . ">\n\n" .
                          '<handler xmlns="' .
                          'http://hoa-project.net/ns/xml/default">' . "\n" .
                          '</handler>';
            }

            $root = @simplexml_load_string($handle, $stream);
        }

        $this->_errors = libxml_get_errors();
        $this->clearErrors();

        if(false === $root || true === $this->hasError()) {

            if(false === $this->hasError())
                throw new Exception(
                    'Failed to open the XML document %s.',
                    1, $innerStream->getStreamName());

            $errors   = $this->getErrors();
            $first    = array_shift($errors);
            $message  = '  • ' . trim(ucfirst($first->message)) .
                        ' (at line ' . $first->line .
                        ', column ' . $first->column . ')';

            foreach($errors as $error)
                $message .= ';' . "\n" .
                            '  • ' . trim(ucfirst($error->message)) .
                            ' (at line ' . $error->line .
                            ', column ' . $error->column . ')';

            $message .= '.' . "\n";

            if($innerStream instanceof \Hoa\Stream\IStream\In) {

                $xml  = explode("\n", $innerStream->readAll());

                if(!empty($xml[0])) {

                    $message .= "\n" . 'You should take a look at ' .
                                'this piece of code: ' . "\n";
                    $lines    = count($xml) - 1;
                    $line     = $first->line;
                    $foo      = strlen((string) ($line + 3));

                    for($i = max(1, $line - 3),
                        $m = min($lines, $line + 3);
                        $i <= $m;
                        ++$i) {

                        $message .= sprintf('%' . $foo . 'd', $i) . '. ';

                        if($i == $line)
                            $message .= '➜  ';
                        else
                            $message .= '   ';

                        $message .= $xml[$i - 1] . "\n";
                    }
                }
            }

            throw new Exception(
                'Errors occured while parsing the XML document %s:' .
                "\n" . '%s',
                2, array($innerStream->getStreamName(), $message));
        }

        $this->setStream($root);
        $this->setInnerStream($innerStream);

        if(true === $initializeNamespace)
            $this->initializeNamespaces();

        return;
    }

    /**
     * Initialize namespaces.
     * If your document has no namespace, some of the Element\Basic::select*()
     * methods could not work properly.
     *
     * @access  protected
     * @return  void
     * @throw   \Hoa\Xml\Exception\NamespaceMissing
     */
    public function initializeNamespaces ( ) {

        $stream            = $this->getStream();
        $this->_namespaces = $stream->getDocNamespaces();

        if(empty($this->_namespaces))
            throw new Exception\NamespaceMissing(
                'The XML document %s must have a default namespace at least.',
                4, $this->getInnerStream()->getStreamName());

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
     * @return  \Hoa\Xml
     * @throw   \Hoa\Xml\Exception
     */
    public function useNamespace ( $namespace ) {

        if(null === $this->_namespaces)
            $this->initializeNamespaces();

        if(false === $prefix = array_search($namespace, $this->_namespaces))
            throw new Exception(
                'The namespace %s does not exist in the document %s.',
                5, array($namespace, $this->getInnerStream()->getStreamName()));

        $this->getStream()->registerXPathNamespace('__current_ns', $namespace);

        return $this;
    }

    /**
     * Get namespace prefix.
     *
     * @access  public
     * @param   string  $namespace    Namespace.
     * @return  string
     * @throw   \Hoa\Xml\Exception
     */
    public function getPrefix ( $namespace ) {

        if(false === $prefix = array_search($namespace, $this->_namespaces))
            throw new Exception(
                'The namespace %s does not exist in the document %s.',
                6, array($namespace, $this->getInnerStream()->getStreamName()));

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
     * @return  \Hoa\Xml\Element
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
     * @return  \Hoa\Xml\Element
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
     * @return  \Hoa\Xml\Element
     * @throw   \Hoa\Compiler\Exception
     */
    public function querySelector ( $query ) {

        return $this->getStream()->querySelector($query);
    }

    /**
     * Execute a query selector and return one or many results.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  \Hoa\Xml\Element
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

        return $this->getStream()->readAttribute($name);
    }

    /**
     * Whether an attribute exists.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists ( $name ) {

        return $this->getStream()->attributeExists($name);
    }

    /**
     * Read attributes value as a list.
     *
     * @access  public
     * @return  array
     */
    public function readAttributesAsList ( ) {

        return $this->getStream()->readAttributesAsList();
    }

    /**
     * Read a attribute value as a list.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  array
     */
    public function readAttributeAsList ( $name ) {

        return $this->getStream()->readAttributeAsList($name);
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        return $this->getStream()->readAttributesAsString();
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
     * @return  \DOMElement
     */
    public function readDOM ( ) {

        return $this->getStream()->readDOM();
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
     * @return  \Hoa\Xml\Element
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

        return !empty($this->_errors);
    }

    /**
     * Get all errors (as an array of libXMLError structures).
     *
     * @access  public
     * @return  array
     */
    public function getErrors ( ) {

        return $this->_errors;
    }

    /**
     * Clear libXMLError.
     *
     * @access  protected
     * @return  void
     */
    protected function clearErrors ( ) {

        return libxml_clear_errors();
    }
}

}
