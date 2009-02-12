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

class Hoa_Pom_Token_Call_Method extends Hoa_Pom_Token_Call {

    /**
     * Object name.
     *
     * @var Hoa_Pom_Token_Variable object
     */
    protected $_object = null;

    /**
     * Method name.
     *
     * @var Hoa_Pom_Token_Call_Function object
     */
    protected $_method = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $object    Object name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_Variable $object ) {

        $this->setObject($object);

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
     * Set method name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Call_Function  $method    Method name.
     * @return  Hoa_Pom_Token_Call_Function
     */
    public function setMethod ( $method ) {

        $old           = $this->_method;
        $this->_method = $method;

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
     * Get method name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Call_Function
     */
    public function getMethod ( ) {

        return $this->_method;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            $this->getObject()->tokenize(),
            array(array(
                0 => Hoa_Pom::_OBJECT_OPERATOR,
                1 => '->',
                2 => -1
            )),
            $this->getMethod()->tokenize()
        );
    }
}
