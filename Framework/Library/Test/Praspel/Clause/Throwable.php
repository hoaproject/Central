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
 * @subpackage  Hoa_Test_Praspel_Clause_Throwable
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel_Clause
 */
import('Test.Praspel.Clause') and load();

/**
 * Class Hoa_Test_Praspel_Clause_Throwable.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Clause_Throwable
 */

class Hoa_Test_Praspel_Clause_Throwable implements Hoa_Test_Praspel_Clause {

    /**
     * List of exception names.
     *
     * @var Hoa_Test_Praspel_Clause_Throwable array
     */
    protected $_list = array();

    /**
     * Make a conjunction between two exception name declarations.
     *
     * @var Hoa_Test_Praspel_Clause_Throwable object
     */
    public $_comma   = null;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_comma = $this;

        return;
    }

    /**
     * Add an exception name that could be thrown.
     *
     * @access  public
     * @param   string  $name    Exception name.
     * @return  Hoa_Test_Praspel_Clause_Throwable
     */
    public function couldThrow ( $name ) {

        if(false === $this->exceptionExists($name))
            $this->_list[] = $name;

        return $this;
    }

    /**
     * Check if an exception is declared in the list.
     *
     * @access  public
     * @param   string  $name    Exception name.
     * @return  bool
     */
    public function exceptionExists ( $name ) {

        return true === in_array($name, $this->getList());
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

    /**
     * Transform this object model into Praspel.
     *
     * @access  public
     * @return  string
     */
    public function __toPraspel ( ) {

        $gc  = get_class($this);
        $out = '@' . strtolower(substr($gc, strrpos($gc, '_') + 1));

        return $out . ' ' . implode(', ', $this->getList()) . ";\n";
    }

    /**
     * Transform this object model into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $gc  = get_class($this);
        $out = strtolower(substr($gc, strrpos($gc, '_') + 1));

        return '$praspel' . "\n" .
               '    ->clause(\'' . $out . '\')'  . "\n" .
               '    ->couldThrow(\'' .
               implode(
                   '\')' . "\n" . '    ->_comma' . "\n" . '    ->couldThrow(\'',
                   $this->getList()
               ) . '\')' . "\n;";
    }
}
