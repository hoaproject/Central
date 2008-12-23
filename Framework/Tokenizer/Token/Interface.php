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
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Interface
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Token_Util_Exception
 */
import('Tokenizer.Token.Util.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Interface
 */
import('Tokenizer.Token.Util.Interface');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_String
 */
import('Tokenizer.Token.String');

/**
 * Hoa_Tokenizer_Token_Comment
 */
import('Tokenizer.Token.Comment');

/**
 * Hoa_Tokenizer_Token_Class_Constant
 */
import('Tokenizer.Token.Class.Constant');

/**
 * Hoa_Tokenizer_Token_Class
 */
import('Tokenizer.Token.Class');

/**
 * Hoa_Tokenizer_Token_Class_Method
 */
import('Tokenizer.Token.Class.Method');

/**
 * Class Hoa_Tokenizer_Token_Interface.
 *
 * Represent an interface.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Interface
 */

class Hoa_Tokenizer_Token_Interface implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Interface comment.
     *
     * @var Hoa_Tokenizer_Token_Comment object
     */
    protected $_comment    = null;

    /**
     * Interface name.
     *
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_name       = null;

    /**
     * Parent interface.
     *
     * @var Hoa_Tokenizer_Token_Class object
     */
    protected $_parent     = null;

    /**
     * Collection of constants.
     *
     * @var Hoa_Tokenizer_Token_Class array
     */
    protected $_constants  = array();

    /**
     * Collection of methods.
     *
     * @var Hoa_Tokenizer_Token_Interface array
     */
    protected $_methods    = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $name    Interface name. Could be a string or a
     *                           Hoa_Tokenizer_Token_String instance.
     * @return  void
     */
    public function __construct ( $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set interface comment.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Comment  $comment    Interface comment.
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function setComment ( Hoa_Tokenizer_Token_Comment $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Remove interface comment.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function removeComment ( ) {

        return $this->setComment(new Hoa_Tokenizer_Token_Comment(null));
    }

    /**
     * Set interface name.
     *
     * @access  public
     * @param   mixed   $name    Interface name. Could be a string or a
     *                           Hoa_Tokenizer_Token_String instance.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setName ( $name ) {

        if(!($name instanceof Hoa_Tokenizer_Token_String))
            $name    = new Hoa_Tokenizer_Token_String($name);

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Set the parent interface.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class  $parent    Parent.
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function setParent ( Hoa_Tokenizer_Token_Class $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Check if interface has a parent.
     *
     * @access  public
     * @return  bool
     */
    public function hasParent ( ) {

        return null !== $this->getParent();
    }

    /**
     * Remove interface parent.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function removeParent ( ) {

        return $this->setParent(null);
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
     * @param   mixed   $constant    Constant to check. Could be a string or
     *                               a Hoa_Tokenizer_Token_Class_Constant
     *                               instance.
     * @return  bool
     */
    public function constantExists ( $constant ) {

        if($constant instanceof Hoa_Tokenizer_Token_Class_Constant)
            $constant = $constant->getName();

        foreach($this->getConstants() as $i => $c)
            if($c->getName() == $constant)
                return true;

        return false;
    }

    /**
     * Add a constant.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Constant  $constant    Constant
     *                                                           instance.
     * @return  Hoa_Tokenizer_Token_Class_Constant
     */
    public function addConstant ( Hoa_Tokenizer_Token_Class_Constant $constant ) {

        if(true === $this->constantExists($constant))
            return;

        return $this->_constants[] = $constant;
    }

    /**
     * Remove a constant.
     *
     * @access  public
     * @param   mixed   $constant    Constant name. Could be a string or a
     *                               Hoa_Tokenizer_Token_Class_Constant
     *                               instance.
     * @return  array
     */
    public function removeConstant ( $constant ) {

        if($constant instanceof Hoa_Tokenizer_Token_Class_Constant)
            $constant = $constant->getName();

        if(false === $this->constantExists($constant))
            return $this->_constants;

        foreach($this->getConstants() as $i => $c)
            if($c->getName() == $constant) {

                unset($this->_constants[$i]);
                break;
            }

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
     * @param   mixed   $method    Method to check. Could be a string or
     *                             a Hoa_Tokenizer_Token_Class_Method instance.
     * @return  bool
     */
    public function methodExists ( $method ) {

        if($method instanceof Hoa_Tokenizer_Token_Class_Method)
            $method = $method->getName();

        foreach($this->getMethods() as $i => $m)
            if($m->getName() == $method)
                return true;

        return false;
    }

    /**
     * Add a method.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Method  $method    Method instance.
     * @return  Hoa_Tokenizer_Token_Class_Method
     */
    public function addMethod ( Hoa_Tokenizer_Token_Class_Method $method ) {

        if(true === $this->methodExists($method))
            return;

        return $this->_methods[] = $method;
    }

    /**
     * Remove a method.
     *
     * @access  public
     * @param   mixed   $method    Method name. Could be a string or a
     *                             Hoa_Tokenizer_Token_Class_Method instance.
     * @return  array
     */
    public function removeMethod ( $method ) {

        if($method instanceof Hoa_Tokenizer_Token_Class_Method)
            $method = $method->getName();

        if(false === $this->methodExists($method))
            return $this->_methods;

        foreach($this->getMethods() as $i => $m)
            if($m->getName() == $method) {

                unset($this->_methods[$i]);
                break;
            }

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
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get interface comment.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function getComment ( ) {

        return $this->_comment;
    }

    /**
     * Get parent.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function getParent ( ) {

        return $this->_parent;
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
    public function toArray ( ) {

        return array(array());
    }
}
