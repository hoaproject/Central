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
 * Hoa_Test_Urg_Type_Exception_Maxtry
 */
import('Test.Urg.Type.Exception.Maxtry');

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
 * Class Hoa_Test_Urg_Type_Integer.
 *
 * Represent an integer.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Integer
 */

class Hoa_Test_Urg_Type_Integer extends Hoa_Test_Urg_Type_Number {

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
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name = 'integer';



    /**
     * Build a integer.
     *
     * @access  public
     * @param   int     $value    Default value (usefull for manual unit test).
     * @return  void
     */
    public function __construct ( $value = null ) {

        if(null !== $value && true === $this->predicate($value)) {

            parent::setArguments($value);
            $this->_value = $value;
        }

        $this->setLowerBoundValue(self::getNegativeInfinity());
        $this->setUpperBoundValue(self::getPositiveInfinity());

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   int     $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        return is_int($q);
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     * @throws  Hoa_Test_Urg_Type_Exception_Maxtry
     */
    public function randomize ( ) {

        if(null !== $this->_value)
            return $this->_value;

        $maxtry = Hoa_Test::getInstance()->getParameter('test.maxtry');
        $lower  = $this->getLowerBoundValue();
        $upper  = $this->getUpperBoundValue();

        do {

            $random = Hoa_Test_Urg::Ud($lower, $upper);

        } while(false === $this->predicate($random) && $maxtry-- > 0);

        if($maxtry == -1)
            throw new Hoa_Test_urg_Type_Exception_Maxtry(
                'All tries failed (%d tries) from %s.', 0,
                array(Hoa_Test::getInstance()->getParameter('test.maxtry'), get_class($this)));

        $this->setValue($random);

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
}
