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
 * @subpackage  Hoa_Database_Criterion_Abstract
 *
 */

/**
 * Hoa_Database_Criterion_Exception
 */
import('Database.Criterion.Exception');

/**
 * Class Hoa_Database_Criterion_Abstract.
 *
 * Abstract class to manage criterion.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Criterion_Abstract
 */

abstract class Hoa_Database_Criterion_Abstract {

    /**
     * The where and the having clauses understand search conditions. We got one
     * array for these two clauses, the index is given by these following
     * constants.
     *
     * @const string
     */
    const MODE_WHERE     = 'where';
    const MODE_HAVING    = 'having';

    /**
     * Criteria are separated by boolean-keyword, here : “and” and “or”.
     *
     * @const string
     */
    const BOOLEAN_AND    = 'AND';
    const BOOLEAN_OR     = 'OR';

    /**
     * Criteria can have subgroups for boolean expressions.
     *
     * @const string
     */
    const SUBGROUP_OPEN  = '(';
    const SUBGROUP_CLOSE = ')';

    /**
     * The field instance.
     *
     * @var Hoa_Database_Model_Field object
     */
    protected $_field           = null;

    /**
     * The search conditions array.
     *
     * @var Hoa_Database_Criterion_Abstract array
     */
    protected $searchCondition  =  array(
        self::MODE_WHERE        => array(),
        self::MODE_HAVING       => array()
    );

    /**
     * The order by clauses array.
     *
     * @var Hoa_Database_Criterion_Abstract string
     */
    protected $orderBy          = null;

    /**
     * The group by clauses array.
     *
     * @var Hoa_Database_Criterion_Abstract string
     */
    protected $groupBy          = null;

    /**
     * The current mode for search conditions array (please, see the MODE_*
     * constants).
     *
     * @var Hoa_Database_Criterion_Abstract string
     */
    protected $mode             = self::MODE_WHERE;

    /**
     * Whether the boolean-keyword criteria seperator is set.
     *
     * @var Hoa_Database_Criterion_Abstract bool
     */
    protected $booleanTermIsSet = true;

    /**
     * Number of sub-groups in criteria, i.e. number of sub-conditions.
     *
     * @var Hoa_Database_Criterion_Abstract int
     */
    protected $subGroupNumber   = 0;

    /**
     * Prepared query values.
     *
     * @var Hoa_Database_Criterion_Abstract array
     */
    protected $preparedValue   = array();



    /**
     * Set the field instance.
     *
     * @access  public
     * @param   Hoa_Database_Model_Field  $field    The field instance.
     * @return  void
     */
    public function __construct ( Hoa_Database_Model_Field $field ) {

        $this->setField($field);
    }

    /**
     * Set the field instance.
     *
     * @access  protected
     * @param   Hoa_Database_Model_Field  $field    The field instance.
     * @return  Hoa_Database_Model_Field
     */
    protected function setField ( Hoa_Database_Model_Field $field ) {

        $old          = $this->_field;
        $this->_field = $field;

        return $old;
    }

    /**
     * Get the field instance.
     *
     * @access  protected
     * @return  Hoa_Database_Model_Field
     */
    protected function getField ( ) {

        return $this->_field;
    }

    /**
     * Add a linked table to this referenced table (master table).
     *
     * @access  protected
     * @param   Hoa_Database_Model_Table  $table    Table to add.
     * @return  void
     */
    protected function addLinkedTable ( Hoa_Database_Model_Table $table ) {

        $this->getField()->getTable()->addLinkedTable($table);
    }

    /**
     * Check if the boolean term is set.
     *
     * @access  public
     * @return  bool
     */
    public function isBoolTermSet ( ) {

        return $this->booleanTermIsSet;
    }

    /**
     * Set the flag self::$booleanTermIsSet to false.
     *
     * @access  protected
     * @return  void
     */
    protected function unsetBooleanTerm ( ) {

        $this->booleanTermIsSet = false;
    }

    /**
     * Set the flag self::$booleanTermIsSet to true.
     *
     * @access  protected
     * @return  void
     */
    protected function setBooleanTerm ( ) {

        $this->booleanTermIsSet = true;
    }

    /**
     * Add a search condition.
     *
     * @access  protected
     * @param   string     $condition    The search condition to add.
     * @param   mixed      $value        If the query should be prepared, this
     *                                   is its value, else the value is set in
     *                                   the $condition (normally) ; e.g. in a
     *                                   sub-query condition.
     * @return  void
     */
    protected function addSearchCondition ( $condition, $value = null ) {

        if(false === $this->isBoolTermSet())
            $this->searchCondition[$this->getMode()][] = self::BOOLEAN_AND;

        $this->unsetBooleanTerm();

        if(null === $condition)
            return;

        if(null === $value) {

            $this->searchCondition[$this->getMode()][] = $this->myVprintf($condition);
            return;
        }

        if(is_string($value))
            $value = array($value);

        $format = array();

        foreach($value as $k => $v) {

            $key = md5($this->getField()->getIdentifier() . $condition . $v);
            $this->preparedValue[$key] = $v;
            $format[]                  = $key;
        }

        $this->searchCondition[$this->getMode()][] = $this->myVprintf($condition, $format);

        return;
    }

    /**
     * Add an order to the field (ASC or DESC clauses).
     *
     * @access  protected
     * @param   string     $order    The order.
     * @return  void
     */
    protected function addOrder ( $order ) {

        $this->orderBy = $this->myVprintf($order);

        return;
    }

    /**
     * Add a group to the GROUP BY fields list.
     *
     * @access  protected
     * @return  void
     */
    protected function addGroup ( $group ) {

        $this->groupBy = $this->myVprintf($group);

        return;
    }

    /**
     * Get the current mode. Please, see the MODE_* constants.
     *
     * @access  public
     * @return  string
     */
    public function getMode ( ) {

        return $this->mode;
    }

    /**
     * Get the number of sub-groups, i.e. the number of sub-conditions.
     *
     * @access  public
     * @return  int
     */
    public function getSubGroupNumber ( ) {

        return $this->subGroupNumber;
    }

    /**
     * Get the WHERE search conditions.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function getWhere ( ) {

        if($this->getSubGroupNumber() > 0)
            throw new Hoa_Database_Criterion_Exception(
                '%d subgroup(s) is/are not closed.',
                0, $this->getSubGroupNumber());

        return $this->searchCondition[self::MODE_WHERE];
    }

    /**
     * Get the HAVING search conditions.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function getHaving ( ) {

        if($this->getSubGroupNumber() > 0)
            throw new Hoa_Database_Criterion_Exception(
                '%d subgroup(s) is/are not closed.',
                0, $this->getSubGroupNumber());

        return $this->searchCondition[self::MODE_HAVING];
    }

    /**
     * Get the order of this field.
     *
     * @access  public
     * @return  string
     */
    public function getOrderBy ( ) {

        return $this->orderBy;
    }

    /**
     * Get the group of this field.
     *
     * @access  public
     * @return  string
     */
    public function getGroupBy ( ) {

        return $this->groupBy;
    }

    /**
     * Get the prepared values.
     *
     * @access  public
     * @return  string
     */
    public function getPreparedValue ( ) {

        return $this->preparedValue;
    }

    /**
     * Transform data with printf format to a valid string.
     *
     * @access  protected
     * @param   string     $data        Data to transform.
     * @param   array      $prepared    Name of a prepared query (:name).
     * @return  string
     */
    protected function myVprintf ( $data, Array $prepared = array() ) {

        if(empty($prepared))
            return sprintf($data, $this->getField()->__toString());

        $prepared = array_map(
            create_function(
                '$name',
                'return \':\' . $name;'
            ),
            $prepared
        );

        array_unshift($prepared, $this->getField()->__toString());

        return vsprintf($data, $prepared);
    }

    /**
     * Reset the object.
     *
     * @access  public
     * @return  void
     */
    public function _reset ( ) {

        $this->searchCondition  = array(
            self::MODE_WHERE    => array(),
            self::MODE_HAVING   => array()
        );
        $this->orderBy          = null;
        $this->groupBy          = null;
        $this->mode             = self::MODE_WHERE;
        $this->booleanTermIsSet = true;
        $this->subGroupNumber   = 0;
        $this->preparedValue    = array();
    }
}
