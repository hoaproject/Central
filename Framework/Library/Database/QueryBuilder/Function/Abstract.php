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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Function_Abstract
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
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
