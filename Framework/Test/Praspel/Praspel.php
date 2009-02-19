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
 * @subpackage  Hoa_Test_Praspel
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
 * Hoa_Test_Praspel_Clause_Requires
 */
import('Test.Praspel.Clause.Requires');

/**
 * Hoa_Test_Praspel_Clause_Ensures
 */
import('Test.Praspel.Clause.Ensures');

/**
 * Hoa_Test_Praspel_Type
 */
import('Test.Praspel.Type');

/**
 * Hoa_Test_Praspel_Call
 */
import('Test.Praspel.Call');

/**
 * Class Hoa_Test_Praspel.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel
 */

class Hoa_Test_Praspel {

    /**
     * Collection of clauses.
     *
     * @var Hoa_Test_Praspel array
     */
    protected $_clauses             = array();

    /**
     * The call object.
     *
     * @var Hoa_Test_Praspel_Call object
     */
    protected $_call                = null;

    /**
     * Path to user types.
     *
     * @var Hoa_Test_Praspel string
     */
    protected static $_userTypePath = null;



    /**
     * Add a clause.
     *
     * @access  public
     * @param   string  $name    Clause name.
     * @return  Hoa_Test_Praspel_Clause
     * @throws  Hoa_Test_Praspel_Exception
     */
    public function clause ( $name ) {

        if(true === $this->clauseExists($name))
            return $this->_clauses[$name];

        $clause = null;

        switch($name) {

            case 'requires':
                $clause = new Hoa_Test_Praspel_Clause_Requires();
              break;

            case 'ensures':
                $clause = new Hoa_Test_Praspel_Clause_Ensures();
              break;

            default:
                throw new Hoa_Test_Praspel_Exception(
                    'Unknown clause %s.', 0, $name);
        }

        return $this->_clauses[$name] = $clause;
    }

    /**
     * Create a type.
     *
     * @access  public
     * @param   string  $name    Type name.
     * @param   ...     ...      Type arguments.
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function type ( $name ) {

        $arguments = func_get_args();
        array_shift($arguments);
        $type      = new Hoa_Test_Praspel_Type($name, $arguments);

        return $type->getType();
    }

    /**
     * Call a method.
     *
     * @access  public
     * @param   object  $object    Object where method is.
     * @param   string  $method    Method name.
     * @return  Hoa_Test_Praspel_Call
     */
    public function call ( $object, $method ) {

        $old         = $this->_call;
        $this->_call = new Hoa_Test_Praspel_Call($this, $object, $method);

        return $old;
    }

    /**
     * Check if a clause already exists or not.
     *
     * @access  public
     * @param   string     $name    Clause name.
     * @return  bool
     */
    public function clauseExists ( $name ) {

        return isset($this->_clauses[$name]);
    }

    /**
     * Get all clauses.
     *
     * @access  public
     * @param   string     $name    Clause name.
     * @return  Hoa_Test_Praspel_Clause
     * @throw   Hoa_Test_Praspel_Exception
     */
    public function getClause ( $name ) {

        if(false === $this->clauseExists($name))
            throw new Hoa_Test_Praspel_Exception(
                'Clause %s is not defined.', 1, $name);

        return $this->_clauses[$name];
    }

    /**
     * Get the call.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Call
     */
    public function getCall ( ) {

        return $this->_call;
    }

    /**
     * Set the path to user types.
     *
     * @access  public
     * @param   string  $path    Path to user types.
     * @return  string
     * @throws  Hoa_Test_Praspel_Exception
     */
    public static function setUserTypePath ( $path ) {

        if(!is_dir($path))
            throw new Hoa_Test_Praspel_Exception(
                'Path %s does not exists, it could not be used as path to user ' .
                'types.', 0, $path);

        $old                 = self::$_userTypePath;
        self::$_userTypePath = $path;

        return $old;
    }

    /**
     * Get the path to user types.
     *
     * @access  public
     * @return  string
     */
    public static function getUserTypePath ( ) {

        return self::$_userTypePath;
    }
}
