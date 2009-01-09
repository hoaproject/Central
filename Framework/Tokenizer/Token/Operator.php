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
 * @subpackage  Hoa_Tokenizer_Token_Operator
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
 * Class Hoa_Tokenizer_Token_Operator.
 *
 * Represent an operator.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Operator
 */

abstract class Hoa_Tokenizer_Token_Operator implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Operator.
     *
     * @var Hoa_Tokenizer_Token_Operator string
     */
    protected $_operator = null;

    /**
     * Operator type.
     *
     * @var Hoa_Tokenizer_Token_Operator mixed
     */
    protected $_type     = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $operator    Operator.
     * @return  void
     */
    public function __construct ( $operator ) {

        $this->setOperator($operator);

        return;
    }

    /**
     * Set operator.
     *
     * @access  public
     * @param   string  $operator    Operator.
     * @return  string
     */
    public function setOperator ( $operator ) {

        $old             = $this->_operator;
        $this->_operator = $operator;

        return $old;
    }

    /**
     * Set type.
     *
     * @access  protected
     * @param   mixed      $type    Type of operator.
     * @return  mixed
     */
    protected function setType ( $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Get operator.
     *
     * @access  public
     * @return  string
     */
    public function getOperator ( ) {

        return $this->_operator;
    }

    /**
     * Get type.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getType ( ) {

        return $this->_type;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array(array(
            $this->getType(),
            $this->getOperator(),
            -1
        ));
    }
}
