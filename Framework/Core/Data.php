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
 * @package     Hoa_Core
 * @subpackage  Hoa_Core_Data
 *
 */

/**
 * Class Hoa_Core_Data.
 *
 * Universel data structure.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Core
 * @subpackage  Hoa_Core_Data
 */

class Hoa_Core_Data implements ArrayAccess {

    /**
     * Data as intuitive structure.
     *
     * @var Hoa_Core_Data array
     */
    protected $_data = array();

    /**
     * Temporize the branch name.
     *
     * @var Hoa_Core_Data string
     */
    protected $_temp = null;



    /**
     * Get a branch.
     *
     * @access  public
     * @param   string  $name    Branch name.
     * @return  Hoa_Core_Data
     */
    public function __get ( $name ) {

        $this->_temp = $name;

        return $this;
    }

    /**
     * Check if the n-th branch exists.
     *
     * @access  public
     * @param   mixed   $offset    Branch index.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        if(null === $this->_temp || !is_int($offset))
            return false;

        return true === array_key_exists($offset, $this->_data[$this->_temp]);
    }

    /**
     * Get the n-th branch.
     *
     * @access  public
     * @param   mixed   $offset    Branch index. Could be null to
     *                             auto-increment.
     * @return  Hoa_Core_Data
     */
    public function offsetGet ( $offset ) {

        if(null === $this->_temp)
            return;

        $handle      = $this->_temp;
        $this->_temp = null;

        if(false === array_key_exists($handle, $this->_data)) {

            $this->_data[$handle] = array();

            if(null === $offset)
                return $this->_data[$handle][] = new self();

            return $this->_data[$handle][$offset] = new self();
        }

        if(null === $offset)
            return $this->_data[$handle][] = new self();

        return $this->_data[$handle][$offset];
    }

    /**
     * Set the n-th branch.
     *
     * @access  public
     * @param   mixed   $offset    Branch index. Could be null to
     *                             auto-increment.
     * @param   mixed   $value     Branche value (scalar or array value).
     * @return  Hoa_Core_Data
     */
    public function offsetSet ( $offset, $value ) {

        if(null === $this->_temp)
            return;

        if(true === is_array($value)) {

            $handle = $this->_data[$this->_temp][$offset] = new self();

            foreach($value as $key => $ii)
                foreach($ii as $i => $value)
                    $handle->__get($key)->offsetSet($i, $value);

            $this->_temp = null;

            return;
        }

        if(null === $offset)
            $this->_data[$this->_temp][]        = $value;
        else
            $this->_data[$this->_temp][$offset] = $value;

        $this->_temp                            = null;

        return;
    }

    /**
     * Unset the n-th branch.
     *
     * @access  public
     * @param   mixed   $offset    Branch index.
     * @return  Hoa_Core_Data
     */
    public function offsetUnset ( $offset ) {

        if(null === $this->_temp)
            return;

        if(null === $offset)
            return;

        unset($this->_data[$this->_temp][$offset]);
        $this->_temp = null;

        return;
    }

    /**
     * Transform data as universal structure.
     *
     * @access  public
     * @return  array
     */
    public function toArray ( ) {

        $out = array();

        foreach($this->_data as $key => $ii)
            foreach($ii as $i => $value)
                if(is_object($value))
                    $out[$i][$key] = $value->toArray();
                else
                    $out[$i][$key] = $value;

        return $out;
    }
}
