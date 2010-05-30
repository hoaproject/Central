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
 * @subpackage  Hoa_Test_Urg
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Urg_Exception
 */
import('Test.Urg.Exception');

/**
 * Hoa_Test_Urg_Type_Integer
 */
import('Test.Urg.Type.Integer');

/**
 * Hoa_Test_Urg_Type_Float
 */
import('Test.Urg.Type.Float');

/**
 * Class Hoa_Test_Urg.
 *
 * Some usefull uniform random generator methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Abdallah BEN OTHMAN <ben_othman@live.fr>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg
 */

class Hoa_Test_Urg {

    /**
     * Generate a discrete uniform distribution (based on a Mersenne Twister for
     * pseudo-random algorithm).
     *
     * @access  public
     * @param   int     $lower    Lower bound.
     * @param   int     $upper    Upper bound.
     * @return  int
     */
    public static function Ud ( $lower = null, $upper = null ) {

        if(null === $lower)
            $lower = Hoa_Test_Urg_Type_Integer::getNegativeInfinity();

        if(null === $upper)
            $upper = Hoa_Test_Urg_Type_Integer::getPositiveInfinity();

        if($upper !== Hoa_Test_Urg_Type_Integer::getPositiveInfinity())
            $upper++;

        return (int) self::Uc($lower, $upper);
    }

    /**
     * Generate a continuous uniform distribution (based on a combined linear
     * congruential generator).
     *
     * @access  public
     * @param   float   $lower    Lower bound.
     * @param   float   $upper    Upper bound.
     * @return  float
     */
    public static function Uc ( $lower = null, $upper = null ) {

        if(null === $lower)
            $lower = Hoa_Test_Urg_Type_Float::getNegativeInfinity();

        if(null === $upper)
            $upper = Hoa_Test_Urg_Type_Float::getNegativeInfinity();

        return $lower + lcg_value() * ($upper - $lower);
    }
}
