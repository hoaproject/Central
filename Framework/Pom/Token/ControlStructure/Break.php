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
 * @subpackage  Hoa_Pom_Token_ControlStructure_Break
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
 * Hoa_Pom_Token_ControlStructure
 */
import('Pom.Token.ControlStructure');

/**
 * Class Hoa_Pom_Token_ControlStructure_Break.
 *
 * Represent a break.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Break
 */

class Hoa_Pom_Token_ControlStructure_Break extends Hoa_Pom_Token_ControlStructure {

    /**
     * Level up.
     *
     * @var mixed object
     */
    protected $_level = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Number_LNumber  $level    Level up.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_Number_LNumber $level ) {

        $this->setLevel($level);

        return;
    }

    /**
     * Set level.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Number_LNumber  $level    Level up.
     * @return  Hoa_Pom_Token_Number_LNumber
     */
    public function setLevel ( Hoa_Pom_Token_Number_LNumber $level ) {

        $old          = $this->_level;
        $this->_level = $level;

        return $old;
    }

    /**
     * Set auto-level.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Number_LNumber
     */
    public function setAutoLevel ( ) {

        $old          = $this->_level;
        $this->_level = null;

        return $old;
    }

    /**
     * Get level.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Number_LNumber
     */
    public function getLevel ( ) {

        return $this->_level;
    }

    /**
     * Check if continue has a level.
     *
     * @access  public
     * @return  bool
     */
    public function hasLevel ( ) {

        return $this->_level !== null;
    }

    /**
     * Transform token to “tokenize array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_BREAK,
                1 => 'break',
                2 => -1
            )),
            (true === $this->hasLevel()
                 ? $this->getLevel()->tokenize()
                 : array()
            )
        );
    }
}
