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
 * @subpackage  Hoa_Test_Urg_Type_Undefined
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
 * Hoa_Test_Urg_Interface_Type
 */
import('Test.Urg.Type.Interface.Type');

/**
 * Hoa_Test_Urg_Type_Integer
 */
import('Test.Urg.Type.Integer');

/**
 * Hoa_Test_Urg_Type_Float
 */
import('Test.Urg.Type.Float');

/**
 * Hoa_Test_Urg_Type_String
 */
import('Test.Urg.Type.String');

/**
 * Hoa_Test_Urg_Type_Boolean
 */
import('Test.Urg.Type.Boolean');

/**
 * Hoa_Test_Urg_Type_Array
 */
import('Test.Urg.Type.Array');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Class Hoa_Test_Urg_Type_Undefined.
 *
 * Represent the absolute super-type.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Abdallah BEN OTHMAN <ben_othman@live.fr>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Undefined
 */

class Hoa_Test_Urg_Type_Undefined implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name            = 'undefined';

    /**
     * Random value.
     *
     * @var Hoa_Test_Urg_Type_Undefined null
     */
    protected $_value           = null;

    /**
     * Type's arguments.
     *
     * @var Hoa_Test_Urg_Type_Undefined
     */
    protected $_arguments       = array();

    /**
     * The real object generated (integer, float etc.)
     *
     * @var Hoa_Test_Urg_Type_Interface_Type object
     */
    private $_undefinedObject   = null;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $choice = Hoa_Test_Urg::Ud(0, 4);

        switch($choice) {

            case 0:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Boolean();
              break;
            
            case 1:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Integer();
              break;

            case 2:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Float();
              break;

            case 3:
                throw new Hoa_Test_Urg_Type_Exception(
                    'TODO', 42);
              break;

            case 4:
                $result = array();
                $choice = Hoa_Test_Urg::Ud(0, 1);
                
                if(0 === $choice)
                    $key = new Hoa_Test_Urg_Type_Integer();

                if(1 === $choice)
                    $key = new Hoa_Test_Urg_Type_Integer();

                $value = new Hoa_Test_Urg_Type_Undefined($this->_depth + 1, $this->_maxDepth);

                $result[0][0] = $key;
                $result[0][1] = $value;

                $this->_undefinedObject = new Hoa_Test_Urg_Type_Array(
                    $result,
                    Hoa_Test_Urg::Ud(0, 16)
                );
              break;
        }

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   mixed   $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        return true;
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     */
    public function randomize ( ) {

        $this->_undefinedObject->randomize();

        return;
    }

    /**
     * Clear the current value.
     *
     * @access  public
     * @return  Hoa_Test_Urg_Type_Undefined
     */
    public function clear ( ) {

        $this->_value = null;

        return $this;
    }

    /**
     * Get the random value.
     *
     * @access  public
     * @return  mixed
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get the name of type.
     *
     * @access  public
     * @return  mixed
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Set type's arguments.
     *
     * @access  protected
     * @param   ...
     * @return  void
     */
    protected function setArguments ( ) {

        $this->_arguments = func_get_args();

        return;
    }

    /**
     * Get type's argument.
     *
     * @access  public
     * @return  array
     */
    public function getArguments ( ) {

        return $this->_arguments;
    }
}
