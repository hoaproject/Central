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
 * @subpackage  Hoa_Database_Criterion_Predicate
 *
 */

/**
 * Hoa_Database_Criterion_Abstract
 */
import('Database.Criterion.Abstract');

/**
 * Class Hoa_Database_Criterion_Predicate.
 *
 * This class proposes all predicates.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Database
 * @subpackage  Hoa_Database_Criterion_Predicate
 */

class Hoa_Database_Criterion_Predicate extends Hoa_Database_Criterion_Abstract {

    /**
     * Switch mode.
     */

    /**
     * Switch the mode of search conditions to the WHERE clause, i.e. apply all
     * new search condition to the WHERE clause (by default).
     *
     * @access  public
     * @return  void
     */
    public function where ( ) {

        $this->mode = parent::MODE_WHERE;
    }

    /**
     * Switch the mode of search conditions to the HAVING clause, i.e. apply all
     * new search condition to the HAVING clause.
     *
     * @access  public
     * @return  void
     */
    public function having ( ) {

        $this->mode = parent::MODE_HAVING;
    }

    /**
     * Boolean expression.
     */

    /**
     * Add an AND clause if and only if a previous clause was set, except for the OR
     * clause, i.e. if add an OR clause, and after, an AND clause, the AND clause will
     * not be added.
     * AND clause is added automatically between each other clauses.
     *
     * @access  public
     * @return  void
     */
    public function _and ( ) {

        $this->addSearchCondition(null);
        $this->setBooleanTerm();
    }

    /**
     * Add an OR clause. Please see the self::_and() method, this method has the
     * same comportement.
     * The OR clause is not added automatically.
     *
     * @access  public
     * @return  void
     */
    public function _or ( ) {

        $this->setBooleanTerm();
        $this->addSearchCondition(parent::BOOLEAN_OR);
        $this->setBooleanTerm();
    }

    /**
     * Start a logic subgroup, i.e. : A && (B || C), the parenthesis represent a
     * subgroup.
     *
     * @access  public
     * @return  void
     */
    public function subGroup ( ) {

        $this->setBooleanTerm();
        $this->subGroupNumber++;
        $this->addSearchCondition(parent::SUBGROUP_OPEN);
        $this->setBooleanTerm();
    }

    /**
     * Stop a logic subgroup. Please, see the self::subGroup() method to know
     * more.
     *
     * @access  public
     * @return  void
     */
    public function endSubGroup ( ) {

        $this->setBooleanTerm();
        $this->addSearchCondition(parent::SUBGROUP_CLOSE);
        $this->subGroupNumber--;
    }

    /**
     * Comparison.
     */

    /**
     * Add an equal clause, i.e. the symbol “=”.
     *
     * @access  public
     * @param   mixed   $operand    It could be a integer, a string, a float, a
     *                              char etc., or a Hoa_Database_Model_Table
     *                              instance (to make a sub-query).
     * @return  void
     */
    public function equal ( $operand ) {

        if($operand instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s =');
            $this->subGroup();
            $operand->select();
            $this->addSearchCondition($operand->getQuery());
            $this->endSubGroup();
        }
        else {

            if($operand instanceof Hoa_Database_Model_Field)
                $this->addLinkedTable($operand->getTable());

            $this->addSearchCondition('%1$s = %2$s', (string) $operand);
        }
    }

    /**
     * Add a different clause, i.e. the symbol “<>”.
     *
     * @access  public
     * @param   mixed   $operand    It could be a integer, a string, a float, a
     *                              char etc., or a Hoa_Database_Model_Table
     *                              instance (to make a sub-query).
     * @return  void
     */
    public function different ( $operand ) {

        if($operand instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s <>');
            $this->subGroup();
            $operand->select();
            $this->addSearchCondition($operand->getQuery());
            $this->endSubGroup();
        }
        else {

            if($operand instanceof Hoa_Database_Model_Field)
                $this->addLinkedTable($operand->getTable());

            $this->addSearchCondition('%1$s <> %2$s', (string) $operand);
        }
    }

    /**
     * Add a lesserThan clause, i.e. the symbol “<”.
     *
     * @access  public
     * @param   mixed   $operand    It could be a integer, a string, a float, a
     *                              char etc., or a Hoa_Database_Model_Table
     *                              instance (to make a sub-query).
     * @return  void
     */
    public function lesserThan ( $operand ) {

        if($operand instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s <');
            $this->subGroup();
            $operand->select();
            $this->addSearchCondition($operand->getQuery());
            $this->endSubGroup();
        }
        else {

            if($operand instanceof Hoa_Database_Model_Field)
                $this->addLinkedTable($operand->getTable());

            $this->addSearchCondition('%1$s < %2$s', (string) $operand);
        }
    }

    /**
     * Add a greaterThan clause, i.e. the symbol “>”.
     *
     * @access  public
     * @param   mixed   $operand    It could be a integer, a string, a float, a
     *                              char etc., or a Hoa_Database_Model_Table
     *                              instance (to make a sub-query).
     * @return  void
     */
    public function greaterThan ( $operand ) {

        if($operand instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s >');
            $this->subGroup();
            $operand->select();
            $this->addSearchCondition($operand->getQuery());
            $this->endSubGroup();
        }
        else {

            if($operand instanceof Hoa_Database_Model_Field)
                $this->addLinkedTable($operand->getTable());

            $this->addSearchCondition('%1$s > %2$s', (string) $operand);
        }
    }

    /**
     * Add a lesserOrEqual clause, i.e. the symbol “<=”.
     *
     * @access  public
     * @param   mixed   $operand    It could be a integer, a string, a float, a
     *                              char etc., or a Hoa_Database_Model_Table
     *                              instance (to make a sub-query).
     * @return  void
     */
    public function lesserOrEqual ( $operand ) {

        if($operand instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s <=');
            $this->subGroup();
            $operand->select();
            $this->addSearchCondition($operand->getQuery());
            $this->endSubGroup();
        }
        else {

            if($operand instanceof Hoa_Database_Model_Field)
                $this->addLinkedTable($operand->getTable());

            $this->addSearchCondition('%1$s <= %2$s', (string) $operand);
        }
    }

    /**
     * Add a greaterrOrEqual clause, i.e. the symbol “>=”.
     *
     * @access  public
     * @param   mixed   $operand    It could be a integer, a string, a float, a
     *                              char etc., or a Hoa_Database_Model_Table
     *                              instance (to make a sub-query).
     * @return  void
     */
    public function greaterOrEqual ( $operand ) {

        if($operand instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s >=');
            $this->subGroup();
            $operand->select();
            $this->addSearchCondition($operand->getQuery());
            $this->endSubGroup();
        }
        else {

            if($operand instanceof Hoa_Database_Model_Field)
                $this->addLinkedTable($operand->getTable());

            $this->addSearchCondition('%1$s >= %2$s', (string) $operand);
        }
    }

    /**
     * Interval.
     */

    /**
     * Add a BETWEEN clause.
     *
     * @access  public
     * @param   mixed   $a    The first operand. If given a
     *                        Hoa_Database_Model_Field instance, it will be casted
     *                        to a string. If given a string, it is ok. If given
     *                        something other, it will be casted to a string.
     * @param   mixed   $b    The second operand. See the $a argument.
     * @return  void
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function between ( $a, $b ) {

        if(is_object($a) && !($a instanceof Hoa_Database_Model_Field))
            throw new Hoa_Database_Criterion_Exception(
                'If given an object to the between operation, it must be a ' .
                'Hoa_Database_Model_Field instance ; given %s.',
                0, get_class($a));

        if(is_object($b) && !($b instanceof Hoa_Database_Model_Field))
            throw new Hoa_Database_Criterion_Exception(
                'If given an object to the between operation, it must be a ' .
                'Hoa_Database_Model_Field instance ; given %s.',
                0, get_class($b));

        $a = (string) $a;
        $b = (string) $b;

        if(strlen($a) <= 0)
            throw new Hoa_Database_Criterion_Exception(
                'If given a string for the first argument of the between ' .
                'operation, its length must be greater than 0.', 1);
           
        if(strlen($b) <= 0)
            throw new Hoa_Database_Criterion_Exception(
                'If given a string for the second argument of the between ' .
                'operation, its length must be greater than 0.', 1);

        $this->addSearchCondition('%1$s BETWEEN %2$s AND %3$s', array($a, $b));
    }

    /**
     * Add a NOT BETWEEN clause.
     *
     * @access  public
     * @param   mixed   $a    Please, see the first argument of the
     *                        self::between() method.
     * @param   mixed   $b    Please, see the second argument of the
     *                        self::between() method.
     * @return  void
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function notBetween ( $a, $b ) {

        if(is_object($a) && !($a instanceof Hoa_Database_Model_Field))
            throw new Hoa_Database_Criterion_Exception(
                'If given an object to the between operation, it must be a ' .
                'Hoa_Database_Model_Field instance ; given %s.',
                0, get_class($a));

        if(is_object($b) && !($b instanceof Hoa_Database_Model_Field))
            throw new Hoa_Database_Criterion_Exception(
                'If given an object to the between operation, it must be a ' .
                'Hoa_Database_Model_Field instance ; given %s.',
                0, get_class($b));

        $a = (string) $a;
        $b = (string) $b;

        if(strlen($a) <= 0)
            throw new Hoa_Database_Criterion_Exception(
                'If given a string for the first argument of the between ' .
                'operation, its length must be greater than 0.', 1);
           
        if(strlen($b) <= 0)
            throw new Hoa_Database_Criterion_Exception(
                'If given a string for the second argument of the between ' .
                'operation, its length must be greater than 0.', 1);

        $this->addSearchCondition('%1$s NOT BETWEEN %2$s AND %3$s', array($a, $b));
    }

    /**
     * Search.
     */

    /**
     * Add a LIKE clause.
     *
     * @access  public
     * @param   string  $atom      The search pattern.
     * @param   string  $escape    The joker char for the search.
     * @return  void
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function like ( $atom, $escape = null ) {

        if(!is_string($atom) || strlen($atom) <= 0)
            throw new Hoa_Database_Criterion_Exception(
                'The like atom must be a string, and its length must be greater ' .
                'than 0 ; given %s.', 0, gettype($atom));

        if(null !== $escape)
            if(!is_string($escape) || strlen($escape) <= 0)
                throw new Hoa_Database_Criterion_Exception(
                    'The like atom must be a string, and its length must be ' .
                    'greater than 0 ; given %s.', 0, gettype($escape));

        $condition = '%1$s LIKE %2$s';

        if(null !== $escape)
            $condition .= ' ESCAPE ' . str_replace('%', '%%', $escape);

        $this->addSearchCondition($condition, $atom);
    }

    /**
     * Add a NOT LIKE clause.
     *
     * @access  public
     * @param   string  $atom      Please, see the first argument of the
     *                             self::like() method.
     * @param   string  $escape    Please, see the second argument of the
     *                             self::like() method.
     * @return  void
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function notLike ( $atom, $escape = null ) {

        if(!is_string($atom) && strlen($atom) > 0)
            throw new Hoa_Database_Criterion_Exception(
                'The like atom must be a string, and its length must be greater ' .
                'than 0 ; given %s.', 0, gettype($atom));

        if(null !== $escape)
            if(!is_string($escape) && strlen($escape) > 0)
                throw new Hoa_Database_Criterion_Exception(
                    'The like atom must be a string, and its length must be ' .
                    'greater than 0 ; given %s.', 0, gettype($escape));

        $condition = '%1$s NOT LIKE %2$s';

        if(null !== $escape)
            $condition .= ' ESCAPE ' . str_replace('%', '%%', $escape);

        $this->addSearchCondition($condition, $atom);
    }

    /**
     * Value.
     */

    /**
     * Add a IS NULL clause.
     *
     * @access  public
     * @return  void
     */
    public function isNull ( ) {

        $this->addSearchCondition('%1$s IS NULL');
    }

    /**
     * Add a IS NOT NULL clause.
     *
     * @access  public
     * @return  void
     */
    public function isNotNull ( ) {

        $this->addSearchCondition('%1$s IS NOT NULL');
    }

    /**
     * List.
     */

    /**
     * Add an IN clause.
     * If given 1 argument, it must be a table. If given n arguments, they must
     * be fields.
     *
     * @access  public
     * @param   mixed                     $a     Could be a
     *                                           Hoa_Database_Model_Table
     *                                           instance to make a sub-query,
     *                                           or a Hoa_Database_Model_Field
     *                                           to make a list of fields.
     * @param   Hoa_Database_Model_Field  ...    Other element of the list.
     * @return  void
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function in ( $a ) {

        $atoms = func_get_args();

        if($a instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s IN');
            $this->subGroup();
            $a->select();
            $this->addSearchCondition($a->getQuery());
            $this->endSubGroup();
            return;
        }

        foreach($atoms as $foo => $atom)
            if(!($atom instanceof Hoa_Database_Model_Field))
                throw new Hoa_Database_Criterion_Exception(
                    'The in method only takes arguments of Hoa_Database_Model_Field ' .
                    'objects, or one single Hoa_Database_Model_Table argument ; ' .
                    'given %s.', 0, gettype($atom));
            else
                $this->addLinkedTable($atom->getTable());

        $this->addSearchCondition('%1$s IN ' . implode(', ', $atoms));
    }

    /**
     * Add an IN clause.
     * If given 1 argument, it must be a table. If given n arguments, they must
     * be fields.
     *
     * @access  public
     * @param   mixed                     $a     Please, see the first argument
     *                                           of the self::in() method.
     * @param   Hoa_Database_Model_Field  ...    Please, see the first argument
     *                                           of the self::in() method.
     * @throw   Hoa_Database_Criterion_Exception
     */
    public function notIn ( $a ) {

        $atoms = func_get_args();

        if($a instanceof Hoa_Database_Model_Table) {

            $this->addSearchCondition('%1$s IN');
            $this->subGroup();
            $a->select();
            $this->addSearchCondition($a->getQuery());
            $this->endSubGroup();
            return;
        }

        foreach($atoms as $foo => $atom)
            if(!($atom instanceof Hoa_Database_Model_Field))
                throw new Hoa_Database_Criterion_Exception(
                    'The in method only takes arguments of Hoa_Database_Model_Field ' .
                    'objects, or one single Hoa_Database_Model_Table argument ; ' .
                    'given %s.', 0, gettype($atom));
            else
                $this->addLinkedTable($atom->getTable());

        $this->addSearchCondition('%1$s NOT IN ' . implode(', ', $atoms));
    }

    /**
     * Existence.
     */

    /**
     * Add an EXISTS clause.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table   $sub    The sub-query represented by
     *                                             a table. In fact, sub-query
     *                                             is only constitued by a
     *                                             SELECT (ALL|DISTINCT) query,
     *                                             so methods are called
     *                                             automatically on the object.
     * @return  void
     */
    public function exists ( Hoa_Database_Model_Table $sub ) {

        $this->addSearchCondition('%1$s EXISTS');
        $this->subGroup();
        $sub->select();
        $this->addSearchCondition($sub->getQuery());
        $this->endSubGroup();
    }

    /**
     * Add a NOT EXISTS clause.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $sub    Please, see the first argument
     *                                            of the self::exists() method.
     * @return  void
     */
    public function notExists ( Hoa_Database_Model_Table $sub ) {

        $this->addSearchCondition('%1$s NOT EXISTS');
        $this->subGroup();
        $sub->select();
        $this->addSearchCondition($sub->getQuery());
        $this->endSubGroup();
    }

    /**
     * Quantifier.
     */

    /**
     * Add an ALL clause.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $sub    The sub-query represented by
     *                                            a table. In fact, sub-query
     *                                            is only constitued by a
     *                                            SELECT (ALL|DISTINCT) query,
     *                                            so methods are called
     *                                            automatically on the object.
     * @return  void
     */
    public function all ( Hoa_Database_Model_Table $sub ) {

        $this->addSearchCondition('%1$s ALL');
        $this->subGroup();
        $sub->select();
        $this->addSearchCondition($sub->getQuery());
        $this->endSubGroup();
    }

    /**
     * Add an ANY clause.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $sub    The sub-query represented by
     *                                            a table. In fact, sub-query
     *                                            is only constitued by a
     *                                            SELECT (ALL|DISTINCT) query,
     *                                            so methods are called
     *                                            automatically on the object.
     * @return  void
     */
    public function any ( Hoa_Database_Model_Table $sub ) {

        $this->addSearchCondition('%1$s ANY');
        $this->subGroup();
        $sub->select();
        $this->addSearchCondition($sub->getQuery());
        $this->endSubGroup();
    }

    /**
     * Add a SOME clause.
     *
     * @access  public
     * @param   Hoa_Database_Model_Table  $sub    The sub-query represented by
     *                                            a table. In fact, sub-query
     *                                            is only constitued by a
     *                                            SELECT (ALL|DISTINCT) query,
     *                                            so methods are called
     *                                            automatically on the object.
     * @return  void
     */
    public function some ( Hoa_Database_Model_Table $sub ) {

        $this->addSearchCondition('%1$s SOME');
        $this->subGroup();
        $sub->select();
        $this->addSearchCondition($sub->getQuery());
        $this->endSubGroup();
    }

    /**
     * Order.
     */

    /**
     * Add a ASC clause for the order.
     *
     * @access  public
     * @return  void
     */
    public function asc ( ) {

        $this->addOrder('%1$s ASC');
    }

    /**
     * Add a DESC clause for the order.
     *
     * @access  public
     * @return  void
     */
    public function desc ( ) {

        $this->addOrder('%1$s DESC');
    }

    /**
     * Group.
     */

    /**
     * Add the current field to the GROUP BY clause list.
     *
     * @access  public
     * @return  void
     */
    public function groupBy ( ) {

        $this->addGroup('%1$s');
    }
}
