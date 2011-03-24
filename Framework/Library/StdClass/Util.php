<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @category    Framework
 * @package     Hoa_StdClass
 * @subpackage  Hoa_StdClass_Util
 *
 */

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
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     New BSD License
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
            $previousNode = new StdClass();

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
