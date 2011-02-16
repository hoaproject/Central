<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Database_QueryBuilder_Criterion_SearchCondition
 *
 */

/**
 * Hoa_Database_QueryBuilder_Criterion_Exception
 */
import('Database.QueryBuilder.Criterion.Exception');

/**
 * Hoa_Database_QueryBuilder_Interface
 */
import('Database.QueryBuilder.Interface');

/**
 * Hoa_Database_Criterion_Abstract
 */
import('Database.Criterion.Abstract');

/**
 * Class Hoa_Database_QueryBuilder_Criterion_SearchCondition.
 *
 * Build a WHERE or HAVING clauses (search condition clauses).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_QueryBuilder_Criterion_SearchCondition
 */

class Hoa_Database_QueryBuilder_Criterion_SearchCondition implements Hoa_Database_QueryBuilder_Interface {

    /**
     * The built query.
     *
     * @var Hoa_Database_QueryBuilder_Criterion_SearchCondition
     */
    protected $query = null;



    /**
     * Call the self::builtQuery() method.
     *
     * @access  public
     * @param   array   $searchCondition    The search condition.
     * @return  void
     */
    public function __construct ( Array $searchCondition ) {

        $this->builtQuery($searchCondition);
    }

    /**
     * Built the query.
     *
     * @access  protected
     * @param   array      $searchCondition    The search condition.
     * @return  string
     */
    protected function builtQuery ( Array $searchCondition ) {

        $out = null;
        $i   = 0;
        $tab = 4;

        foreach($searchCondition as $key => $condition) {

            if($condition === Hoa_Database_Criterion_Abstract::SUBGROUP_CLOSE)
                $i--;

            $out .= str_replace(
                        "\n",
                        "\n" . str_repeat(' ', $tab * $i),
                        str_repeat(' ', $tab * $i) .
                        $condition
                    ) . "\n";

            if($condition === Hoa_Database_Criterion_Abstract::SUBGROUP_OPEN)
                $i++;
        }

        $this->query = $out;

        return $this->getQuery();
    }

    /**
     * Return the built.
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
