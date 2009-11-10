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
 * @subpackage  Hoa_Test_Praspel_Clause_Throws
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
 * Hoa_Test_Praspel_Clause
 */
import('Test.Praspel.Clause');

/**
 * Class Hoa_Test_Praspel_Clause_Throws.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Clause_Throws
 */

class Hoa_Test_Praspel_Clause_Throws implements Hoa_Test_Praspel_Clause {

    /**
     * List of exception names.
     *
     * @var Hoa_Test_Praspel_Clause_Throws array
     */
    protected $_list = array();



    /**
     * Declare a list of exception.
     *
     * @access  public
     * @param   array   $list    List of exception.
     * @return  array
     */
    public function lists ( Array $list ) {

        $old         = $this->_list;
        $this->_list = $list;

        return $old;
    }

    /**
     * Check if an exception is declared in the list.
     *
     * @access  public
     * @param   string    $exception    Exception name.
     * @return  bool
     */
    public function exceptionExists ( $exception ) {

        return false !== array_search($exception, $this->getList());
    }

    /**
     * Get list of exceptions.
     *
     * @access  public
     * @return  array
     */
    public function getList ( ) {

        return $this->_list;
    }
}
