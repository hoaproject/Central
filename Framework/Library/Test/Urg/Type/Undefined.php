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
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Undefined
 */

class Hoa_Test_Urg_Type_Undefined implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Number of the first choice
     *
     * @const int
     */
    const CHOICE_FIRST   = 1;

    /**
     * Number of the last choice
     *
     * @const int
     */
    const CHOICE_LAST    = 5;

    /**
     * Choice for Integer
     *
     * @const int
     */
    const CHOICE_INTEGER = 1;

    /**
     * Choice for Float
     *
     * @const int
     */
    const CHOICE_FLOAT   = 2;

    /**
     * Choice for Boolean
     *
     * @const int
     */
    const CHOICE_BOOLEAN = 3;

    /**
     * Choice for String
     *
     * @const int
     */
    const CHOICE_STRING  = 4;
    
    /**
     * Choice for Array
     *
     * @const int
     */
    const CHOICE_ARRAY   = 5;

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
     * The real object generated (Integer, Float, etc.)
     *
     * @var Hoa_Test_Urg_Type_Interface_Type object
     */
    protected $_undefinedObject = null;

    /**
     * Depth in an array of undefined
     *
     * @var Hoa_Test_Urg_Type_Undefined int
     */
    protected $_depth           = 0;

    /**
     * Depth max for an array of undefined
     *
     * @var Hoa_Test_Urg_Type_Undefined int
     */
    protected $_maxDepth        = 0;



    /**
     * Constructor.
     *
     * @access  public
     * @param   int  $depth       Depth.
     * @param   int  $maxDepth    Max depth.
     * @return  void
     */
    public function __construct ( $depth = 0, $maxDepth = 3 ) {

        $this->_maxDepth = $maxDepth;
        $this->_depth    = $depth;
        $maxBound        = self::CHOICE_LAST;

        if($this->_depth == $this->_maxDepth)
            $maxBound--;

        $choice          = Hoa_Test_Urg::Ud(self::CHOICE_FIRST, $maxBound);

        switch($choice) {
            
            case self::CHOICE_INTEGER:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Integer();
              break;

            case self::CHOICE_FLOAT:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Float();
              break;

            case self::CHOICE_BOOLEAN:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Boolean();
              break;

            //TODO revoir generation de string
            case self::CHOICE_STRING:
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Integer();
              break;

            case self::CHOICE_ARRAY:
                $types = $this->getRandomizeTypes();
                $length = Hoa_Test_Urg::Ud(0, 20);
                $this->_undefinedObject = new Hoa_Test_Urg_Type_Array($types, $length);
              break;

            default:
                $_undefinedObject = new Hoa_Test_Urg_Type_Integer();
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
     * Get a randomize array for the argument types of the constructor.
     *
     * @access private
     * @return array
     */
    private function getRandomizeTypes ( ) {

        $result = array();
        $choice = Hoa_Test_Urg::Ud(0, 1);
        
        if(0 === $choice)
            $key = new Hoa_Test_Urg_Type_Integer();

        if(1 === $choice)
            $key = new Hoa_Test_Urg_Type_Integer();

        $value = new Hoa_Test_Urg_Type_Undefined($this->_depth + 1, $this->_maxDepth);

        $result[0][0] = $key;
        $result[0][1] = $value;

        return $result;
    }
}
