<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Xml;

use Hoa\Consistency;
use Hoa\Stream;

/**
 * Class \Hoa\Xml.
 *
 *
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Xml
    extends    Stream\Composite
    implements Element,
               Stream\IStream\Structural,
               \Countable,
               \IteratorAggregate,
               \ArrayAccess
{
    /**
     * Cache of namespaces.
     *
     * @var array
     */
    protected $_namespaces = null;

    /**
     * Errors.
     *
     * @var array
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
     * @param   string         $stream                 Stream name to use.
     * @param   \Hoa\Stream    $innerStream            Inner stream.
     * @param   bool           $initializeNamespace    Whether we initialize
     *                                                 namespaces.
     * @param   mixed          $entityResolver         Entity resolver.
     * @throws  \Hoa\Xml\Exception
     * @throws  \Hoa\Xml\Exception\NamespaceMissing
     */
    public function __construct(
        $stream,
        Stream $innerStream,
        $initializeNamespace = true,
        $entityResolver      = null
    ) {
        if (!function_exists('simplexml_load_file')) {
            throw new Exception(
                'SimpleXML must be enable for using %s.',
                0,
                get_class($this)
            );
        }

        if (null !== $entityResolver) {
            $entityResolver = xcallable($entityResolver);
        }

        libxml_use_internal_errors(true);

        if (PHP_VERSION_ID >= 50400) {
            libxml_set_external_entity_loader(
                function ($public, $system, $context) use (&$entityResolver) {
                    if (null === $entityResolver) {
                        return null;
                    }

                    return $entityResolver($public, $system, $context);
                }
            );
        } else {
            libxml_disable_entity_loader(true);
        }

        if ($innerStream instanceof Stream\IStream\In) {
            if ($innerStream instanceof Stream\IStream\Pointable) {
                $innerStream->rewind();
            }

            $handle = $innerStream->readAll();
            $root   = @simplexml_load_string($handle, $stream);
        } else {
            $root = @simplexml_load_file($innerStream->getStreamName(), $stream);
        }

        $this->_errors = libxml_get_errors();
        $this->clearErrors();

        if (false === $root || true === $this->hasError()) {
            if (false === $this->hasError()) {
                throw new Exception(
                    'Failed to open the XML document %s.',
                    1,
                    $innerStream->getStreamName()
                );
            }

            $errors  = $this->getErrors();
            $first   = array_shift($errors);
            $message =
                '  • ' . trim(ucfirst($first->message)) .
                ' (at line ' . $first->line .
                ', column ' . $first->column . ')';

            foreach ($errors as $error) {
                $message .=
                    ';' . "\n" .
                    '  • ' . trim(ucfirst($error->message)) .
                    ' (at line ' . $error->line .
                    ', column ' . $error->column . ')';
            }

            $message .= '.' . "\n";

            if ($innerStream instanceof Stream\IStream\In) {
                $xml  = explode("\n", $innerStream->readAll());

                if (!empty($xml[0])) {
                    $message .=
                        "\n" . 'You should take a look at ' .
                        'this piece of code: ' . "\n";
                    $lines = count($xml) - 1;
                    $line  = $first->line;
                    $foo   = strlen((string) ($line + 3));

                    for (
                        $i = max(1, $line - 3), $m = min($lines, $line + 3);
                        $i <= $m;
                        ++$i
                    ) {
                        $message .= sprintf('%' . $foo . 'd', $i) . '. ';

                        if ($i == $line) {
                            $message .= '➜  ';
                        } else {
                            $message .= '   ';
                        }

                        $message .= $xml[$i - 1] . "\n";
                    }
                }
            }

            throw new Exception(
                'Errors occured while parsing the XML document %s:' .
                "\n" . '%s',
                2,
                [$innerStream->getStreamName(), $message]
            );
        }

        $this->setStream($root);
        $this->setInnerStream($innerStream);

        if (true === $initializeNamespace) {
            $this->initializeNamespaces();
        }

        return;
    }

    /**
     * Initialize namespaces.
     * If your document has no namespace, some of the Element\Basic::select*()
     * methods could not work properly.
     *
     * @return  void
     * @throws  \Hoa\Xml\Exception\NamespaceMissing
     */
    public function initializeNamespaces()
    {
        $stream            = $this->getStream();
        $this->_namespaces = $stream->getDocNamespaces();

        if (empty($this->_namespaces)) {
            throw new Exception\NamespaceMissing(
                'The XML document %s must have a default namespace at least.',
                4,
                $this->getInnerStream()->getStreamName()
            );
        }

        if (1 == count($this->_namespaces)) {
            $stream->registerXPathNamespace(
                '__current_ns',
                current($this->_namespaces)
            );
        } else {
            foreach ($this->_namespaces as $prefix => $namespace) {
                if ('' == $prefix) {
                    $prefix = '__current_ns';
                }

                $stream->registerXPathNamespace($prefix, $namespace);
            }
        }

        return;
    }

    /**
     * Whether a namespace exists.
     *
     * @param   string  $namespace    Namespace.
     * @return  bool
     */
    public function namespaceExists($namespace)
    {
        return false !== array_search($namespace, $this->_namespaces);
    }

    /**
     * Use a specific namespace.
     *
     * @param   string  $namespace    Namespace.
     * @return  \Hoa\Xml
     * @throws  \Hoa\Xml\Exception
     */
    public function useNamespace($namespace)
    {
        if (null === $this->_namespaces) {
            $this->initializeNamespaces();
        }

        if (false === $prefix = array_search($namespace, $this->_namespaces)) {
            throw new Exception(
                'The namespace %s does not exist in the document %s.',
                5,
                [$namespace, $this->getInnerStream()->getStreamName()]
            );
        }

        $this->getStream()->registerXPathNamespace('__current_ns', $namespace);

        return $this;
    }

    /**
     * Get namespace prefix.
     *
     * @param   string  $namespace    Namespace.
     * @return  string
     * @throws  \Hoa\Xml\Exception
     */
    public function getPrefix($namespace)
    {
        if (false === $prefix = array_search($namespace, $this->_namespaces)) {
            throw new Exception(
                'The namespace %s does not exist in the document %s.',
                6,
                [$namespace, $this->getInnerStream()->getStreamName()]
            );
        }

        return $prefix;
    }

    /**
     * Get declared namespaces.
     *
     * @return  array
     */
    public function getNamespaces()
    {
        if (null === $this->_namespaces) {
            $this->initializeNamespaces();
        }

        return $this->_namespaces;
    }

    /**
     * Select root of the document: :root.
     *
     * @return  \Hoa\Xml\Element
     */
    public function selectRoot()
    {
        return $this->getStream()->selectRoot();
    }

    /**
     * Select any elements: *.
     *
     * @return  array
     */
    public function selectAnyElements()
    {
        return $this->getStream()->selectAnyElements();
    }

    /**
     * Select elements of type E: E.
     *
     * @param   string  $E    Element E.
     * @return  array
     */
    public function selectElements($E = null)
    {
        return $this->getStream()->selectElements($E);
    }

    /**
     * Select F elements descendant of an E element: E F.
     *
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectDescendantElements($F = null)
    {
        return $this->getStream()->selectDescendantElements($F);
    }

    /**
     * Select F elements children of an E element: E > F.
     *
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectChildElements($F = null)
    {
        return $this->getStream()->selectChildElements($F);
    }

    /**
     * Select an F element immediately preceded by an E element: E + F.
     *
     * @param   string  $F    Element F.
     * @return  \Hoa\Xml\Element
     */
    public function selectAdjacentSiblingElement($F)
    {
        return $this->getStream()->selectAdjacentSiblingElement($F);
    }

    /**
     * Select F elements preceded by an E element: E ~ F.
     *
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectSiblingElements($F = null)
    {
        return $this->getStream()->selectSiblingElements($F);
    }

    /**
     * Execute a query selector and return the first result.
     *
     * @param   string  $query    Query.
     * @return  \Hoa\Xml\Element
     * @throws  \Hoa\Compiler\Exception
     */
    public function querySelector($query)
    {
        return $this->getStream()->querySelector($query);
    }

    /**
     * Execute a query selector and return one or many results.
     *
     * @param   string  $query    Query.
     * @return  \Hoa\Xml\Element
     * @throws  array
     */
    public function querySelectorAll($query)
    {
        return $this->getStream()->querySelectorAll($query);
    }

    /**
     * Run a XPath query on this tree.
     *
     * @param   string  $path    XPath query.
     * @return  array
     */
    public function xpath($path)
    {
        return $this->getStream()->xpath($path);
    }

    /**
     * Transform this object to a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getStream()->__toString();
    }

    /**
     * Read all attributes.
     *
     * @return  array
     */
    public function readAttributes()
    {
        return $this->getStream()->readAttributes();
    }

    /**
     * Read a specific attribute.
     *
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute($name)
    {
        return $this->getStream()->readAttribute($name);
    }

    /**
     * Whether an attribute exists.
     *
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists($name)
    {
        return $this->getStream()->attributeExists($name);
    }

    /**
     * Read attributes value as a list.
     *
     * @return  array
     */
    public function readAttributesAsList()
    {
        return $this->getStream()->readAttributesAsList();
    }

    /**
     * Read a attribute value as a list.
     *
     * @param   string  $name    Attribute's name.
     * @return  array
     */
    public function readAttributeAsList($name)
    {
        return $this->getStream()->readAttributeAsList($name);
    }

    /**
     * Read attributes as a string.
     *
     * @return  string
     */
    public function readAttributesAsString()
    {
        return $this->getStream()->readAttributesAsString();
    }

    /**
     * Read all with XML node.
     *
     * @return  string
     */
    public function readXML()
    {
        return $this->getStream()->asXML();
    }

    /**
     * Read content as a DOM tree.
     *
     * @return  \DOMElement
     */
    public function readDOM()
    {
        return $this->getStream()->readDOM();
    }

    /**
     * Get the name of the XML element.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->getStream()->getName();
    }

    /**
     * Count children number.
     *
     * @return  int
     */
    public function count()
    {
        return $this->getStream()->count();
    }

    /**
     * Get the iterator.
     *
     * @return  \Hoa\Xml\Element
     */
    public function getIterator()
    {
        return $this->getStream();
    }

    /**
     * Set a child.
     *
     * @param   string  $name     Child name.
     * @param   mixed   $value    Child value.
     * @return  void
     */
    public function __set($name, $value)
    {
        $this->getStream()->$name = $value;

        return;
    }

    /**
     * Get a child.
     *
     * @param   string  $name    Child value.
     * @return  mixed
     */
    public function __get($name)
    {
        return $this->getStream()->$name;
    }

    /**
     * Check if an attribute exists.
     *
     * @param   string  $offset    Attribute name.
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return null !== $this->readAttribute($offset);
    }

    /**
     * Get an attribute.
     *
     * @param   string  $offset    Attribute name.
     * @return  array
     */
    public function offsetGet($offset)
    {
        return $this->readAttribute($offset);
    }

    /**
     * Set a value to the attribute.
     *
     * @param   string  $offset    Attribute name.
     * @param   string  $value     Attribute value.
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        $handle          = $this->getStream();
        $handle[$offset] = $value;

        return;
    }

    /**
     * Remove an attribute.
     *
     * @param   string  $offset    Attribute name.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        $handle = $this->getStream();
        unset($handle[$offset]);

        return;
    }

    /**
     * Whether an error occured or not.
     *
     * @return  boolean
     */
    public function hasError()
    {
        return !empty($this->_errors);
    }

    /**
     * Get all errors (as an array of libXMLError structures).
     *
     * @return  array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Clear libXMLError.
     *
     * @return  void
     */
    protected function clearErrors()
    {
        return libxml_clear_errors();
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Xml\Xml');
