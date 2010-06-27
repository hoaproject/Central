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
 * @subpackage  Hoa_Database_QueryBuilder_Field
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

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
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
