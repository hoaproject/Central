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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_BoundFloat
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Type_Interface_Type
 */
import('Test.Urg.Type.Interface.Type');

/**
 * Hoa_Test_Urg_Type_Float
 */
import('Test.Urg.Type.Float');

/**
 * Class Hoa_Test_Urg_Type_BoundFloat.
 *
 * Represent a bound float.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_BoundFloat
 */

class Hoa_Test_Urg_Type_BoundFloat extends    Hoa_Test_Urg_Type_Integer
                                   implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Upper bound statement (given by parent::BOUND_* constants).
     *
     * @var Hoa_Test_Urg_Type_BoundFloat bool
     */
    protected $_upperBoundStatement = parent::BOUND_CLOSE;

    /**
     * Lower bound statement (given by parent::BOUND_* constants).
     *
     * @var Hoa_Test_Urg_Type_BoundFloat bool
     */
    protected $_lowerBoundStatement = parent::BOUND_CLOSE;



    /**
     * Build a bound float.
     *
     * @access  public
     * @param   int     $lowerValue        Lower bound value.
     * @param   int     $upperValue        Upper bound value.
     * @param   bool    $upperStatement    Given by constant parent::BOUND_*.
     * @param   bool    $lowerStatement    Given by constant parent::BOUND_*.
     * @return  void
     */
    public function __construct ( $lowerValue, $upperValue,
                                  $upperStatement = parent::BOUND_CLOSE,
                                  $lowerStatement = parent::BOUND_CLOSE ) {

        if($lower > $upper) {

            $this->setLowerBoundValue($upper);
            $this->setUpperBoundValue($lower);
        }
        else {

            $this->setLowerBoundValue($lower);
            $this->setUpperBoundValue($upper);
        }

        $this->setUpperBoundStatement($upperStatement);
        $this->setLowerBoundStatement($lowerStatement);
        $this->randomize();

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   float   $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        $lower = $this->getLowerBoundValue();
        $upper = $this->getUpperBoundValue();

        if(parent::BOUND_CLOSE == $this->getLowerBoundStatement()) {

            if($q < $lower)
                return false;
        }
        else
            if($q <= $lower)
                return false;

        if(parent::BOUND_CLOSE == $this->getUpperBoundStatement()) {

            if($q > $upper)
                return false;
        }
        else
            if($q >= $upper)
                return false;

        return true;
    }

    /**
     * Set upper bound statement.
     *
     * @access  protected
     * @param   bool       $upperStatement    Given by constant parent::BOUND_*.
     * @return  bool
     */
    protected function setUpperBoundStatement ( $upperStatement ) {

        $old                        = $this->_upperBoundStatement;
        $this->_upperBoundStatement = $upperStatement;

        return $old;
    }

    /**
     * Set lower bound statement.
     *
     * @access  protected
     * @param   bool       $lowerStatement    Given by constant parent::BOUND_*.
     * @return  bool
     */
    protected function setLowerBoundStatement ( $lowerStatement ) {

        $old                        = $this->_lowerBoundStatement;
        $this->_lowerBoundStatement = $lowerStatement;

        return $old;
    }

    /**
     * Get upper bound statement.
     *
     * @access  protected
     * @return  bool
     */
    protected function getUpperBoundStatement ( ) {

        return $this->_upperBoundStatement;
    }

    /**
     * Get lower bound statement.
     *
     * @access  protected
     * @return  bool
     */
    protected function getLowerBoundStatement ( ) {

        return $this->_lowerBoundStatement;
    }
}
