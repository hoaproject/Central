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
 * @subpackage  Hoa_Pom_Token_Class
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
 * Hoa_Pom_Token_Util_Interface_SuperScalar
 */
import('Pom.Token.Util.Interface.SuperScalar');

/**
 * Hoa_Pom_Token_Util_Interface_Type
 */
import('Pom.Token.Util.Interface.Type');

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
 * Hoa_Pom_Token_Class_Constant
 */
import('Pom.Token.Class.Constant');

/**
 * Hoa_Pom_Token_Class_Attribute
 */
import('Pom.Token.Class.Attribute');

/**
 * Hoa_Pom_Token_Class_Method
 */
import('Pom.Token.Class.Method');

/**
 * Class Hoa_Pom_Token_Class.
 *
 * Represent a class.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Class
 */

class Hoa_Pom_Token_Class implements Hoa_Pom_Token_Util_Interface_Tokenizable,
                                     Hoa_Pom_Token_Util_Interface_SuperScalar,
                                     Hoa_Pom_Token_Util_Interface_Type {

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
     * @var Hoa_Pom_Token_Comment object
     */
    protected $_comment    = null;

    /**
     * Class name.
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_name       = null;

    /**
     * Whether class is final.
     *
     * @var Hoa_Pom_Token_Class bool
     */
    protected $_isFinal    = false;

    /**
     * Whether class is abstract.
     *
     * @var Hoa_Pom_Token_Class bool
     */
    protected $_isAbstract = false;

    /**
     * Parent class name.
     *
     * @var Hoa_Pom_Token_String object
     */
    protected $_parent     = null;

    /**
     * Collection of interface names.
     *
     * @var Hoa_Pom_Token_Class array
     */
    protected $_interfaces = array();

    /**
     * Collection of constants.
     *
     * @var Hoa_Pom_Token_Class array
     */
    protected $_constants  = array();

    /**
     * Collection of attributes.
     *
     * @var Hoa_Pom_Token_Class array
     */
    protected $_attributes = array();

    /**
     * Collection of methods.
     *
     * @var Hoa_Pom_Token_Class array
     */
    protected $_methods    = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Class name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_String $name ) {

        $this->setName($name);

        return;
    }

    /**
     * Set class comment.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Comment  $comment    Class comment.
     * @return  Hoa_Pom_Token_Comment
     */
    public function setComment ( Hoa_Pom_Token_Comment $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Remove class comment.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Comment
     */
    public function removeComment ( ) {

        return $this->setComment(new Hoa_Pom_Token_Comment(null));
    }

    /**
     * Set class name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $name    Class name.
     * @return  Hoa_Pom_Token_String
     */
    public function setName ( Hoa_Pom_Token_String $name ) {

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

        $old               = $this->_isFinal;
        $this->_isFinal    = $final;
        $this->_isAbstract = self::CONCRET_CLASS;

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
        $this->_isFinal    = self::MEMBER_CLASS;

        return $old;
    }

    /**
     * Set the parent class.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $parent    Parent.
     * @return  Hoa_Pom_Token_String
     */
    public function setParent ( Hoa_Pom_Token_String $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Remove class parent.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Class
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
     * @param   Hoa_Pom_Token_String  $interface    Interface name to
     *                                              check.
     * @return  bool
     */
    public function isImplemented ( Hoa_Pom_Token_String $interface ) {

        return isset($this->_interfaces[$interface->getString()]);
    }

    /**
     * Add an interface.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $interface    Interface name.
     * @return  Hoa_Pom_Token_String
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addInterface ( Hoa_Pom_Token_String $interface ) {

        if(true === $this->isImplemented($interface))
            throw new Hoa_Pom_Token_Util_Exception(
                'Interface %s is already implemented.', 0, $interface->getString());

        return $this->_interfaces[$interface->getString()] = $interface;
    }

    /**
     * Remove an interface.
     *
     * @access  public
     * @param   Hoa_Pom_Token_String  $interface    Interface name.
     * @return  array
     */
    public function removeInterface ( Hoa_Pom_Token_String $interface ) {

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
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addConstant ( Hoa_Pom_Token_Class_Constant $constant ) {

        if(true === $this->constantExists($constant))
            throw new Hoa_Pom_Token_Util_Exception(
                'Constant %s already exists.',
                1, $constant->getName()->getString());

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
     * @param   Hoa_Pom_Token_Class_Attribute  $attribute    Attribute to
     *                                                       check.
     * @return  bool
     */
    public function attributeExists ( Hoa_Pom_Token_Class_Attribute $attribute ) {

        return isset($this->_attributes[$attribute->getName()->getName()->getString()]);
    }

    /**
     * Add an attribute.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Attribute  $attribute    Attribute
     *                                                       instance.
     * @return  Hoa_Pom_Token_Class_Attribute
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addAttribute ( Hoa_Pom_Token_Class_Attribute $attribute ) {

        if(true === $this->attributeExists($attribute))
            throw new Hoa_Pom_Token_Util_Exception(
                'Attribute %s alreaday exists.',
                2, $attribute->getName()->getName()->getString());

        return $this->_attributes[$attribute->getName()->getName()->getString()] = $attribute;
    }

    /**
     * Remove an attribute.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Class_Attribute  $attribute    Attribute name.
     * @return  array
     */
    public function removeAttribute ( Hoa_Pom_Token_Class_Attribute $attribute ) {

        unset($this->_attributes[$attribute->getName()->getName()->getString()]);

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
            throw new Hoa_Pom_Token_Util_Exception(
                'Method %s already exists.', 3, $method->getName()->getString());

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
     * @return  Hoa_Pom_Token_String
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
     * @return  Hoa_Pom_Token_Comment
     */
    public function getComment ( ) {

        return $this->_comment;
    }

    /**
     * Check if class has a comment.
     *
     * @access  public
     * @return  bool
     */
    public function hasComment ( ) {

        return null !== $this->getComment();
    }

    /**
     * Get parent.
     *
     * @access  public
     * @return  Hoa_Pom_Token_String
     */
    public function getParent ( ) {

        return $this->_parent;
    }

    /**
     * Check if class has parent.
     *
     * @access  public
     * @return  bool
     */
    public function hasParent ( ) {

        return null !== $this->getParent();
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
     * Check if class has one or many interfaces.
     *
     * @access  public
     * @return  bool
     */
    public function hasInterfaces ( ) {

        return $this->getInterfaces() != array();
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

        $ifirst     = true;
        $interfaces = array();

        foreach($this->getInterfaces() as $i => $interface) {

            if(false === $ifirst)
                $interfaces[] = array(
                    0 => Hoa_Pom::_COMMA,
                    1 => ',',
                    2 => -1
                );
            else
                $ifirst = false;

            $handle       = $interface->tokenize();
            $interfaces[] = $handle[0];
        }

        $constants  = array();

        foreach($this->getConstants() as $i => $constant)
            foreach($constant->tokenize() as $key => $value)
                $constants[] = $value;

        $attributes = array();

        foreach($this->getAttributes() as $i => $attribute)
            foreach($attribute->tokenize() as $key => $value)
                $attributes[] = $value;

        $methods    = array();

        foreach($this->getMethods() as $i => $method)
            foreach($method->tokenize() as $key => $value)
                $methods[] = $value;

        return array_merge(
            (true === $this->hasComment()
                 ? $this->getComment()->tokenize()
                 : array()
            ),
            (true === $this->isAbstract()
                 ? array(array(
                       0 => Hoa_Pom::_ABSTRACT,
                       1 => 'abstract',
                       2 => -1
                   ))
                 : array()
            ),
            (true === $this->isFinal()
                 ? array(array(
                       0 => Hoa_Pom::_FINAL,
                       1 => 'final',
                       2 => -1
                   ))
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_CLASS,
                1 => 'class',
                2 => -1
            )),
            $this->getName()->tokenize(),
            (true === $this->hasParent()
                 ? array_merge(
                       array(array(
                           0 => Hoa_Pom::_EXTENDS,
                           1 => 'extends',
                           2 => -1
                       )),
                       $this->getParent()->tokenize()
                   )
                 : array()
            ),
            (true === $this->hasInterfaces()
                 ? array_merge(
                       array(array(
                           0 => Hoa_Pom::_IMPLEMENTS,
                           1 => 'implements',
                           2 => -1
                       )),
                       $interfaces
                   )
                 : array()
            ),
            array(array(
                0 => Hoa_Pom::_OPEN_BRACE,
                1 => '{',
                2 => -1
            )),
            $constants,
            $attributes,
            $methods,
            array(array(
                0 => Hoa_Pom::_CLOSE_BRACE,
                1 => '}',
                2 => -1
            ))
        );
    }
}
