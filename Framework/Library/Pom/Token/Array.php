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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Array
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom_Token_Util_Interface_SuperScalar
 */
import('Pom.Token.Util.Interface.SuperScalar');

/**
 * Hoa_Pom_Token_Util_Interface_Type
 */
import('Pom.Token.Util.Interface.Type');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Array.
 *
 * Represent an array (aïe, not easy …).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Array
 */

class Hoa_Pom_Token_Array implements Hoa_Pom_Token_Util_Interface_SuperScalar,
                                     Hoa_Pom_Token_Util_Interface_Type,
                                     Hoa_Visitor_Element {

    /**
     * Represent a key of an array.
     *
     * @const int
     */
    const KEY   = 0;

    /**
     * Represent a value of an array.
     *
     * @const int
     */
    const VALUE = 1;

    /**
     * Set of key/value that constitute an array.
     *
     * @var Hoa_Pom_Token_Array array
     */
    protected $_array = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $elements    Could be an instance of an element or a
     *                               collection of elements.
     * @return  void
     */
    public function __construct ( $elements = array() ) {

        $this->addElements((array) $elements);

        return;
    }

    /**
     * Add many elements.
     *
     * @access  public
     * @param   array   $elements    Add many elements to the array.
     * @return  array
     */
    public function addElements ( Array $elements = array() ) {

        foreach($elements as $i => $element)
            if(is_array($element))
                $this->addElement($element[self::KEY], $element[self::VALUE]);
            else
                $this->addElement(null, $element);

        return $this->getArray();
    }

    /**
     * Add an element.
     *
     * @access  public
     * @param   mixed   $key      Key to add. Null to auto-increment.
     * @param   mixed   $value    Value to add.
     * @return  array
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addElement ( $key, $value ) {

        if(null !== $key)
            if(   !($key instanceof Hoa_Pom_Token_Call)
               && !($key instanceof Hoa_Pom_Token_Clone)
               && !($key instanceof Hoa_Pom_Token_New)
               && !($key instanceof Hoa_Pom_Token_Number)
               && !($key instanceof Hoa_Pom_Token_Operation)
               && !($key instanceof Hoa_Pom_Token_String)
               && !($key instanceof Hoa_Pom_Token_Variable))
                throw new Hoa_Pom_Token_Util_Exception(
                    'An array key cannot accept a class that ' .
                    'is an instance of %s.', 0, get_class($key));

        if(   !($value instanceof Hoa_Pom_Token_Array)
           && !($value instanceof Hoa_Pom_Token_Call)
           && !($value instanceof Hoa_Pom_Token_Clone)
           && !($value instanceof Hoa_Pom_Token_New)
           && !($value instanceof Hoa_Pom_Token_Number)
           && !($value instanceof Hoa_Pom_Token_Operation)
           && !($value instanceof Hoa_Pom_Token_String)
           && !($value instanceof Hoa_Pom_Token_Variable))
            throw new Hoa_Pom_Token_Util_Exception(
                'An array value cannot accept a class that ' .
                'is an instance of %s.', 1, get_class($value));

        return $this->_array[] = array(
            self::KEY   => $key,
            self::VALUE => $value
        );
    }

    /**
     * Get the complete array.
     *
     * @access  public
     * @return  array
     */
    public function getArray ( ) {

        return $this->_array;
    }

    /**
     * Empty this array.
     *
     * @access  public
     * @return  array
     */
    public function emptyMe ( ) {

        $old          = $this->_array;
        $this->_array = array();

        return $old;
    }

    /**
     * Check if this array is empty or not.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty ( ) {

        return $this->getArray() == array();
    }

    /**
     * Check if a data is an uniform super-scalar or not.
     *
     * @access  public
     * @return  bool
     */
    public function isUniformSuperScalar ( ) {

        $oldKey       = null;
        $currentKey   = null;
        $oldValue     = null;
        $currentValue = null;
        $handleKey    = false;
        $handleValue  = false;

        foreach($this->getArray() as $i => $entry) {

            $handleKey   = false;
            $handleValue = false;

            list($currentKey, $currentValue) = $entry;

            if($currentKey instanceof Hoa_Pom_Token_Util_Interface_SuperScalar)
                if(true === $currentKey->isUniformSuperScalar())
                    $handleKey = true;

            if($currentValue instanceof Hoa_Pom_Token_Util_Interface_SuperScalar)
                if(true === $currentValue->isUniformSuperScalar())
                    $handleValue = true;

            if(   false === $handleKey
               || false === $handleValue)
                continue;
            else
                return false;

            if(   !($currenyKey   instanceof Hoa_Pom_Token_Util_Interface_Scalar)
               || !($currentValue instanceof Hoa_Pom_Token_Util_Interface_Scalar))
                return false;

            $handleKey   = get_class($currentKey);
            $handleValue = get_class($currentValue);

            if(null === $oldKey) { // $oldValue is also null.

                $oldKey   = $handleKey;
                $oldValue = $handleuValue;
                continue;
            }

            if(   ($oldKey   != $handleKey)
               || ($oldValue != $handleValue))
               return false;

            $oldKey   = $handleKey;
            $oldValue = $handleValue;
        }

        return true;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
