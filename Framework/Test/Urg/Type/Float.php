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
 * Hoa_Test_Urg_Type_Interface_Randomizable
 */
import('Test.Urg.Type.Interface.Randomizable');

/**
 * Hoa_Test_Urg_Type_Number
 */
import('Test.Urg.Type.Number');

/**
 * Class Hoa_Test_Urg_Type_Float.
 *
 * Represent a float.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Float
 */

class Hoa_Test_Urg_Type_Float extends    Hoa_Test_Urg_Type_Number
                              implements Hoa_Test_Urg_Type_Interface_Randomizable {

    /**
     * Build a float.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        //$this->setUpperBoundValue( 1.8e308);
        //$this->setLowerBoundValue(-1.8e308);
        // to big at home …
        parent::__construct();

        return;
    }

    /**
     * Choose a random value.
     *
     * @access  protected
     * @return  void
     */
    protected function randomize ( ) {

        $upper  = $this->getUpperBoundValue();
        $lower  = $this->getLowerBoundValue();
        $delta  = $upper - $lower;
        $random = $lower + lcg_value() * $delta;

        if($this instanceof Hoa_Test_Urg_Type_Interface_Predicable)
            while(false === $this->predicate($random)) // Increment test number ?
                $random = $lower + lcg_value() * $delta;

        $this->setValue($random);

        return;
    }
}
