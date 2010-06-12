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
 * Hoa_Framework
 */
require_once 'Framework.php';

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
    implements Hoa_Stream_Io_Structural {

    /**
     *
     */
    public function __construct ( $stream, Hoa_Stream $innerStream ) {

        $this->setStream(simplexml_load_file(
            $innerStream->getStreamName(),
            $stream
        ));
        $this->setInnerStream($innerStream);

        return;
    }

    public function __set ( $name, $value ) {

        $this->getStream()->$name = $value;
    }

    public function __get ( $name ) {

        return $this->getStream()->$name;
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
     * Transform this object to a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getStream()->__toString();
    }
}
