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
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Function_Common
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Database_QueryBuilder_Function_Exception
 */
import('Database.QueryBuilder.Function.Exception');

/**
 * Hoa_Database_QueryBuilder_Function_Abstract
 */
import('Database.QueryBuilder.Function.Abstract');

/**
 * Class Hoa_Database_QueryBuilder_Function_Common.
 *
 * Common functions.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Function_Common
 */

class Hoa_Database_QueryBuilder_Function_Common extends Hoa_Database_QueryBuilder_Function_Abstract {

    /**
     * Add a COUNT function.
     *
     * @access  public
     * @return  parent::rtrn()
     */
    public function count ( ) {

        parent::addFunction('COUNT(%s)');

        return parent::rtrn();
    }

    /**
     * Add a MIN function.
     *
     * @access  public
     * @return  parent::rtrn()
     */
    public function min ( ) {

        parent::addFunction('MIN(%s)');

        return parent::rtrn();
    }

    /**
     * Add a MAX function.
     *
     * @access  public
     * @return  parent::rtrn()
     */
    public function max ( ) {

        parent::addFunction('MAX(%s)');

        return parent::rtrn();
    }

    /**
     * Add a MINUS function.
     *
     * @access  public
     * @param   Hoa_Database_Model_Field  $operand    If given, the operation
     *                                                will be $field - $operand,
     *                                                else -$field.
     * @return  parent::rtrn()
     */
    public function minus ( Hoa_Database_Model_Field $operand = null ) {

        if(null === $operand)
            parent::addFunction('-%s');
        else
            parent::addFunction('(%s - ' . $operand . ')');

        return parent::rtrn();
    }
}
