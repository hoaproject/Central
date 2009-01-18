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
 * @subpackage  Hoa_Tokenizer_Token_Class_Constant
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
 * Hoa_Tokenizer_Token_Util_Interface_Scalar
 */
import('Tokenizer.Token.Util.Interface.Scalar');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Operator_Assignement
 */
import('Tokenizer.Token.Operator.Assignement');

/**
 * Class Hoa_Tokenizer_Token_Class_Constant.
 *
 * Represent a class (or interface) constant.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Class_Constant
 */

class Hoa_Tokenizer_Token_Class_Constant implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable,
                                                    Hoa_Tokenizer_Token_Util_Interface_Scalar {

    /**
     * Constant comment.
     *
     * @var Hoa_Tokenizer_Token_Comment object
     */
    protected $_comment  = null;

    /**
     * Constant name.
     *
     * @var Hoa_Tokenizer_Token_String object
     */
    protected $_name     = null;

    /**
     * Constant value.
     *
     * @var Hoa_Tokenizer_Token_Util_Interface_Scalar object
     */
    protected $_value    = null;

    /**
     * Constant operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Assignement object
     */
    protected $_operator = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $name    Constant name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

        $this->setOperator();
        $this->setName($name);

        return;
    }

    /**
     * Set comment.
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
     * Remove constant comment.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function removeComment ( ) {

        return $this->setComment(new Hoa_Tokenizer_Token_Comment(null));
    }

    /**
     * Set constant name.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $name    Constant name.
     * @return  Hoa_Tokenizer_Token_String
     */
    public function setName ( Hoa_Tokenizer_Token_String $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Set constant value.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Util_Interface_Scalar  $value    Constant
     *                                                               value.
     * @return  Hoa_Tokenizer_Token_Util_Interface_Scalar
     */
    public function setValue ( Hoa_Tokenizer_Token_Util_Interface_Scalar $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Set the operator.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    protected function setOperator ( ) {

        return $this->_operator = new Hoa_Tokenizer_Token_Operator_Assignement('=');
    }

    /**
     * Get constant name.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_String
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get constant comment.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function getComment ( ) {

        return $this->_comment;
    }

    /**
     * Check if constant has a comment.
     *
     * @access  public
     * @return  bool
     */
    public function hasComment ( ) {

        return null !== $this->getComment();
    }

    /**
     * Get constant value.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Util_Interface_Scalar
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Check if constant has a value.
     *
     * @access  public
     * @return  bool
     */
    public function hasValue ( ) {

        return null !== $this->getValue();
    }

    /**
     * Get constant operator.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    protected function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function tokenize ( ) {

        if(false === $this->hasValue())
           throw new Hoa_Tokenizer_Token_Util_Exception(
            'A constant must have a value.', 0);

        return array_merge(
            (true === $this->hasComment()
                 ? $this->getComment()->tokenize()
                 : array()
            ),
            array(array(
                Hoa_Tokenizer::_CONST,
                'const',
                -1
            )),
            $this->getName()->tokenize(),
            $this->getOperator()->tokenize(),
            $this->getValue()->tokenize(),
            array(array(
                Hoa_Tokenizer::_SEMI_COLON,
                ';',
                -1
            ))
        );
    }
}
