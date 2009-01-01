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
 * Hoa_Tokenizer_Token_Util_Interface
 */
import('Tokenizer.Token.Util.Interface');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Class
 */
import('Tokenizer.Token.Class');

/**
 * Hoa_Tokenizer_Token_String
 */
import('Tokenizer.Token.String');

/**
 * Hoa_Tokenizer_Token_Number_DNumber
 */
import('Tokenizer.Token.Number.DNumber');

/**
 * Hoa_Tokenizer_Token_Number_LNumber
 */
import('Tokenizer.Token.Number.LNumber');

/**
 * Hoa_Tokenizer_Token_Comment
 */
import('Tokenizer.Token.Comment');

/**
 * Hoa_Tokenizer_Token_Operator_Assign
 */
import('Tokenizer.Token.Operator.Assign');

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

class Hoa_Tokenizer_Token_Class_Constant implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Constant class (that contains this constant).
     *
     * @var Hoa_Tokenizer_Token_Class object
     */
    protected $_class    = null;

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
     * Constant value. Could be a Hoa_Tokenizer_Token_String or a
     * Hoa_Tokenizer_Token_Number instance.
     *
     * @var mixed object
     */
    protected $_value    = null;

    /**
     * Constant operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Assign object
     */
    protected $_operator = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed                      $name     Constant name. Could be a
     *                                               string or a
     *                                               Hoa_Tokenizer_Token_String
     *                                               instance.
     * @param   Hoa_Tokenizer_Token_Class  $class    Class that contains this
     *                                               constant.
     * @return  void
     */
    public function __construct ( $name, Hoa_Tokenizer_Token_Class $class ) {

        $this->setClass($name);
        $this->setName($name);
        $this->setOperator();

        return;
    }

    /**
     * Set constant class.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class  $clas    Class that contains this
     *                                              constant.
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function setClass ( Hoa_Tokenizer_Token_Class $class ) {

        $old          = $this->_class;
        $this->_class = $class;

        return $old;
    }

    /**
     * Set constant comment.
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
     * @param   mixed   $name    Constant name. Could be a string or a
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
     * Set constant value.
     *
     * @access  public
     * @param   mixed   $value    Constant value. Could be a
     *                            Hoa_Tokenizer_Token_String or a
     *                            Hoa_Tokenizer_Token_Number instance.
     * @return  mixed
     */
    public function setValue ( $value ) {

        if(is_string($value))
            $value = new Hoa_Tokenizer_Token_String($value);
        elseif(is_int($value))
            $value = new Hoa_Tokenizer_Token_Number_LNumber($value);
        elseif(is_float($value))
            $value = new Hoa_Tokenizer_Token_Number_DNumber($value);

        if(   !($value instanceof Hoa_Tokenizer_Token_String)
           && !($value instanceof Hoa_Tokeniezr_Token_Number))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Constant value must be an instance of '.
                'Hoa_Tokenizer_Token_String or Hoa_Tokenizer_Token_Number. ' .
                'Given %s.', 0, gettype($value));

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Set the operator.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Operator_Assign
     */
    protected function setOperator ( ) {

        return $this->_operator = new Hoa_Tokenizer_Token_Operator_Assign('=');
    }

    /**
     * Get class.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Class
     */
    public function getClass ( ) {

        return $this->_class;
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
     * Get constant value.
     *
     * @access  public
     * @return  mixed
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get constant operator.
     *
     * @access  protected
     * @return  Hoa_Tokenizer_Token_Operator_Assign
     */
    protected function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @param   int     $context    Context.
     * @return  array
     */
    public function toArray ( $context = Hoa_Tokenizer::CONTEXT_STANDARD ) {

        /*
        if(   $context == Hoa_Tokenizer::CONTEXT_STANDARD
           || $context == Hoa_Tokenizer::CONTEXT_DECLARATION)
        */
            return array_merge(
                $this->getComment()->toArray(),
                array(array(
                    Hoa_Tokenizer::_CONST,
                    'const',
                    -1
                )),
                $this->getName()->toArray(),
                $this->getOperator()->toArray(),
                $this->getValue()->toArray(),
                array(array(
                    Hoa_Tokenizer::_SEMI_COLON,
                    ';',
                    -1
                ))
            );

        /*
        else
            return array_merge(
                $this->getClass()->getName()->toArray(),
                array(
                    Hoa_Tokenizer::_DOUBLE_COLON,
                    '::',
                    -1
                ),
                $this->getName()->toArray()
            );
        */
    }
}
