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
 * @subpackage  Hoa_Pom_Token_Call_ClassConstant
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom_Token_Util_Interface_Scalar
 */
import('Pom.Token.Util.Interface.Scalar');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Call
 */
import('Pom.Token.Call');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Call_ClassConstant.
 *
 * Represent a call to a class constant.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Call_ClassConstant
 */

class Hoa_Pom_Token_Call_ClassConstant extends    Hoa_Pom_Token_Call
                                       implements Hoa_Pom_Token_Util_Interface_Scalar,
                                                  Hoa_Visitor_Element {

    /**
     * Class name.
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_class    = null;

    /**
     * Constant name.
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_constant = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $class    Class name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_String $class ) {

        $this->setClass($class);

        return;
    }

    /**
     * Set class name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $class    Class name.
     * @return  Hoa_Pom_Token_String
     */
    public function setClass ( Hoa_Pom_Token_String $class ) {

        $old          = $this->_class;
        $this->_class = $class;

        return $old;
    }

    /**
     * Set constant name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $constant    Constant name.
     * @return  Hoa_Pom_Token_String
     */
    public function setConstant ( Hoa_Pom_Token_String $constant ) {

        $old             = $this->_constant;
        $this->_constant = $constant;

        return $old;
    }

    /**
     * Get class name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_String
     */
    public function getClass ( ) {

        return $this->_class;
    }

    /**
     * Get constant name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_String
     */
    public function getConstant ( ) {

        return $this->_constant;
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
