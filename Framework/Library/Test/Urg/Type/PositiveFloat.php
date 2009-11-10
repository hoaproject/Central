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
 * @subpackage  Hoa_Test_Urg_Type_PositiveFloat
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
 * Hoa_Test_Urg_Type_BoundFloat
 */
import('Test.Urg.Type.BoundFloat');

/**
 * Class Hoa_Test_Urg_Type_PositiveFloat.
 *
 * Represent a positive float.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_PositiveFloat
 */

class Hoa_Test_Urg_Type_PositiveFloat extends    Hoa_Test_Urg_Type_BoundFloat
                                      implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Build a positive float.
     *
     * @access  public
     * @param   int     $upperValue        The upper bound value.
     * @param   bool    $upperStatement    The upper bound statement (given by
     *                                     parent::BOUND_* constants).
     * @return  void
     */
    public function __construct ( $upperValue     = null,
                                  $upperStatement = parent::BOUND_CLOSE ) {

        if(null === $upperValue)
            $upperValue = $this->getPositiveInfinity();

        parent::__construct(0, $upperValue, parent::BOUND_OPEN, $upperStatement);

        return;
    }
}
