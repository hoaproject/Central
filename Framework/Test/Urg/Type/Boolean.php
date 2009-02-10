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
 * @subpackage  Hoa_Test_Urg_Type_Boolean
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
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Class Hoa_Test_Urg_Type_Boolean.
 *
 * Represent a boolean.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Boolean
 */

class Hoa_Test_Urg_Type_Boolean implements Hoa_Test_Urg_Interface_Randomizable {

    /**
     * Random value.
     *
     * @var Hoa_Test_Urg_Type_Boolean int
     */
    protected $_value = null;



    /**
     * Build a boolean.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->randomize();

        return;
    }

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
     * Get the random value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function getValue ( ) {

        return $this->_value;
    }

    /**
     * Choose a random value.
     *
     * @access  protected
     * @return  void
     */
    protected function randomize ( ) {

        $random = Hoa_Test_Urg::Ud(0, 1);

        if($this instanceof Hoa_Test_Urg_Type_Interface_Predicable)
            while(false === $this->predicate($random))
                $random = Hoa_Test_Urg::Ud(0, 1);

        $this->setValue($random);

        return;
    }
}
