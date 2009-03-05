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
 * @subpackage  Hoa_Test_Urg_Type_Float
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
 * Hoa_Test_Urg_Type_Exception_Maxtry
 */
import('Test.Urg.Type.Exception.Maxtry');

/**
 * Hoa_Test_Urg_Type_Interface_Type
 */
import('Test.Urg.Type.Interface.Type');

/**
 * Hoa_Test_Urg_Type_Number
 */
import('Test.Urg.Type.Number');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Class Hoa_Test_Urg_Type_Float.
 *
 * Represent a float.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Float
 */

class Hoa_Test_Urg_Type_Float extends    Hoa_Test_Urg_Type_Number
                              implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Zero.
     *
     * @const float
     */
    const ZERO            = 0.0;

    /**
     * The closest value of zero on a 32 bits system.
     *
     * @const float
     */
    const CLOSEST_ZERO_32 = 1.4012985e-45;

    /**
     * The closest value of zero on a 64 bits system.
     *
     * @const float
     */
    const CLOSEST_ZERO_64 = 4.94065645841e-324;

    /**
     * The “infinity”, i.e. the greatest value on a 32 bits system.
     *
     * @const float
     */
    const INFINITY_32     = 3.4028235e38;

    /**
     * The “infinity”, i.e. the greatest value on a 64 bits system.
     *
     * @const float
     */
    const INFINITY_64     = 1.7976931348623157e308;



    /**
     * Build a float.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->setUpperBoundValue($this->getPositiveInfinity());
        $this->setLowerBoundValue($this->getNegativeInfinity());

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

        if(null == $q)
            $q = $this->getValue();

        return is_float($q);
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     * @throws  Hoa_Test_Urg_Type_Exception_Maxtry
     */
    public function randomize ( ) {

        $maxtry = Hoa_Test::getInstance()->getParameter('test.maxtry');
        $lower  = $this->getLowerBoundValue();
        $upper  = $this->getUpperBoundValue();

        do {

            $random = Hoa_Test_Urg::Uc($lower, $upper);

        } while(false === $this->predicate($random) && $maxtry-- > 0);

        if($maxtry == -1)
            throw new Hoa_Test_urg_Type_Exception_Maxtry(
                'All tries failed (%d tries).',
                0, Hoa_Test::getInstance()->getParameter('test.maxtry'));

        $this->setValue($random);

        return;
    }

    /**
     * Get the zero.
     *
     * @access  public
     * @return  float
     */
    public static function getZero ( ) {

        return self::ZERO;
    }

    /**
     * Get the positive closest value of zero.
     *
     * @access  public
     * @return  float
     */
    public static function getPositiveClosestValue ( ) {

        if(true === S_64_BITS)
            return self::CLOSEST_ZERO_64;

        return self::CLOSEST_ZERO_32;
    }

    /**
     * Get the negative closest value of zero.
     *
     * @access  public
     * @return  float
     */
    public static function getNegativeClosestValue ( ) {

        if(true === S_64_BITS)
            return -self::CLOSEST_ZERO_64;

        return -self::CLOSEST_ZERO_32;
    }

    /**
     * Get the positive infinity.
     *
     * @access  public
     * @return  float
     */
    public static function getPositiveInfinity ( ) {

        if(true === S_64_BITS)
            return self::INFINITY_64;

        return self::INFINITY_32;
    }

    /**
     * Get the negative infinity.
     *
     * @access  public
     * @return  float
     */
    public static function getNegativeInfinity ( ) {

        if(true === S_64_BITS)
            return -self::INFINITY_64;

        return -self::INFINITY_32;
    }
}
