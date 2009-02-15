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
 * @subpackage  Hoa_Pom_Token_Call_Function
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
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Pom_Token_Variable
 */
import('Pom.Token.Variable');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Call_Function.
 *
 * Represent a call to a function.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Call_Function
 */

class Hoa_Pom_Token_Call_Function extends    Hoa_Pom_Token_Call
                                  implements Hoa_Visitor_Element {

    /**
     * Function name.
     *
     * @var mixed object
     */
    protected $_name      = null;

    /**
     * List of arguments.
     *
     * @var Hoa_Pom_Token_Call_Function array
     */
    protected $_arguments = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $name    Function name.
     * @return  void
     */
    public function __construct ( $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set function name.
     *
     * @access  public
     * @param   mixed   $name    Function name.
     * @return  mixed
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function setName ( Hoa_Pom_Token_String $name ) {

        switch(get_class($method)) {

            case 'Hoa_Pom_Token_String':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'A method should only be called by a string or a ' .
                    'variable. Given %s.', 0, $method);
        }

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Add many arguments.
     *
     * @access  public
     * @param   array   $arguments    Arguments to add.
     * @return  array
     */
    public function addArguments ( Array $arguments ) {

        foreach($arguments as $i => $argument)
            $this->addArgument($argument);

        return $this->_arguments;
    }

    /**
     * Add an argument.
     *
     * @access  public
     * @param   mixed   $argument    Argument to add.
     * @return  array
     */
    public function addArgument ( $argument ) {

        if(   !($argument instanceof Hoa_Pom_Token_Array)
           && !($argument instanceof Hoa_Pom_Token_Call)
           && !($argument instanceof Hoa_Pom_Token_Clone)
           && !($argument instanceof Hoa_Pom_Token_New)
           && !($argument instanceof Hoa_Pom_Token_Number)
           && !($argument instanceof Hoa_Pom_Token_Operation)
           && !($argument instanceof Hoa_Pom_Token_String)
           && !($argument instanceof Hoa_Pom_Token_Variable))
            throw new Hoa_Pom_Token_Util_Exception(
                'Cannot call a function with a %s in argument', 1,
                get_class($argument));

        $this->_arguments[] = $argument;

        return $this->_arguments;
    }

    /**
     * Remove the n-th argument.
     *
     * @access  public
     * @param   int     $n    Argument number to remove.
     * @return  void
     */
    public function removeArgument ( $n ) {

        unset($this->_arguments[$n]);

        return;
    }

    /**
     * Get name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get arguments.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
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
