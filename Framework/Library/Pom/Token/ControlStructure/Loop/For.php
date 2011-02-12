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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_ControlStructure_Loop_For
 *
 */

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Instruction_Block
 */
import('Pom.Token.Instruction.Block');

/**
 * Class Hoa_Pom_Token_ControlStructure_Loop_For.
 *
 * Represent a for loop.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Loop_For
 */

class       Hoa_Pom_Token_ControlStructure_Loop_For
    extends Hoa_Pom_Token_Instruction_Block {

    /**
     * Initializing expression.
     *
     * @var Hoa_Pom_Token_ControlStructure_Loop_For array
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
     * @var Hoa_Pom_Token_ControlStructure_Loop_For array
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

            case 'Hoa_Pom_Token_Call':
            case 'Hoa_Pom_Token_Cast':
            case 'Hoa_Pom_Token_Clone':
            case 'Hoa_Pom_Token_Comment':
            case 'Hoa_Pom_Token_New':
            case 'Hoa_Pom_Token_Number':
            case 'Hoa_Pom_Token_Operation':
            case 'Hoa_Pom_Token_String':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
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
     * @param   Hoa_Pom_Token_Operation  $condExpression    Expression.
     * @return  Hoa_Pom_Token_Operation
     */
    public function setCondExpression ( Hoa_Pom_Token_Operation $condExpression ) {

        $old                   = $this->_condExpression;
        $this->_condExpression = $condExpression;

        return $old;
    }

    /**
     * Remove conditional expression.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Operation
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

            case 'Hoa_Pom_Token_Call':
            case 'Hoa_Pom_Token_Cast':
            case 'Hoa_Pom_Token_Clone':
            case 'Hoa_Pom_Token_Comment':
            case 'Hoa_Pom_Token_New':
            case 'Hoa_Pom_Token_Number':
            case 'Hoa_Pom_Token_Operation':
            case 'Hoa_Pom_Token_String':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'An next expression cannot be constitued by a class that ' .
                    'is an instance of %s.', 1, get_class($nextExpression));
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
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function getIniExpression ( $i ) {

        if(!isset($this->_iniExpression[$i]))
            throw new Hoa_Pom_Token_Util_Exception(
                'Ini expression number %s does not exist.', 2, $i);

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
     * @return  Hoa_Pom_Token_Operation
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
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function getNextExpression ( $i ) {

        if(!isset($this->_nextExpression[$i]))
            throw new Hoa_Pom_Token_Util_Exception(
                'Next expression number %s does not exist.', 3, $i);

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
}
