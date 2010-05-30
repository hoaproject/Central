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
 * @subpackage  Hoa_Database_QueryBuilder_Criterion_GroupBy
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Database_QueryBuilder_Criterion_Exception
 */
import('Database.QueryBuilder.Criterion.Exception');

/**
 * Hoa_Database_QueryBuilder_Interface
 */
import('Database.QueryBuilder.Interface');

/**
 * Class Hoa_Database_QueryBuilder_Criterion_GroupBy.
 *
 * Build the GROUP BY clauses.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Criterion_GroupBy
 */

class Hoa_Database_QueryBuilder_Criterion_GroupBy implements Hoa_Database_QueryBuilder_Interface {

    /**
     * The built query.
     *
     * @var Hoa_Database_QueryBuilder_Criterion_GroupBy string
     */
    protected $query = null;



    /**
     * Call the self::builtQuery() method.
     *
     * @access  public
     * @param   string  $groupBy    The group by.
     * @return  void
     */
    public function __construct ( $groupBy ) {

        $this->builtQuery($groupBy);
    }

    /**
     * Built the query.
     *
     * @access  protected
     * @param   string     $groupBy    The group by.
     * @return  string
     */
    protected function builtQuery ( $groupBy ) {

        $this->query = $groupBy;

        return $this->getQuery();
    }

    /**
     * Return the built query.
     *
     * @access  public
     * @return  string
     */
    public function getQuery ( ) {

        return $this->query;
    }

    /**
     * Call the self::getQuery() method.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getQuery();
    }
}
