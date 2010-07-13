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
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xml_Element
 */
import('Xml.Element');

/**
 * Hoa_Xml_CssToXPath
 */
import('Xml.CssToXPath');

/**
 * Hoa_Stream_Io_Structural
 */
import('Stream.Io.Structural');

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
               Hoa_Stream_Io_Structural {

    /**
     * Root of XML tree.
     *
     * @var Hoa_Xml_Element_Basic object
     */
    protected static $_root       = null;

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
     * Set root.
     *
     * @access  public
     * @return  void
     */
    public function setRoot ( $root ) {

        $old         = self::$_root;
        self::$_root = $root;

        return $old;
    }

    /**
     * Select root of the document: :root.
     *
     * @access  public
     * @return  Hoa_Xml_Element_Basic
     */
    public function selectRoot ( ) {

        self::$_buffer = null;

        return self::$_root;
    }

    /**
     * Select any element: *.
     *
     * @access  public
     * @return  array
     */
    public function selectAnyElement ( ) {

        self::$_buffer = null;

        return $this->xpath('//*');
    }

    /**
     * Select an element of type E: E.
     *
     * @access  public
     * @param   string  $E    Element E.
     * @return  array
     */
    public function selectElement ( $E = null ) {

        if(null === $E)
            return $this->selectAnyElement();

        self::$_buffer = null;

        return $this->xpath('//' . $E);
    }

    /**
     * Select an F element descendant of an E element: E F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectDescendantElement ( $F = null ) {

        return $this->selectElement($F);
    }

    /**
     * Select an F element child of an E element: E > F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectChildElement ( $F = null ) {

        self::$_buffer = null;

        if(null === $F || '*' == $F)
            return $this->children();

        return $this->$F;
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
        $handle        = $this->xpath('following-sibling::*[1]/self::' . $F);

        if(empty($handle))
            return false;

        return $handle[0];
    }

    /**
     * Select an F element preceded by an E element: E ~ F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectSiblingElement ( $F = null ) {

        if(null === $F)
            $F = '*';

        self::$_buffer = null;

        return $this->xpath('following-sibling::' . $F);
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

        if(null === self::$_cssToXPath)
            self::$_cssToXPath = new Hoa_Xml_CssToXPath();

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
}
