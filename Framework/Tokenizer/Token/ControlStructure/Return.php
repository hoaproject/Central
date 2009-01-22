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
 * @subpackage  Hoa_Pom_Token_ControlStructure_Return
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
 * Class Hoa_Pom_Token_ControlStructure_Return.
 *
 * Represent a return.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_ControlStructure_Return
 */

class Hoa_Pom_Token_ControlStructure_Return extends Hoa_Pom_Token_ControlStructure {

    /**
     * Value of return.
     *
     * @var mixed object
     */
    protected $_value = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $value    Value.
     * @return  void
     */
    public function __construct ( $value ) {

        $this->setValue($value);

        return;
    }

    /**
     * Set value.
     *
     * @access  public
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function setValue ( $value ) {

        switch(get_class($value)) {

            case 'Hoa_Pom_Token_Array':
            case 'Hoa_Pom_Token_Call':
            case 'Hoa_Pom_Token_Cast':
            case 'Hoa_Pom_Token_Clone':
            case 'Hoa_Pom_Token_Comment':
            case 'Hoa_Pom_Token_ControlStructure_Ternary':
            case 'Hoa_Pom_Token_New':
            case 'Hoa_Pom_Token_Number':
            case 'Hoa_Pom_Token_Operation':
            case 'Hoa_Pom_Token_String':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'A return cannot accept a class like %s.',
                    0, get_class($value));
        }

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
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
     * Transform token to “tokenize array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        return array_merge(
            array(array(
                0 => Hoa_Pom::_RETURN,
                1 => 'return',
                2 => -1
            )),
            $this->getValue()->tokenize()
        );
    }
}
