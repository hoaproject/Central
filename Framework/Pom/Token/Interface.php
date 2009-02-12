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
 * @subpackage  Hoa_Pom_Token_Interface
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
 * Hoa_Pom_Token_Util_Interface_Tokenizable
 */
import('Pom.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Comment
 */
import('Pom.Token.Comment');

/**
 * Hoa_Pom_Token_Util_Interface_Type
 */
import('Pom.Token.Util.Interface.Type');

/**
 * Hoa_Pom_Token_Comment
 */
import('Pom.Token.Comment');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Hoa_Pom_Token_Class_Constant
 */
import('Pom.Token.Class.Constant');

/**
 * Hoa_Pom_Token_Class_Method
 */
import('Pom.Token.Class.Method');

/**
 * Class Hoa_Pom_Token_Interface.
 *
 * Represent an interface.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Interface
 */

class Hoa_Pom_Token_Interface implements Hoa_Pom_Token_Util_Interface_Tokenizable,
                                         Hoa_Pom_Token_Util_Interface_Type {

    /**
     * Interface comment.
     *
     * @var Hoa_Pom_Token_Comment object
     */
    protected $_comment   = null;

    /**
     * Interface name.
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_name      = null;

    /**
     * Collection of parent names.
     *
     * @var Hoa_Pom_Token_Interface array
     */
    protected $_parents   = array();

    /**
     * Collection of constants.
     *
     * @var Hoa_Pom_Token_Interface array
     */
    protected $_constants = array();

    /**
     * Collection of methods.
     *
     * @var Hoa_Pom_Token_Class array
     */
    protected $_methods   = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Interface name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_String $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set interface comment.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Comment  $comment    Interface comment.
     * @return  Hoa_Pom_Token_Comment
     */
    public function setComment ( Hoa_Pom_Token_Comment $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Remove interface comment.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Comment
     */
    public function removeComment ( ) {

        return $this->setComment(new Hoa_Pom_Token_Comment(null));
    }

    /**
     * Set interface name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Interface name.
     * @return  Hoa_Pom_Token_String
     */
    public function setName ( Hoa_Pom_Token_String $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Add many parents.
     *
     * @access  public
     * @param   array   $parents    Parents to add.
     * @return  array
     */
    public function addParents ( Array $parents ) {

        foreach($parents as $i => $parent)
            $this->addParent($parent);

        return $this->_parents;
    }

    /**
     * Check if a parent exists.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $parent     Parent name to
     *                                            check.
     * @return  bool
     */
    public function parentExists ( Hoa_Pom_Token_String $parent ) {

        return isset($this->_parents[$parent->getString()]);
    }

    /**
     * Add a parent.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $parent    Parent name.
     * @return  Hoa_Pom_Token_String
     */
    public function addParent ( Hoa_Pom_Token_String $parent ) {

        if(true === $this->parentExists($parent))
            return;

        return $this->_parents[$parent->getString()] = $parent;
    }

    /**
     * Remove a parent.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $parent    Parent name.
     * @return  array
     */
    public function removeParent ( Hoa_Pom_Token_String $parent ) {

        unset($this->_parents[$parent->getString()]);

        return $this->_parents;
    }

    /**
     * Add many constants.
     *
     * @access  public
     * @param   array   $constants    Constants to add.
     * @return  array
     */
    public function addConstants ( Array $constants ) {

        foreach($constants as $i => $constant)
            $this->addConstant($constant);

        return $this->_constants;
    }

    /**
     * Check if a constant exists.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Constant   $constant    Constant to
     *                                                      check.
     * @return  bool
     */
    public function constantExists ( Hoa_Pom_Token_Class_Constant $constant ) {

        return isset($this->_constants[$constant->getName()->getString()]);
    }

    /**
     * Add a constant.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Constant  $constant    Constant
     *                                                     instance.
     * @return  Hoa_Pom_Token_Class_Constant
     */
    public function addConstant ( Hoa_Pom_Token_Class_Constant $constant ) {

        if(true === $this->constantExists($constant))
            return;

        return $this->_constants[$constant->getName()->getString()] = $constant;
    }

    /**
     * Remove a constant.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Constant  $constant    Constant name.
     * @return  array
     */
    public function removeConstant ( Hoa_Pom_Token_Class_Constant $constant ) {

        unset($this->_constants[$constant->getName()->getString()]);

        return $this->_constants;
    }

    /**
     * Add many methods.
     *
     * @access  public
     * @param   array   $methods    Methods to add.
     * @return  array
     */
    public function addMethods ( Array $methods ) {

        foreach($methods as $i => $method)
            $this->addMethod($method);

        return $this->_methods;
    }

    /**
     * Check if a method exists.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Method  $method    Method to check.
     * @return  bool
     */
    public function methodExists ( Hoa_Pom_Token_Class_Method $method ) {

        return isset($this->_methods[$method->getName()->getString()]);
    }

    /**
     * Add a method.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Method  $method    Method instance.
     * @return  Hoa_Pom_Token_Class_Method
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addMethod ( Hoa_Pom_Token_Class_Method $method ) {

        if(true === $this->methodExists($method))
            return;

        if(true === $method->hasBody())
            throw new Hoa_Tokenizen_Token_Util_Exception(
                'Interface only accepts empty methods. ' .
                'Method %s a body.', 0, $method->getName()->getString());

        return $this->_methods[$method->getName()->getString()] = $method;
    }

    /**
     * Remove a method.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Method  $method    Method name.
     * @return  array
     */
    public function removeMethod ( Hoa_Pom_Token_Class_Method $method ) {

        unset($this->_methods[$method->getName()->getString()]);

        return $this->_methods;
    }

    /**
     * Check if interface has a body.
     *
     * @access  public
     * @return  bool
     */
    public function hasBody ( ) {

        return    $this->getConstants() != array()
               && $this->getMethods()   != array();
    }

    /**
     * Get interface name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get interface comment.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Comment
     */
    public function getComment ( ) {

        return $this->_comment;
    }

    /**
     * Get all parents.
     *
     * @access  public
     * @return  array
     */
    public function getParents ( ) {

        return $this->_parents;
    }

    /**
     * Get all constants.
     *
     * @access  public
     * @return  array
     */
    public function getConstants ( ) {

        return $this->_constants;
    }

    /**
     * Get all methods.
     *
     * @access  public
     * @return  array
     */
    public function getMethods ( ) {

        return $this->_methods;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        // @todo.
        return array(array());
    }
}
