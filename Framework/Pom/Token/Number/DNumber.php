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
 * @subpackage  Hoa_Pom_Token_Number_DNumber
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
 * Hoa_Pom_Token_Number
 */
import('Pom.Token.Number');

/**
 * Class Hoa_Pom_Token_Number_DNumber.
 *
 * Represent a dnumber : float, double or real number, i.e. ℝ.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Number_DNumber
 */

class Hoa_Pom_Token_Number_DNumber extends Hoa_Pom_Token_Number {

    /**
     * Value.
     *
     * @var Hoa_Pom_Token_Number_DNumber float
     */
    protected $_value = 0.0;



    /**
     * Set number.
     *
     * @access  public
     * @param   mixed   $number    Number. Could be a string or a number.
     * @return  float
     */
    public function setNumber ( $number ) {

        $number  = (float) $number;
        $pattern = Hoa_Pom_Token_Number::D_LNUM . '|' .
                   Hoa_Pom_Token_Number::D_DNUM . '|' .
                   Hoa_Pom_Token_Number::D_EXPONENT_DNUM;

        if(0 === preg_match('#' . $pattern . '#', (string) $number))
            throw new Hoa_Pom_Token_Util_Exception(
                'DNumber %d is not well-formed.', 0, $number);

        return parent::setNumber($number);
    }

    /**
     * Get number.
     *
     * @access  public
     * @return  float
     */
    public function getNumber ( ) {

        return (float) $this->_value;
    }
}
