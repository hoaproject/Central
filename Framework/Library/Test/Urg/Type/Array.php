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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Test_Urg_Type_Array
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
 * Hoa_Test_Urg_Type_Undefined
 */
import('Test.Urg.Type.Undefined');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Class Hoa_Test_Urg_Type_Array.
 *
 * Represent an array.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Array
 */

class Hoa_Test_Urg_Type_Array extends Hoa_Test_Urg_Type_Undefined {

    /**
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name   = 'array';

    /**
     * Random value.
     *
     * @var Hoa_Test_Urg_Type_Array int
     */
    protected $_value  = null;

    /**
     * All types that constitute the array.
     *
     * @var Hoa_Test_Urg_Type_Array array
     */
    protected $_types  = array();

    /**
     * Size of the array.
     *
     * @var Hoa_Test_Urg_Type_Array mixed
     */
    protected $_length = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   array   $types     Types.
     * @param   mixed   $length    Length.
     * @return  void
     */
    public function __construct ( Array $types, $length = 20 ) {

        $this->setTypes($types);
        $this->setLength($length);

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   bool    $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        $types = $this->getTypes();

        for($i = 0, $max = count($types); $i < $max; $i++) {

            $count = 0;

            foreach($q as $key => $value)
                if(    true === $types[$i][0]->predicate($key)
                    && true === $types[$i][1]->predicate($value))
                    $count++;

            if(0 == $count)
                return false;
        }

        return true;
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
        $cTypes = count($this->_types) - 1;
        $length = $this->getLength();

        if($length instanceof Hoa_Test_Urg_Type_Integer)
            $length = $length->getValue();

        $length = abs($length);

        do {

            $random = array();

            for($i = 0; $i < $length;) {

                $e = Hoa_Test_Urg::Ud(0, $cTypes);

                $this->_types[$e][0]->randomize();
                $this->_types[$e][1]->randomize();

                if(isset($random[$this->_types[$e][0]->getValue()])) {

                    $maxtry--;
                    continue;
                }

                $random[$this->_types[$e][0]->getValue()] =
                        $this->_types[$e][1]->getValue();
                $i++;
            }

        } while(false === $this->predicate($random) && $maxtry-- > 0);

        if($maxtry == -1)
            throw new Hoa_Test_urg_Type_Exception_Maxtry(
                'All tries failed (%d tries).',
                0, Hoa_Test::getInstance()->getParameter('test.maxtry'));

        $this->setValue($random);

        return;
    }

    /**
     * Set the random value.
     *
     * @access  protected
     * @param   array      $value    The random value.
     * @return  array
     */
    protected function setValue ( $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Set types of array.
     *
     * @access  protected
     * @param   array      $types    Types.
     * @return  array
     */
    protected function setTypes ( Array $types ) {

        $old          = $this->_types;
        $this->_types = $types;

        return $old;
    }

    /**
     * Set length of array.
     *
     * @access  protected
     * @param   mixed      $length    Length.
     * @return  mixed
     */
    protected function setLength ( $length ) {

        $old           = $this->_length;
        $this->_length = $length;

        return $old;
    }

    /**
     * Get the random value.
     *
     * @access  public
     * @return  array
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get types of array.
     *
     * @access  public
     * @return  array
     */
    public function getTypes ( ) {

        return $this->_types;
    }

    /**
     * Get length of array.
     *
     * @access  public
     * @return  mixed
     */
    public function getLength ( ) {

        return $this->_length;
    }
}
