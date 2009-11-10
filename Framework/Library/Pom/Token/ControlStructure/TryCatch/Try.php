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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_ControlStructure_TryCatch_Try
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
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Instruction_Block
 */
import('Pom.Token.Instruction.Block');

/**
 * Class Hoa_Pom_Token_ControlStructure_TryCatch_Try.
 *
 * Represent a try block.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_TryCatch_Try
 */

class       Hoa_Pom_Token_ControlStructure_TryCatch_Try
    extends Hoa_Pom_Token_Instruction_Block {

    /**
     * Collections of catch.
     *
     * @var Hoa_Pom_Token_TryCatch_Try array
     */
    protected $_catch  = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $instructions    One or more instructions.
     * @return  void
     */
    public function __construct ( $expression ) {

        parent::setBracesMode(self::FORCE_BRACES);
        
        return parent::__contruct($instructions);
    }

    /**
     * Add many catch blocks.
     *
     * @access  public
     * @param   array   $catchs    Many catch blocks to add.
     * @return  array
     */
    public function addCatchs ( Array $catchs = array() ) {

        foreach($catchs as $i => $catch)
            $this->addCatch($catch);

        return $this->_catch;
    }

    /**
     * Add a catch block.
     *
     * @access  public
     * @param   Hoa_Pom_Token_ControlStructure_TryCatch_Catch  $catch    Catch block.
     * @return  Hoa_Pom_Token_ControlStructure_TryCatch_Catch
     */
    public function addCatch ( Hoa_Pom_Token_ControlStructure_TryCatch_Catch $catch ) {

        return $this->_catch[] = $catch;
    }

    /**
     * Remove a catch block.
     *
     * @access  public
     * @param   int     $i    Catch block number.
     * @return  array
     */
    public function removeCatch ( $i ) {

        unset($this->_catch[$i]);

        return $this->_catch;
    }

    /**
     * Remove all catch blocks.
     *
     * @access  public
     * @return  array
     */
    public function removeCatchs ( ) {

        $old = $this->_catch[$i];

        foreach($this->_catch as $i => $catch)
            unset($this->_catch[$i]);

        return $old;
    }

    /**
     * Get all catch blocks.
     *
     * @access  public
     * @return  array
     */
    public function getCatchs ( ) {

        return $this->_catch;
    }

    /**
     * Get a catch block.
     *
     * @access  public
     * @param   int     $i    Catch block number.
     * @return  Hoa_Pom_Token_ControlStructure_TryCatch_Catch
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function getCatch ( $i ) {

        if(!isset($this->_catch[$i]))
            throw new Hoa_Pom_Token_Util_Exception(
                'Catch block number %d does not exist.', 0, $i);

        return $this->_catch[$i];
    }

    /**
     * Check if a catch block exists.
     *
     * @access  public
     * @return  bool
     */
    public function hasCatch ( ) {

        return $this->_catch != array();
    }
}
