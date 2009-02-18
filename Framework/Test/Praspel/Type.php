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
 * @subpackage  Hoa_Test_Praspel_Type
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel
 */
import('Test.Praspel.~');

/**
 * Class Hoa_Test_Praspel_Type.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Type
 */

class Hoa_Test_Praspel_Type {

    /**
     * Type found.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type object
     */
    protected $_type = null;



    /**
     * Find and build the type.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @return  void
     * @throws  Hoa_Test_Praspel_Exception
     */
    public function __construct ( $name ) {

        $this->factory($name);

        return;
    }

    /**
     * Factory of types.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @return  void
     */
    protected function factory ( $name ) {

        $name  = ucfirst(strtolower($name));
        $class = 'Hoa_Test_Urg_Type_' . $name;

        import('Test.Urg.Type.' . $name);

        $this->_type = new $class();

        return;
    }

    /**
     * Get the found type.
     *
     * @access  public
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function getType ( ) {

        return $this->_type;
    }
}
