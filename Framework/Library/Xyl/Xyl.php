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
 * @package     Hoa_Xyl
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xyl_Exception
 */
import('Xyl.Exception');

/**
 * Hoa_Xyl_Element
 */
import('Xyl.Element') and load();

/**
 * Hoa_Xyl_Element_Basic
 */
import('Xyl.Element.Basic');

/**
 * Hoa_Xml
 */
import('Xml.~') and load();

/**
 * Class Hoa_Xyl.
 *
 * 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Xyl
 */

class          Hoa_Xyl
    extends    Hoa_Xml
    implements Hoa_Xyl_Element {

    /**
     * Data bucket.
     *
     * @var Hoa_Xyl array
     */
    protected $_data  = array();

    protected $_yield = array();

    /**
     * Map and store index.
     *
     * @var Hoa_Xyl array
     */
    private $_i       = 0;

    /**
     * Map index to XYL element.
     *
     * @var Hoa_Xyl array
     */
    private $_map     = array();

    /**
     * Store data of XYL element.
     *
     * @var Hoa_Xyl array
     */
    private $_store   = array();



    /**
     * Interprete a stream as XYL.
     *
     * @access  public
     * @param   Hoa_Stream  $stream    Stream to interprete as XYL.
     * @return  void
     * @throw   Hoa_Xml_Exception
     */
    public function __construct ( Hoa_Stream $stream ) {

        parent::__construct('Hoa_Xyl_Element_Basic', $stream);

        return;
    }

    /**
     * Get element store.
     *
     * @access  public
     * @param   Hoa_Xyl_Element  $element    Element as identifier.
     * @return  array
     */
    final public function &_getStore ( Hoa_Xyl_Element $element ) {

        if(false === $id = array_search($element, $this->_map)) {

            $id                = $this->_i++;
            $this->_map[$id]   = $element;
            $this->_store[$id] = null;
        }

        return $this->_store[$id];
    }

    /**
     * Add data to the data bucket.
     *
     * @access  public
     * @param   array  $data    Data to add.
     * @return  array
     */
    public function addData ( Array $data ) {

        return $this->_data = array_merge_recursive($this->_data, $data);
    }

    /**
     * Distribute data into the XYL tree. Data are linked to element through a
     * reference to the data bucket in this object.
     *
     * @access  public
     * @return  void
     */
    public function linkData ( ) {

        return $this->getStream()->linkData($this->_data);
    }

    /**
     * Get data of this element.
     *
     * @access  public
     * @return  array
     */
    public function &getData ( ) {

        return $this->getStream()->getData();
    }

    /**
     * Get current data of this element.
     *
     * @access  public
     * @return  mixed
     */
    public function getCurrentData ( ) {

        return $this->getStream()->getCurrentData();
    }

    public function firstUpdate ( ) {

        return $this->getStream()->firstUpdate();
    }

    public function update ( ) {

        return $this->getStream()->update();
    }

    public function addYield ( Hoa_Xyl_Element $yield ) {

        $this->_yield[$yield->readAttribute('name')] = $yield;

        return;
    }
}
