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
 * @subpackage  Hoa_Pom_Token_String_Boolean
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
 * Hoa_Pom_Token_Util_Interface_Scalar
 */
import('Pom.Token.Util.Interface.Scalar');

/**
 * Hoa_Pom_Token_Util_Interface_Type
 */
import('Pom.Token.Util.Interface.Type');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_String
 */
import('Pom.Token.String');

/**
 * Class Hoa_Pom_Token_String_Boolean.
 *
 * Represent a boolean.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_String_Boolean
 */

class Hoa_Pom_Token_String_Boolean extends    Hoa_Pom_Token_String
                                   implements Hoa_Pom_Token_Util_Interface_Scalar,
                                              Hoa_Pom_Token_Util_Interface_Type {

    /**
     * Set string.
     *
     * @access  public
     * @param   mixed   $string    String, could be a boolean or a string.
     * @return  string
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function setString ( $string ) {

        if(true      === $string)
            $string    = 'true';
        elseif(false === $string)
            $string    = 'false';

        $string = strtolower($string);

        if(   $string != 'true'
           && $string != 'false')
            throw new Hoa_Pom_Token_Util_Exception(
                'Boolean cannot be different of true or false. Given %s.', 0,
                $string);

        return parent::setString($string);
    }
}
