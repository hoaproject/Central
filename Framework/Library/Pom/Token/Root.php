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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Root
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Root.
 *
 * Represent the root of object model.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Root
 */

class Hoa_Pom_Token_Root implements Hoa_Visitor_Element {

    /**
     * Collection of childs.
     *
     * @var Hoa_Pom_Token_Root array
     */
    protected $_childs = array();



    /**
     * Add many childs.
     *
     * @access  public
     * @param   array   $childs    Childs to add.
     * @return  array
     */
    public function addElements ( Array $childs = array() ) {

        foreach($childs as $i => $child)
            $this->addElement($child);

        return $this->_childs;
    }

    /**
     * Add a child.
     *
     * @access  public
     * @param   mixed   $child    Child to add.
     * @return  array
     */
    public function addElement ( $child ) {

        $this->_childs[] = $child;

        return $this->_childs;
    }

    /**
     * Get all childs.
     *
     * @access  public
     * @return  array
     */
    public function getElements ( ) {

        return $this->_childs;
    }

    /**
     * Get the last child.
     *
     * @access  public
     * @return  array
     */
    public function getLastElement ( ) {

        return end($this->_childs);
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
