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
