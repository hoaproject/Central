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
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Hoa_Tokenizer_Token_Variable_Valued
 */
import('Tokenizer.Token.Variable.Valued');

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

class Hoa_Tokenizer_Token_Class_Attribute extends Hoa_Tokenizer_Token_Variable_Valued {

    /**
     * Attribute comment.
     *
     * @var Hoa_Tokenizer_Token_Comment object
     */
    protected $_comment = null;

    /**
     * Attribute access.
     *
     * @var Hoa_Tokenizer_Token_Class_Access object
     */
    protected $_access  = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_String  $name    Attribute name.
     * @return  void
     */
    public function __construct ( Hoa_Tokenizer_Token_String $name ) {

        $this->setAccess(new Hoa_Tokenizer_Token_Class_Access('public'));
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
     * Set operator.
     * Will not define the new operator. Force “=”.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Operator_Assignement  $operator    Operator.
     * @return  Hoa_Tokenizer_Token_Operator_Assignement
     */
    public function setOperator ( Hoa_Tokenizer_Token_Operator_Assignement $operator ) {

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
                    'but a uniform super-scalar.', 0);

        if(!($value instanceof Hoa_Tokenizer_Token_Util_Interface_Scalar))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Value must be a scalar or a uniform super-scalar.', 1);

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
    public function getAcess ( ) {

        return $this->_access;
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
            parent::tokenize()
        );
    }
}
