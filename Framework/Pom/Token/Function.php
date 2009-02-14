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
 * @subpackage  Hoa_Pom_Token_Function
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
 * Hoa_Pom_Token_Comment
 */
import('Pom.Token.Comment');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Pom_Token_Function_Argument
 */
import('Pom.Token.Function.Argument');

/**
 * Class Hoa_Pom_Token_Function.
 *
 * Represent an abstract function.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Function
 */

abstract class Hoa_Pom_Token_Function {

    /**
     * Comment.
     *
     * @var Hoa_Pom_Token_Comment object
     */
    protected $_comment      = null;

    /**
     * Whether returned values are given by reference.
     *
     * @var Hoa_Pom_Token_Function bool
     */
    protected $_isReferenced = false;

    /**
     * Name.
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_name         = null;

    /**
     * List of arguments.
     *
     * @var Hoa_Pom_Token_Function array
     */
    protected $_arguments    = array();

    /**
     * Body.
     *
     * @var Hoa_Pom_Token_Function array
     */
    protected $_body         = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Function name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_String $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set function comment.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Comment  $comment    Function comment.
     * @return  Hoa_Pom_Token_Comment
     */
    public function setComment ( Hoa_Pom_Token_Comment $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Remove function comment.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Comment
     */
    public function removeComment ( ) {

        return $this->setComment(new Hoa_Pom_Token_Comment(null));
    }

    /**
     * Reference this function.
     *
     * @access  public
     * @param   bool    $active    Whether returned values are given by reference.
     * @return  bool
     */
    public function referenceMe ( $active = true ) {

        $old                 = $this->_isReferenced;
        $this->_isReferenced = $active;

        return $old;
    }

    /**
     * Set function name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Function name.
     * @return  Hoa_Pom_Token_String
     */
    public function setName ( Hoa_Pom_Token_String $name ) {

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
     * Check if an argument exists.
     *
     * @access  public
     * @param   mixed   $argument    Argument to check. Could be a string or
     *                               a Hoa_Pom_Token_Function_Argument
     *                               instance.
     * @return  bool
     */
    public function argumentExists ( $argument ) {

        if($argument instanceof Hoa_Pom_Token_Function_Argument)
            $argument = $argument->getName();

        foreach($this->getArguments() as $i => $a)
            if($a->getName() == $argument)
                return true;

        return false;
    }

    /**
     * Add an argument.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Function_Argument  $argument    Argument
     *                                                        instance.
     * @return  Hoa_Pom_Token_Function_Argument
     */
    public function addArgument ( Hoa_Pom_Token_Function_Argument $argument ) {

        if(true === $this->argumentExists($argument))
            return;

        return $this->_arguments[] = $argument;
    }

    /**
     * Remove an argument.
     *
     * @access  public
     * @param   mixed   $argument    Argument name. Could be a string or a
     *                               Hoa_Pom_Token_Function_Argument
     *                               instance.
     * @return  array
     */
    public function removeArgument ( $argument ) {

        if($argument instanceof Hoa_Pom_Token_Function_Argument)
            $argument = $argument->getName();

        if(false === $this->argumentExists($argument))
            return $this->_arguments;

        foreach($this->getArguments() as $i => $a)
            if($a->getName() == $argument) {

                unset($this->_arguments[$i]);
                break;
            }

        return $this->_arguments;
    }

    /**
     * Add an element to the function body.
     *
     * @access  public
     * @param   mixed   $element    Element to add.
     * @return  array
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addBody ( $element ) {

        if(   !($element instanceof Hoa_Pom_Token_Instruction)
           && !($element instanceof Hoa_Pom_Token_LateParsing))
            throw new Hoa_Pom_Token_Util_Exception(
                'A function cannot have %s in his body.', 0, get_class($element));

        $this->_body[] = $element;

        return $this->_body;
    }

    /**
     * Get function comment.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Comment
     */
    public function getComment ( ) {

        return $this->_comment;
    }

    /**
     * Check if function has a comment.
     *
     * @access  public
     * @return  bool
     */
    public function hasComment ( ) {

        return null !== $this->getComment();
    }

    /**
     * Whether returned values are given by reference.
     *
     * @access  public
     * @return  bool
     */
    public function isReferenced ( ) {

        return $this->_isReferenced;
    }

    /**
     * Get function name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get function arguments.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
    }

    /**
     * Get function body.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function getBody ( ) {

        if(   isset($this->_body[0])
           && ($this->_body[0] instanceof Hoa_Pom_Token_LateParsing))
            throw new Hoa_Pom_Token_Util_Exception(
                'Cannot run the late parser because it is not yet coded :-P.', 0);

        return $this->_body;
    }

    /**
     * Check if function has a body.
     *
     * @access  public
     * @return  array
     */
    public function hasBody ( ) {

        return $this->getBody() == array();
    }
}
