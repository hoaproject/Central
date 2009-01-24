<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @package     Hoa_StdClass
 * @subpackage  Hoa_StdClass_Util
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_StdClass_Exception
 */
import('StdClass.Exception');

/**
 * Class Hoa_StdClass_Util.
 *
 * Convert array into a StdClass (of SPL, not of Hoa_StdClass !).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_StdClass
 */

class Hoa_StdClass_Util {

    /**
     * Convert an array to a StdClass object.
     * It's an alias to convertAtoO private method.
     *
     * @access public
     * @param  $array  array    Array to convert.
     * @return void
     */
    public static function convertArrayToObject( Array $array ) {

        self::convertAtoO($array);
    }

    /**
     * Convert an arary to a StdClass object.
     *
     * @access  public
     * @param   array         array     Array.
     * @param   previousNode  object    Previous node. Do not be set by user.
     *                                  Use for recursive calls.
     * @return  object
     */
    private static function convertAtoO ( Array $array, $previousNode = null ) {

        if(null === $previousNode)
            $previousNode = new StdClass;

        foreach($array as $variable => $value) {

            $currentNode = $previousNode;

            if(is_array($value)) {

                $currentNode->$variable = new StdClass();
                self::convertAtoO($value, $currentNode->$variable);
            }
            else
                $currentNode->$variable = $value;
        }

        return $currentNode;
    }
}
