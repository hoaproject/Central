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
 * Hoa_Tokenizer_Token_Util_Interface_Tokenizable
 */
import('Tokenizer.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Tokenizer_Token_Util_Interface_SuperScalar
 */
import('Tokenizer.Token.Util.Interface.SuperScalar');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Comment
 */
import('Tokenizer.Token.Comment');

/**
 * Hoa_Tokenizer_Token_Util_Interface_Type
 */
import('Tokenizer.Token.Util.Interface.Type');

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

class Hoa_Tokenizer_Token_Class implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable,
                                           Hoa_Tokenizer_Token_Util_Interface_SuperScalar,
                                           Hoa_Tokenizer_Token_Util_Interface_Type {

    /**
     * Class is final.
     *
     * @const bool
     */
    const FINAL_CLASS      = true;

    /**
     * Class is a member of the family, i.e. not final.
     *
     * @const bool
     */
    const MEMBER_CLASS     = false;

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
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_name       = null;

    /**
     * Whether class is final.
     *
     * @var Hoa_Tokenizer_Token_Class bool
     */
    protected $_isFinal    = false;

    /**
     * Whether class is abstract.
     *
     * @var Hoa_Tokenizer_Token_Class bool
     */
    protected $_isAbstract = false;

    /**
     * Parent class name.
     *
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_parent     = null;

    /**
     * Collection of interface names.
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
     * @param   Hoa_Tokenizer_Token_String  $name    Class name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

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
     * @param   Hoa_Tokenizer_Token_String  $name    Class name.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setName ( Hoa_Tokenizer_Token_String $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Final class.
     *
     * @access  public
     * @param   bool    $final    Whether class is final, given by
     *                            constants self::FINAL_CLASS or
     *                            self::MEMBER_CLASS.
     * @return  bool
     */
    public function finalMe ( $final = self::FINAL_CLASS ) {

        $this->abstractMe(self::CONCRET_CLASS);

        $old            = $this->_isFinal;
        $this->_isFinal = $final;

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

        $this->finalMe(self::MEMBER_CLASS);

        $old               = $this->_isAbstract;
        $this->_isAbstract = $abstract;

        return $old;
    }

    /**
     * Set the parent class.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $parent    Parent.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setParent ( Hoa_Tokenizer_Token_String $parent ) {

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
     * @param   Hoa_Tokenizer_Token_String  $interface    Interface name to
     *                                                    check.
     * @return  bool
     */
    public function isImplemented ( Hoa_Tokenizer_Token_String $interface ) {

        return isset($this->_interfaces[$interface->getString()]);
    }

    /**
     * Add an interface.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $interface    Interface name.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function addInterface ( Hoa_Tokenizer_Token_String $interface ) {

        if(true === $this->isImplemented($interface))
            return;

        return $this->_interfaces[$interface->getString()] = $interface;
    }

    /**
     * Remove an interface.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $interface    Interface name.
     * @return  array
     */
    public function removeInterface ( Hoa_Tokenizer_Token_String $interface ) {

        unset($this->_interfaces[$interface->getString()]);

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
     * @param   Hoa_Tokenizer_Token_Class_Constant   $constant    Constant to
     *                                                            check.
     * @return  bool
     */
    public function constantExists ( Hoa_Tokenizer_Token_Class_Constant $constant ) {

        return isset($this->_constants[$constant->getName()->getString()]);
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

        return $this->_constants[$constant->getName()->getString()] = $constant;
    }

    /**
     * Remove a constant.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Constant  $constant    Constant name.
     * @return  array
     */
    public function removeConstant ( Hoa_Tokenizer_Token_Class_Constant $constant ) {

        unset($this->_constants[$constant->getName()->getString()]);

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
     * @param   Hoa_Tokenizer_Token_Class_Attribute  $attribute    Attribute to
     *                                                             check.
     * @return  bool
     */
    public function attributeExists ( Hoa_Tokenizer_Token_Class_Attribute $attribute ) {

        return isset($this->_attributes[$attribute->getName()->getString()]);
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

        return $this->_attributes[$attribute->getName()->getString()] = $attribute;
    }

    /**
     * Remove an attribute.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Attribute  $attribute    Attribute name.
     * @return  array
     */
    public function removeAttribute ( Hoa_Tokenizer_Token_Class_Attribute $attribute ) {

        unset($this->_attributes[$attribute->getName()->getString()]);

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
     * @param   Hoa_Tokenizer_Token_Class_Method  $method    Method to check.
     * @return  bool
     */
    public function methodExists ( Hoa_Tokenizer_Token_Class_Method $method ) {

        return isset($this->_methods[$method->getName()->getString()]);
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

        return $this->_methods[$method->getName()->getString()] = $method;
    }

    /**
     * Remove a method.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Method  $method    Method name.
     * @return  array
     */
    public function removeMethod ( Hoa_Tokenizer_Token_Class_Method $method ) {

        unset($this->_methods[$method->getName()->getString()]);

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
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Whether class is final.
     *
     * @access  public
     * @return  bool
     */
    public function isFinal ( ) {

        return $this->_isFinal;
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
     * @return  Hoa_Tokenizer_Token_String
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
     * Check if a data is an uniform super-scalar or not.
     *
     * @access  public
     * @return  bool
     */
    public function isUniformSuperScalar ( ) {

        return false;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array(array());
    }
}
