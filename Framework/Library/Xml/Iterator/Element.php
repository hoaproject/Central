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
 * @subpackage  Hoa_Xml_Iterator_Element
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Class Hoa_Xml_Iterator_Element.
 *
 * This class is an iterator for XML element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Iterator_Element
 */

class Hoa_Xml_Iterator_Element implements RecursiveIterator {

    /**
     * Array.
     *
     * @var Hoa_Xml_Iterator_Element array
     */
    protected $_a = array();



    /**
     * Set our array to recursively iterate.
     *
     * @access  public
     * @param   Array  $a    Array.
     * @return  void
     */
    public function __construct ( Array &$a ) {

        $this->_a = &$a;

        return;
    }

    /**
     * Get the current element.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function current ( ) {

        return current($this->_a);
    }

    /**
     * Get the current element key.
     *
     * @access  public
     * @return  mixed
     */
    public function key ( ) {

        return key($this->_a);
    }

    /**
     * Advance the internal pointer and return the current element.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function next ( ) {

        next($this->_a);

        return $this;
    }

    /**
     * Rewind the inernal pointer and return the first element.
     *
     * @access  public
     * @return  Hoa_Xml_Element
     */
    public function rewind ( ) {

        reset($this->_a);

        return $this;
    }

    /**
     * Check if there is a current element after call to the rewind() or the
     * next() methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        return true === array_key_exists(key($this->_a), $this->_a);
    }

    /**
     * Check if the current element exists more than one time.
     *
     * @access  public
     * @return  bool
     */
    public function hasChildren ( ) {

        return true === is_array(current($this->_a));
    }

    /**
     * Get the current sub-iterator.
     *
     * @access  public
     * @return  Hoa_Xml_Iterator_Element
     */
    public function getChildren ( ) {

        $h = current($this->_a);

        return new self($h);
    }
}
