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
 * @subpackage  Hoa_Xml_Element_Concrete
 *
 */

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Hoa_Xml_Element
 */
import('Xml.Element') and load();

/**
 * Class Hoa_Xml_Element_Concrete.
 *
 * This class represents a XML element in a XML tree.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Element_Concrete
 */

class          Hoa_Xml_Element_Concrete
    implements Hoa_Xml_Element,
               Countable,
               IteratorAggregate {

    /**
     * Store all elements of the abstract tree.
     *
     * @var Hoa_Xml_Element_Concrete array
     */
    protected static $_store      = array();

    /**
     * Super roots of each abstract elements.
     *
     * @var Hoa_Xml_Element_Concrete array
     */
    protected static $_superRoots = array();

    /**
     * Instances of conrete elements of each abstract element.
     *
     * @var Hoa_Xml_Element_Concrete array
     */
    protected static $_multiton   = array();

    /**
     * Name of the concrete element.
     *
     * @var Hoa_Xml_Element_Concrete string
     */
    protected $_name              = null;

    /**
     * Concrete children.
     *
     * @var Hoa_Xml_Element_Concrete array
     */
    protected $_children          = array();

    /**
     * Concrete children for the iterator.
     *
     * @var Hoa_Xml_Element_Concrete array
     */
    protected $_iterator          = array();

    /**
     * Abstract element.
     *
     * @var Hoa_Xml_Element object
     */
    protected $_element           = null;

    /**
     * Super root of the abstract element.
     *
     * @var Hoa_Xml_Element object
     */
    protected $_superRoot         = null;



    /**
     * Build a concrete tree.
     *
     * @access  public
     * @param   Hoa_Xml_Element  $element      Abstract element.
     * @param   Hoa_Xml_Element  $superRoot    Super root.
     * @param   array            $rank         Rank: abstract elements to
     *                                         concrete elements.
     * @param   string           $namespace    Namespace.
     * @return  void
     */
    public function __construct ( Hoa_Xml_Element $element,
                                  Hoa_Xml_Element $superRoot,
                                  Array           $rank = array(),
                                  $namespace            = null ) {

        self::$_store[]      = $element;
        self::$_superRoots[] = $superRoot;
        self::$_multiton[]   = $this;

        if(null !== $namespace)
            $element->useNamespace($namespace);

        if(null === $this->_name) {

            $this->_name     = strtolower(get_class($this));

            if(false !== $po = strrpos($this->_name, '_'))
                $this->_name = substr($this->_name, $po + 1);
        }

        $this->_element      = $element;
        $this->_superRoot    = $superRoot;

        foreach($element->selectChildElements() as $child) {

            $name = $child->getName();

            if(!isset($rank[$name]))
                throw new Hoa_Xml_Exception(
                    'Cannot build the concrete tree because the abstract ' .
                    'element <%s> has no ranked concrete element.', 0, $name);

            $c = $rank[$name];
            $h = new $c($child, $superRoot, $rank, $namespace);
            $this->_children[$h->getName()][] = $h;
            $this->_iterator[]                = $h;
        }

        return;
    }

    /**
     * Get the abstract element ID.
     *
     * @access  public
     * @param   Hoa_Xml_Element  $element    Abstract element.
     * @return  mixed
     */
    public static function getAbstractElementId ( Hoa_Xml_Element $element ) {

        return array_search($element, self::$_store);
    }

    /**
     * Get the abstract element.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function getAbstractElement ( ) {

        return $this->_element;
    }

    /**
     * Get the associated concrete element of an abstract element.
     *
     * @access  public
     * @param   Hoa_Xml_Element  $element    Abstract element.
     * @return  Hoa_Xml_Element_Concrete
     * @throw   Hoa_Xml_Exception
     */
    public static function getConcreteElement ( Hoa_Xml_Element $element ) {

        if(false === $id = self::getAbstractElementId($element))
            throw new Hoa_Xml_Exception(
                'The basic element %s has no concrete equivalent.',
                1, $element->getName());

        return self::$_multiton[$id];
    }

    /**
     * Get the super-root of the abstract element.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function getAbstractElementSuperRoot ( ) {

        return $this->_superRoot;
    }

    /**
     * Get the super-root of an abstract element.
     *
     * @access  public
     * @param   Hoa_Xml_Element  $element    Abstract element.
     * @return  Hoa_Xml_Element
     * @throws  Hoa_Xml_Exception
     */
    public static function getAbstractElementSuperRootOf ( Hoa_Xml_Element $element ) {

        if(false === $id = self::getAbstractElementId($element))
            throw new Hoa_Xml_Exception(
                'The concrete element %s has no concrete equivalent and we ' .
                'cannot retrieve the super-root.', 2, $element->getName());

        return self::$_superRoots[$id];
    }

    /**
     * Get the name of the concrete element.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Count children number.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_children);
    }

    /**
     * Get the iterator.
     *
     * @access  public
     * @return  ArrayIterator
     */
    public function getIterator ( ) {

        return new ArrayIterator($this->_iterator);
    }

    /**
     * Set a child.
     *
     * @access  public
     * @param   string                    $name     Child name.
     * @param   Hoa_Xml_Element_Concrete  $value    Value.
     * @return  void
     */
    public function __set ( $name, Hoa_Xml_Element_Concrete $value ) {

        $this->_children[$name][] = $value;
        $this->_iterator[]        = $value;

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

        if(!isset($this->_children[$name]))
            return null;

        return $this->_children[$name];
    }

    /**
     * Redirect unknown call on the abstract element.
     *
     * @access  public
     * @param   string  $name         Name.
     * @param   array   $arguments    Arguments.
     * @return  mixed
     */
    public function __call ( $name, Array $arguments = array() ) {

        return call_user_func_array(
            array($this->_element, $name),
            $arguments
        );
    }
}
