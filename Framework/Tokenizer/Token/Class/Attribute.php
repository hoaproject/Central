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
 * @subpackage  Hoa_Tokenizer_Token_Class_Attribute
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
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Class_Access
 */
import('Tokenizer.Token.Class.Access');

/**
 * Class Hoa_Tokenizer_Token_Class_Attribute.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Class_Attribute
 */

class Hoa_Tokenizer_Token_Class_Attribute implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Attribute comment.
     *
     * @var Hoa_Tokenizer_Token_Comment object
     */
    protected $_comment  = null;

    /**
     * Attribute access.
     *
     * @var Hoa_Tokenizer_Token_Class_Access object
     */
    protected $_access   = null;

    /**
     * Attribuale name (variable).
     *
     * @var Hoa_Tokenizer_Token_Variable object
     */
    protected $_name     = null;

    /**
     * Attribute operator.
     *
     * @var Hoa_Tokenizer_Token_Operator_Assignement object
     */
    protected $_operator = null;

    /**
     * Attribute value.
     *
     * @var mixed object
     */
    protected $_value    = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $name    Attribute name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

        $this->setAccess(new Hoa_Tokenizer_Token_Class_Access('public'));
        $this->setOperator();
        $this->setName($name);

        return;
    }

    /**
     * Set comment.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Comment  $comment    Attribute comment.
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function setComment ( Hoa_Tokenizer_Token_Comment $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Set access.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Class_Access  $access  Attribute access.
     * @return  Hoa_Tokenizer_Token_Class_Access
     */
    public function setAccess ( Hoa_Tokenizer_Token_Class_Access $access ) {

        $old           = $this->_access;
        $this->_access = $access;

        return $old;
    }

    /**
     * Set name (variable).
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Variable  $variable    Variable.
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function setName ( Hoa_Tokenizer_Token_Variable $variable ) {

        $old         = $this->_name;
        $this->_name = $variable;

        return $old;
    }

    /**
     * Set operator.
     *
     * @access  protected
     * @param   Hoa_Tokenizer_Token_Operator_Assignement  $operator    Operator.
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    protected function setOperator ( Hoa_Tokenizer_Token_Operator_Assignement $operator ) {

        $old             = $this->_operator;
        $this->_operator = new Hoa_Tokenizer_Token_Operator_Assignement('=');

        return $old;
    }

    /**
     * Set value.
     *
     * @access  public
     * @param   mixed   $value    Attribute value.
     * @return  mixed
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function setValue ( $value ) {

        if($value instanceof Hoa_Tokenizer_Token_Util_Interface_SuperScalar)
            if(false === $value->isUniformSuperScalar())
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Value should effectively be a super-scalar, ' .
                    'but an uniform super-scalar.', 0);

        if(!($value instanceof Hoa_Tokenizer_Token_Util_Interface_Scalar))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Value must be a scalar or an uniform super-scalar.', 1);

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get comment.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Comment
     */
    public function getComment ( ) {

        return $this->_comment;
    }

    /**
     * Get access.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Class_Access
     */
    public function getAccess ( ) {

        return $this->_access;
    }

    /**
     * Get name.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Variable
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get operator.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    public function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Get value.
     *
     * @access  public
     * @return  mixed
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            $this->getComment()->tokenize(),
            $this->getAccess()->tokenize(),
            $this->getName()->tokenize(),
            $this->getOperator()->tokenize()
            $this->getValue()->tokenize()
        );
    }
}
