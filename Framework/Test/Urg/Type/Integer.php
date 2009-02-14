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
 * @subpackage  Hoa_Test_Urg_Type_Integer
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
 * Hoa_Test_Urg_Type_Interface_Randomizable
 */
import('Test.Urg.Type.Interface.Randomizable');

/**
 * Hoa_Test_Urg_Type_Number
 */
import('Test.Urg.Type.Number');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Class Hoa_Test_Urg_Type_Integer.
 *
 * Represent an integer.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Integer
 */

class Hoa_Test_Urg_Type_Integer extends    Hoa_Test_Urg_Type_Number
                                implements Hoa_Test_Urg_Type_Interface_Randomizable {

    /**
     * Zero.
     *
     * @const int
     */
    const ZERO         = 0;

    /**
     * The closest value of zero.
     *
     * @const int
     */
    const CLOSEST_ZERO = 1;

    /**
     * The “infinity”, i.e. the greatest value.
     *
     * @const int
     */
    const INFINITY     = PHP_INT_MAX;



    /**
     * Build a integer.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->setUpperBoundValue(self::getPositiveInfinity());
        $this->setLowerBoundValue(self::getNegativeInfinity());
        $this->randomize();

        return;
    }

    /**
     * Get the zero.
     *
     * @access  public
     * @return  int
     */
    public static function getZero ( ) {

        return self::ZERO;
    }

    /**
     * Get the positive closest value of zero.
     *
     * @access  public
     * @return  int
     */
    public static function getPositiveClosestValue ( ) {

        return self::CLOSEST_ZERO;
    }

    /**
     * Get the negative closest value of zero.
     *
     * @access  public
     * @return  int
     */
    public static function getNegativeClosestValue ( ) {

        return -self::CLOSEST_ZERO;
    }

    /**
     * Get the positive infinity.
     *
     * @access  public
     * @return  int
     */
    public static function getPositiveInfinity ( ) {

        return self::INFINITY;
    }

    /**
     * Get the negative infinity.
     *
     * @access  public
     * @return  int
     */
    public static function getNegativeInfinity ( ) {

        return ~self::INFINITY;
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     */
    public function randomize ( ) {

        $upper  = $this->getUpperBoundValue();
        $lower  = $this->getLowerBoundValue();
        $random = Hoa_Test_Urg::Ud($upper, $lower);

        if($this instanceof Hoa_Test_Urg_Type_Interface_Predicable)
            while(false === $this->predicate($random)) // Increment test number ?
                $random = Hoa_Test_Urg::Ud($upper, $lower);

        $this->setValue($random);

        return;
    }
}
