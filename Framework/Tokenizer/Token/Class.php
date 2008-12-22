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
 * @subpackage  Hoa_Tokenizer_Token_Class
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
 * Hoa_Tokenizer_Token_Comment
 */
import('Tokenizer.Token.Comment');

/**
 * Hoa_Tokenizer_Token_Interface
 */
import('Tokenizer.Token.Interface');

/**
 * Hoa_Tokenizer_Token_Class_Constant
 */
import('Tokenizer.Token.Class.Constant');

/**
 * Hoa_Tokenizer_Token_Class_Attribute
 */
import('Tokenizer.Token.Class.Attribute');

/**
 * Hoa_Tokenizer_Token_Class_Method
 */
import('Tokenizer.Token.Class.Method');

/**
 * Class Hoa_Tokenizer_Token_Class.
 *
 * Represent a class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Class
 */

class Hoa_Tokenizer_Token_Class implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Class is abstract.
     *
     * @const bool
     */
    const ABSTRACT_CLASS   = true;

    /**
     * Class is concret, i.e. not abstract.
     *
     * @const bool
     */
    const CONCRET_CLASS    = false;

    /**
     * Class comment.
     *
     * @var Hoa_Tokenizer_Token_Comment object
     */
    protected $_comment    = null;

    /**
     * Class name.
     *
     * @var Hoa_Tokenizer_Token_Class string
     */
    protected $_name       = null;

    /**
     * Whether class is abstract.
     *
     * @var Hoa_Tokenizer_Token_Class bool
     */
    protected $_isAbstract = false;

    /**
     * Parent class.
     *
     * @var Hoa_Tokenizer_Token_Class object
     */
    protected $_parent     = null;

    /**
     * Collection of interfaces.
     *
     * @var Hoa_Tokenizer_Token_Class array
     */
    protected $_interfaces = array();

    /**
     * Collection of constants.
     *
     * @var Hoa_Tokenizer_Token_Class array
     */
    protected $_constants  = array();

    /**
     * Collection of attributes.
     *
     * @var Hoa_Tokenizer_Token_Class array
     */
    protected $_attributes = array();

    /**
     * Collection of methods.
     *
     * @var Hoa_Tokenizer_Token_Class array
     */
    protected $_methods    = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $name    Class name.
     * @return  void
     */
    public function __construct ( $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set class comment.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Comment  $comment    Class comment.
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function setComment ( Hoa_Tokenizer_Token_Comment $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Remove class comment.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function removeComment ( ) {

        return $this->setComment(new Hoa_Tokenizer_Token_Comment(null));
    }

    /**
     * Set class name.
     *
     * @access  public
     * @param   string  $name    Class name.
     * @return  string
     */
    public function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Abstract class.
     *
     * @access  public
     * @param   bool    $abstract    Whether class is abstract, given by
     *                               constants self::ABSTRACT_CLASS or
     *                               self::CONCRET_CLASS.
     * @return  bool
     */
    public function abstractMe ( $abstract = self::ABSTRACT_CLASS ) {

        $old               = $this->_isAbstract;
        $this->_isAbstract = $abstract;

        return $old;
    }

    /**
     * Set the parent class.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class  $parent    Parent name.
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function setParent ( Hoa_Tokenizer_Token_Class $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Check if class has a parent.
     *
     * @access  public
     * @return  bool
     */
    public function hasParent ( ) {

        return null !== $this->getParent();
    }

    /**
     * Remove class parent.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function removeParent ( ) {

        return $this->setParent(null);
    }

    /**
     * Add many interfaces.
     *
     * @access  public
     * @param   array   $interfaces    Interfaces to add.
     * @return  array
     */
    public function addInterfaces ( Array $interfaces ) {

        foreach($interfaces as $i => $interface)
            $this->addInterface($interface);

        return $this->_interfaces;
    }

    /**
     * Check if an interface is implemented.
     *
     * @access  public
     * @param   mixed   $interface    Interface to check. Could be a string or
     *                                a Hoa_Tokenizer_Token_Interface instance.
     * @return  bool
     */
    public function isImplemented ( $interface ) {

        if($interface instanceof Hoa_Tokenizer_Token_Interface)
            $interface = $interface->getName();

        foreach($this->getInterfaces() as $i => $ii)
            if($ii->getName() == $interface)
                return true;

        return false;
    }

    /**
     * Add an interface.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Interface  $interface    Interface instance.
     * @return  Hoa_Tokenizer_Token_Interface
     */
    public function addInterface ( Hoa_Tokenizer_Token_Interface $interface ) {

        if(true === $this->isImplemented($interface))
            return;

        return $this->_interfaces[] = $interface;
    }

    /**
     * Remove an interface.
     *
     * @access  public
     * @param   mixed   $interface    Interface name. Could be a string or a
     *                                Hoa_Tokenizer_Token_Interface instance.
     * @return  array
     */
    public function removeInterface ( $interface ) {

        if($interface instanceof Hoa_Tokenizer_Token_Interface)
            $interface = $interface->getName();

        if(false === $this->isImplemented($interface))
            return $this->_interfaces;

        foreach($this->getInterfaces() as $i => $ii)
            if($ii->getName() == $interface) {

                unset($this->_interfaces[$i]);
                break;
            }

        return $this->_interfaces;
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
     * Add many attributes.
     *
     * @access  public
     * @param   array   $attributes    Attributes to add.
     * @return  array
     */
    public function addAttributes ( Array $attributes ) {

        foreach($attributes as $i => $attribute)
            $this->addAttribute($attribute);

        return $this->_attributes;
    }

    /**
     * Check if an attribute exists.
     *
     * @access  public
     * @param   mixed   $attribute    Attribute to check. Could be a string or
     *                                a Hoa_Tokenizer_Token_Class_Attribute
     *                                instance.
     * @return  bool
     */
    public function attributeExists ( $attribute ) {

        if($attribute instanceof Hoa_Tokenizer_Token_Class_Attribute)
            $attribute = $attribute->getName();

        foreach($this->getAttributes() as $i => $a)
            if($a->getName() == $attribute)
                return true;

        return false;
    }

    /**
     * Add an attribute.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Attribute  $attribute    Attribute
     *                                                             instance.
     * @return  Hoa_Tokenizer_Token_Class_Attribute
     */
    public function addAttribute ( Hoa_Tokenizer_Token_Class_Attribute $attribute ) {

        if(true === $this->attributeExists($attribute))
            return;

        return $this->_attributes[] = $attribute;
    }

    /**
     * Remove an attribute.
     *
     * @access  public
     * @param   mixed   $attribute    Attribute name. Could be a string or a
     *                                Hoa_Tokenizer_Token_Class_Attribute
     *                                instance.
     * @return  array
     */
    public function removeAttribute ( $attribute ) {

        if($attribute instanceof Hoa_Tokenizer_Token_Class_Attribute)
            $constant = $constant->getName();

        if(false === $this->attributeExists($attribute))
            return $this->_attributes;

        foreach($this->getAttributes() as $i => $a)
            if($a->getName() == $attribute) {

                unset($this->_attributes[$i]);
                break;
            }

        return $this->_attributes;
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
     * Check if class has a body.
     *
     * @access  public
     * @return  bool
     */
    public function hasBody ( ) {

        return    $this->getConstants()  != array()
               && $this->getAttributes() != array()
               && $this->getMethods()    != array();
    }

    /**
     * Get class name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Whether class is abstract.
     *
     * @access  public
     * @return  bool
     */
    public function isAbstract ( ) {

        return $this->_isAbstract;
    }

    /**
     * Get class comment.
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
     * Get all interfaces.
     *
     * @access  public
     * @return  array
     */
    public function getInterfaces ( ) {

        return $this->_interfaces;
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
     * Get all attributes.
     *
     * @access  public
     * @return  array
     */
    public function getAttributes ( ) {

        return $this->_attributes;
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

        return array();
    }
}
