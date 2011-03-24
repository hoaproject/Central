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
 * @subpackage  Hoa_Database_QueryBuilder_Field
 *
 */

/**
 * Hoa_Database_QueryBuilder_Exception
 */
import('Database.QueryBuilder.Exception');

/**
 * Hoa_Database_QueryBuilder_Function_Common
 */
import('Database.QueryBuilder.Function.Common');

/**
 * Hoa_Database_QueryBuilder_Criterion_SearchCondition
 */
import('Database.QueryBuilder.Criterion.SearchCondition');

/**
 * Hoa_Database_QueryBuilder_Criterion_OrderBy
 */
import('Database.QueryBuilder.Criterion.OrderBy');

/**
 * Hoa_Database_QueryBuilder_Criterion_GroupBy
 */
import('Database.QueryBuilder.Criterion.GroupBy');

/**
 * Class Hoa_Database_QueryBuilder_Field.
 *
 * Aliases to the criterion query builder.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Field
 */

abstract class Hoa_Database_QueryBuilder_Field extends Hoa_Database_QueryBuilder_Function_Common {

    /**
     * Get the WHERE clause in a string.
     *
     * @access  public
     * @return  string
     */
    public function getWhereString ( ) {

        $sc = new Hoa_Database_QueryBuilder_Criterion_SearchCondition(
                  $this->getCriterion()->getWhere()
              );

        return $sc->getQuery();
    }

    /**
     * Get the HAVING clause in a string.
     *
     * @access  public
     * @return  string
     */
    public function getHavingString ( ) {

        $sc = new Hoa_Database_QueryBuilder_Criterion_SearchCondition(
                  $this->getCriterion()->getHaving()
              );

        return $sc->getQuery();
    }

    /**
     * Get the ORDER BY clause in a string.
     *
     * @access  public
     * @return  string
     */
    public function getOrderByString ( ) {

        $order = new Hoa_Database_QueryBuilder_Criterion_OrderBy(
                     $this->getCriterion()->getOrderBy()
                 );

        return $order->getQuery();
    }

    /**
     * Get the GROUP BY clause in a string.
     *
     * @access  public
     * @return  string
     */
    public function getGroupByString ( ) {

        $order = new Hoa_Database_QueryBuilder_Criterion_GroupBy(
                     $this->getCriterion()->getGroupBy()
                 );

        return $order->getQuery();
    }
}
