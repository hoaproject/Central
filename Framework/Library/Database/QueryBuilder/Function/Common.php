<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @subpackage  Hoa_Database_QueryBuilder_Function_Common
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
 * Class Hoa_Database_QueryBuilder_Function_Common.
 *
 * Common functions.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
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
