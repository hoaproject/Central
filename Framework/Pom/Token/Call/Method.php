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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_Call_Method
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
 * Hoa_Pom_Token_Call
 */
import('Pom.Token.Call');

/**
 * Hoa_Pom_Token_Variable
 */
import('Pom.Token.Variable');

/**
 * Hoa_Pom_Token_Call_Function
 */
import('Pom.Token.Call.Function');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Call_Method.
 *
 * Represent a call to a method.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Call_Method
 */

class Hoa_Pom_Token_Call_Method extends    Hoa_Pom_Token_Call_Function
                                implements Hoa_Visitor_Element {

    /**
     * Object name.
     *
     * @var Hoa_Pom_Token_Variable object
     */
    protected $_object = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable        $object       Object name.
     * @param   mixed                         $method       Method name.
     * @param   array                         $arguments    Arguments to add.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_Variable $object,
                                                         $method    = null,
                                  Array                  $arguments = array() ) {

        $this->setObject($object);

        if(null !== $method)
            parent::__construct($method, $arguments);

        return;
    }

    /**
     * Set object name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $object    Object name.
     * @return  Hoa_Pom_Token_Variable
     */
    public function setObject ( Hoa_Pom_Token_Variable $object ) {

        $old           = $this->_object;
        $this->_object = $object;

        return $old;
    }

    /**
     * Get object name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Variable
     */
    public function getObject ( ) {

        return $this->_object;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              $handle     Handle (reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor, &$handle = null ) {

        return $visitor->visit($this);
    }
}
