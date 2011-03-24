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
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Function_Abstract
 *
 */

/**
 * Hoa_Database_QueryBuilder_Function_Exception
 */
import('Database.QueryBuilder.Function.Exception');

/**
 * Hoa_Database_QueryBuilder_Function_Abstract
 */
import('Database.QueryBuilder.Function.Abstract');

/**
 * Class Hoa_Database_QueryBuilder_Function_Abstract.
 *
 * Abstract class for functions.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Function_Abstract
 */

abstract class Hoa_Database_QueryBuilder_Function_Abstract {

    /**
     * The function stack, i.e. the first function is inner function, and the
     * lastest function is the outter function.
     * And all functions must contain the %s format to be sprintf.
     * E.g. :
     *     array(
     *         0 => 'FUNC(%s)',
     *         1 => 'TION(%s)'
     *     )
     * will produce TION(FUNC()).
     * The field identified is added automatically in the top of the array.
     *
     * @var Hoa_Database_QueryBuilder_Function_Abstract array
     */
    protected $functionStack = array();



    /**
     * Add a function on a field, i.e. in the function stack.
     *
     * @access  public
     * @param   string  $function    The function to add.
     * @return  array
     * @throw   Hoa_Database_QueryBuilder_Function_Exception
     */
    protected function addFunction ( $function ) {

        if(false === $a = strpos($function, '%s'))
            throw new Hoa_Database_QueryBuilder_Function_Exception(
                'A function must contain the %s format to be printed.', 0, '%s');

        $this->functionStack[] = $function;

        return $this->getFunctionStack();
    }

    /**
     * Erase the function stack, i.e. set to an empty array.
     *
     * @access  public
     * @return  array
     */
    public function eraseFunctionStack ( ) {

        $old                 = $this->functionStack;
        $this->functionStack = array();

        return $old;
    }

    /**
     * Get the function stack.
     *
     * @access  public
     * @return  array
     */
    public function getFunctionStack ( ) {

        return $this->functionStack;
    }

    /**
     * Build the field string, i.e. the field identifier with functions.
     *
     * @access  protected
     * @return  string
     */
    protected function getString ( ) {

        $stack = $this->getFunctionStack();

        if(empty($stack))
            return $this->getIdentifier();

        array_unshift($stack, $this->getIdentifier());
        $out = null;

        foreach($stack as $foo => $function)
            $out = sprintf($function, $out);

        return $out;
    }

    /**
     * All methods that produce functions should return this method. Better for
     * the package maintenance.
     *
     * @access  protected
     * @return  Hoa_Database_QueryBuilder_Function_Abstract
     */
    protected function rtrn ( ) {

        return $this;
    }

    /**
     * Transform the object into a string. Call the self::getString() method.
     *
     * @access  public
     * @return  string
     */
    final public function __toString ( ) {

        return $this->getString();
    }
}
