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
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_Loop_For
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
 * Hoa_Tokenizer_Token_Instruction_Block
 */
import('Tokenizer.Token.Instruction.Block');

/**
 * Class Hoa_Tokenizer_Token_ControlStructure_Loop_For.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_ControlStructure_Loop_For
 */

class          Hoa_Tokenizer_Token_ControlStructure_Loop_For
    extends    Hoa_Tokenizer_Token_Instruction_Block
    implements Hoa_Tokenizer_Token_Util_Interface_Tokenizable {

    /**
     * Initializing expression.
     *
     * @var Hoa_Tokenizer_Token_ControlStructure_Loop_For array
     */
    protected $_iniExpression  = array();

    /**
     * Condition expression.
     *
     * @var mixed object 
     */
    protected $_condExpression = null;

    /**
     * Next step expression.
     *
     * @var Hoa_Tokenizer_Token_ControlStructure_Loop_For array
     */
    protected $_nextExpression = array();



    /**
     * Add many initializing expressions.
     *
     * @access  public
     * @param   array   $iniExpressions    Expressions to add.
     * @return  array
     */
    public function addIniExpressions ( Array $iniExpressions = array() ) {

        foreach($iniExpressions as $i => $iniExpression)
            $this->addIniExpression($iniExpression);

        return $this->_iniExpression;
    }

    /**
     * Add an initializing expression.
     *
     * @access  public
     * @param   mixed   $iniExpression    Expression to add.
     * @return  mixed
     */
    public function addIniExpression ( $iniExpression) {

        switch(get_class($iniExpression)) {

            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Cast':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_Variable':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'An ini expression cannot be constitued by a class that ' .
                    'is an instance of %s.', 0, get_class($iniExpression));
        }

        return $this->_iniExpression[] = $iniExpression;
    }

    /**
     * Remove an initializing expression.
     *
     * @access  public
     * @param   int     $i    Expression number.
     * @return  array
     */
    public function removeIniExpression ( $i ) {

        unset($this->_iniExpression[$i]);

        return $this->_iniExpression;
    }

    /**
     * Remove all initializing expressions.
     *
     * @access  public
     * @return  array
     */
    public function removeAllIniExpressions ( ) {

        $old = $this->_iniExpression;

        foreach($this->_iniExpression as $i => $iniExpression)
            unset($this->_iniExpression[$i]);

        return $old;
    }

    /**
     * Set conditional expression.
     *
     * @access  public
     * @param   Hoa_Tokenizer_Token_Operation  $condExpression    Expression.
     * @return  Hoa_Tokenizer_Token_Operation
     */
    public function setCondExpression ( Hoa_Tokenizer_Token_Operation $condExpression ) {

        $old                   = $this->_condExpression;
        $this->_condExpression = $condExpression;

        return $old;
    }

    /**
     * Remove conditional expression.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Operation
     */
    public function removeCondExpression ( ) {

        $old                   = $this->_condExpression;
        $this->_condExpression = null;

        return $old;
    }

    /**
     * Add many next expressions.
     *
     * @access  public
     * @param   array   $nextExpressions    Expressions to add.
     * @return  array
     */
    public function addNextExpressions ( Array $nextExpressions = array() ) {

        foreach($nextExpressions as $i => $nextExpression)
            $this->addNextExpression($nextExpression);

        return $this->_nextExpression;
    }

    /**
     * Add a next expression.
     *
     * @access  public
     * @param   mixed   $nextExpression    Expression to add.
     * @return  mixed
     */
    public function addNextExpression ( $nextExpression) {

        switch(get_class($nextExpression)) {

            case 'Hoa_Tokenizer_Token_Call':
            case 'Hoa_Tokenizer_Token_Cast':
            case 'Hoa_Tokenizer_Token_Clone':
            case 'Hoa_Tokenizer_Token_Comment':
            case 'Hoa_Tokenizer_Token_New':
            case 'Hoa_Tokenizer_Token_Number':
            case 'Hoa_Tokenizer_Token_Operation':
            case 'Hoa_Tokenizer_Token_String':
            case 'Hoa_Tokenizer_Token_Variable':
              break;

            default:
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'An next expression cannot be constitued by a class that ' .
                    'is an instance of %s.', 0, get_class($nextExpression));
        }

        return $this->_nextExpression[] = $nextExpression;
    }

    /**
     * Remove a next expression.
     *
     * @access  public
     * @param   int     $i    Expression number.
     * @return  array
     */
    public function removeNextExpression ( $i ) {

        unset($this->_nextExpression[$i]);

        return $this->_nextExpression;
    }

    /**
     * Remove all next expressions.
     *
     * @access  public
     * @return  array
     */
    public function removeAllNextExpressions ( ) {

        $old = $this->_nextExpression;

        foreach($this->_nextExpression as $i => $nextExpression)
            unset($this->_nextExpression[$i]);

        return $old;
    }

    /**
     * Get all initializing expressions.
     *
     * @access  public
     * @return  array
     */
    public function getIniExpressions ( ) {

        return $this->_iniExpressions;
    }

    /**
     * Get an initializing expression.
     *
     * @access  public
     * @param   int     $i    Expression to get.
     * @return  mixed
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function getIniExpression ( $i ) {

        if(!isset($this->_iniExpression[$i]))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Ini expression number %s does not exist.', 0, $i);

        return $this->_iniExpression[$i];
    }

    /**
     * Check if loop has an initializing expression.
     *
     * @access  public
     * @return  bool
     */
    public function hasIniExpression ( ) {

        return $this->_iniExpression != array();
    }

    /**
     * Get conditional expression.
     *
     * @access  public
     * @return  Hoa_Tokenizer_Token_Operation
     */
    public function getCondExpression ( ) {

        return $this->_condExpression;
    }

    /**
     * Check if loop has a conditional expression.
     *
     * @access  public
     * @return  bool
     */
    public function hasCondExpression ( ) {

        return $this->_condExpression !== null;
    }

    /**
     * Get all next expressions.
     *
     * @access  public
     * @return  array
     */
    public function getNextExpressions ( ) {

        return $this->_nextExpressions;
    }

    /**
     * Get a next expression.
     *
     * @access  public
     * @param   int     $i    Expression to get.
     * @return  mixed
     * @throw   Hoa_Tokenizer_Token_Util_Exception
     */
    public function getNextExpression ( $i ) {

        if(!isset($this->_nextExpression[$i]))
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Next expression number %s does not exist.', 0, $i);

        return $this->_nextExpression[$i];
    }

    /**
     * Check if a loop has a next expression.
     *
     * @access  public
     * @return  bool
     */
    public function hasNextExpression ( ) {

        return $this->_nextExpression != array();
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        $fi   = false;
        $fn   = false;
        $ini  = array();
        $next = array();

        foreach($this->getIniExpressions()  as $i => $iniExpression) {

            if($fi === true)
                $ini[] = array(array(
                             0 => Hoa_Tokenizer::_COMMA,
                             1 => ',',
                             2 => -1
                         ));
            else
                $fi = true;

            $ini[]  = $iniExpression->tokenize();
        }

        foreach($this->getNextExpressions() as $i => $nextExpression) {

            if($fn === true)
                $next[] = array(array(
                              0 => Hoa_Tokenizer::_COMMA,
                              1 => ',',
                              2 => -1
                          ));
            else
                $fn = true;

            $next[] = $nextExpression->tokenize();
        }

        $cond = true === $this->hasCondExpression()
                    ? $this->getCondExpression()
                    : array();

        return array_merge(
            array(array(
                0 => Hoa_Tokenizer::_FOR,
                1 => 'for',
                2 => -1
            )),
            array(array(
                0 => Hoa_Tokenizer::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $ini,
            array(array(
                0 => Hoa_Tokenizer::_SEMI_COLON,
                1 => ';',
                2 => -1
            )),
            $cond,
            array(array(
                0 => Hoa_Tokenizer::_SEMI_COLON,
                1 => ';',
                2 => -1
            )),
            $next
            array(array(
                0 => Hoa_Tokenizer::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            )),
            parent::tokenize()
        );
    }
}
