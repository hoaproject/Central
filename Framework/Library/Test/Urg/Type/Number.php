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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Number
 *
 */

/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Type_Undefined
 */
import('Test.Urg.Type.Undefined');

/**
 * Class Hoa_Test_Urg_Type_Number.
 *
 * Represent a number.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Number
 */

abstract class Hoa_Test_Urg_Type_Number extends Hoa_Test_Urg_Type_Undefined {

    /**
     * Whether bound is open.
     *
     * @const bool
     */
    const BOUND_OPEN  = true;

    /**
     * Whether bound is close.
     *
     * @const bool
     */
    const BOUND_CLOSE = false;

    /**
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name            = 'number';

    /**
     * Lower bound value.
     *
     * @var Hoa_Test_Urg_Type_Number mixed
     */
    protected $_lowerBoundValue = 0;

    /**
     * Upper bound value.
     *
     * @var Hoa_Test_Urg_Type_Number mixed
     */
    protected $_upperBoundValue = 0;

    /**
     * Random value.
     *
     * @var Hoa_Test_Urg_Type_Number mixed
     */
    protected $_value           = null;



    /**
     * Set the random value.
     *
     * @access  protected
     * @param   mixed      $value    The random value.
     * @return  mixed
     */
    protected function setValue ( $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Set the lower bound value.
     *
     * @access  protected
     * @param   mixed      $value    The upper bound value.
     * @return  mixed
     */
    protected function setLowerBoundValue ( $value ) {

        $old                    = $this->_lowerBoundValue;
        $this->_lowerBoundValue = $value;

        return $old;
    }

    /**
     * Set the upper bound value.
     *
     * @access  protected
     * @param   mixed      $value    The upper bound value.
     * @return  mixed
     */
    protected function setUpperBoundValue ( $value ) {

        $old                    = $this->_upperBoundValue;
        $this->_upperBoundValue = $value;

        return $old;
    }

    /**
     * Get the lower bound value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getLowerBoundValue ( ) {

        return $this->_lowerBoundValue;
    }

    /**
     * Get the upper bound value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getUpperBoundValue ( ) {

        return $this->_upperBoundValue;
    }
}
