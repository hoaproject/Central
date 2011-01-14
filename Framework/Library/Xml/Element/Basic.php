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
 * @subpackage  Hoa_Xml_Element_Basic
 *
 */

/**
 * Hoa_Xml_Element
 */
import('Xml.Element') and load();

/**
 * Hoa_Xml_CssToXPath
 */
import('Xml.CssToXPath');

/**
 * Hoa_Stream_Interface_Structural
 */
import('Stream.Interface.Structural') and load();

/**
 * Class Hoa_Xml_Element_Basic.
 *
 * This class represents a XML element in a XML tree.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Element_Basic
 */

class          Hoa_Xml_Element_Basic
    extends    SimpleXMLElement
    implements Hoa_Xml_Element,
               Hoa_Stream_Interface_Structural {

    /**
     * CssToXPath instance.
     *
     * @var Hoa_Xml_CssToXPath object
     */
    protected static $_cssToXPath = null;

    /**
     * String buffer (nodeValue).
     *
     * @var Hoa_StringBuffer object
     */
    protected static $_buffer     = null;



    /**
     * Select root of the document: :root.
     *
     * @access  public
     * @return  Hoa_Xml_Element_Basic
     */
    public function selectRoot ( ) {

        self::$_buffer = null;

        return simplexml_import_dom(
            $this->readDOM()->ownerDocument->documentElement,
            get_class($this)
        );
    }

    /**
     * Select any elements: *.
     *
     * @access  public
     * @return  array
     */
    public function selectAnyElements ( ) {

        self::$_buffer = null;

        return $this->xpath('//__current_ns:*');
    }

    /**
     * Select elements of type E: E.
     *
     * @access  public
     * @param   string  $E    Element E.
     * @return  array
     */
    public function selectElements ( $E = null ) {

        if(null === $E)
            return $this->selectAnyElements();

        self::$_buffer = null;

        return $this->xpath('//__current_ns:' . $E);
    }

    /**
     * Select F elements descendant of an E element: E F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectDescendantElements ( $F = null ) {

        return $this->selectElements($F);
    }

    /**
     * Select F elements children of an E element: E > F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectChildElements ( $F = null ) {

        self::$_buffer = null;

        if(null === $F || '*' == $F)
            return $this->xpath('child::__current_ns:*');

        return $this->xpath('child::__current_ns:' . $F);
    }

    /**
     * Select an F element immediately preceded by an E element: E + F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  Hoa_Xml_Element_Basic
     */
    public function selectAdjacentSiblingElement ( $F ) {

        self::$_buffer = null;
        $handle        = $this->xpath(
            'following-sibling::__current_ns:*[1]/self::__current_ns:' . $F
        );

        if(empty($handle))
            return false;

        return $handle[0];
    }

    /**
     * Select F elements preceded by an E element: E ~ F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectSiblingElements ( $F = null ) {

        if(null === $F)
            $F = '*';

        self::$_buffer = null;

        return $this->xpath('following-sibling::__current_ns:' . $F);
    }

    /**
     * Execute a query selector and return the first result.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  Hoa_Xml_Element_Basic
     * @throw   Hoa_Compiler_Exception
     */
    public function querySelector ( $query ) {

        $handle = $this->querySelectorAll($query);

        if(empty($handle))
            return false;

        return $handle[0];
    }

    /**
     * Execute a query selector and return one or many results.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  Hoa_Xml_Element_Basic
     * @throw   array
     */
    public function querySelectorAll ( $query ) {

        if(null === self::$_cssToXPath) {

            self::$_cssToXPath = new Hoa_Xml_CssToXPath();
            self::$_cssToXPath->setDefaultNamespacePrefix('__current_ns');
        }

        self::$_buffer = null;
        self::$_cssToXPath->compile($query);

        return $this->xpath(self::$_cssToXPath->getXPath());
    }

    /**
     * Transform this object to a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return (string) $this;
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        $handle = (array) $this->attributes();

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
     * Read custom attributes (as a set).
     * For example:
     *     <component data-abc="def" data-uvw="xyz" />
     * “data” is a custom attribute, so the $set.
     *
     * @access  public
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributes ( $set ) {

        $out     = array();
        $set    .= '-';
        $strlen  = strlen($set);

        foreach($this->readAttributes() as $name => $value)
            if($set === substr($name, 0, $strlen))
                $out[substr($name, $strlen)] = $value;

        return $out;
    }

    /**
     * Read custom attributes values as a list.
     *
     * @access  public
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributesAsList ( $set ) {

        $out = array();

        foreach($this->readCustomAttributes($set) as $name => $value)
            $out[$name] = explode(' ', $value);

        return $out;
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

        return $this->asXML();
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
     * Read children as a phrasing model, i.e. transform:
     *     <foo>abc<bar>def</bar>ghi</foo>
     * into
     *     <foo><???>abc</???><bar>def</bar><???>ghi</???></foo>
     * where <???> is the value of the $element argument, i.e. the inter-text
     * element name. Please, see the Hoa_Xml_Element_Model_Phrasing interface.
     *
     * @access  public
     * @param   string  $namespace    Namespace to use ('' if none).
     * @param   string  $element      Inter-text element name.
     * @return  array
     */
    public function readAsPhrasingModel ( $namespace = '', $element = '__text' ) {

        $out   = array();
        $list  = $this->readDOM()->childNodes;
        $class = get_class($this);

        for($i = 0, $max = $list->length; $i < $max; ++$i) {

            $node = $list->item($i);

            switch($node->nodeType) {

                case XML_ELEMENT_NODE:
                    $out[] = simplexml_import_dom($node, $class);
                  break;

                case XML_TEXT_NODE:
                    $out[] = new $class(
                        '<' . $element . '>' . $node->nodeValue .
                        '</' . $element . '>',
                        LIBXML_NOXMLDECL,
                        false,
                        $namespace,
                        false
                    );
                  break;
            }
        }

        return $out;
    }

    /**
     * Use a specific namespace.
     * For performance reason, we did not test if the namespace exists in the
     * document. Please, see the Hoa_Xml::namespaceExists() method to do that.
     *
     * @access  public
     * @param   string  $namespace    Namespace.
     * @return  Hoa_Xml_Element
     */
    public function useNamespace ( $namespace ) {

        $this->registerXPathNamespace('__current_ns', $namespace);

        return $this;
    }
}
