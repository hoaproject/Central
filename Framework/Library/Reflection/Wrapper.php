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
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_Wrapper
 *
 */

/**
 * Class Hoa_Reflection_Wrapper.
 *
 * Wrap some class and emulate inheritance.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_Wrapper
 */

class Hoa_Reflection_Wrapper {

    /**
     * The wrapped object.
     *
     * @var Hoa_Reflection_Wrapped object
     */
    private $_wrapped = null;



    /**
     * Set the wrapped object.
     *
     * @access  protected
     * @param   object     $wrapped    Wrapped object.
     * @return  object
     */
    protected function setWrapped ( $wrapped ) {

        $old            = $this->_wrapped;
        $this->_wrapped = $wrapped;

        return $old;
    }

    /**
     * Get the wrapped object.
     *
     * @access  protected
     * @return  object
     */
    protected function getWrapped ( ) {

        return $this->_wrapped;
    }

    /**
     * Magic setter.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  mixed
     */
    public function __set ( $name, $value ) {

        if(null === $this->_wrapped)
            return null;

        return $this->_wrapped->$name = $value;
    }

    /**
     * Magic getter.
     *
     * @access  public
     * @return  mixed
     */
    public function __get ( $name ) {

        if(null === $this->_wrapped)
            return null;

        return $this->_wrapped->$name;
    }

    /**
     * Magic caller.
     *
     * @access  public
     * @param   string  $name         Method name.
     * @param   array   $arguments    Method arguments.
     * @return  mixed
     */
    public function __call ( $name, Array $arguments ) {

        if(null === $this->_wrapped)
            return null;

        return call_user_func_array(
            array($this->_wrapped, $name),
            $arguments
        );
    }

    /**
     * Magic static caller.
     *
     * @access  public
     * @param   string  $name         Method name.
     * @param   array   $arguments    Method arguments.
     * @return  mixed
     */
    public static function __callStatic ( $name, Array $arguments ) {

        if(null === $this->_wrapped)
            return null;

        return call_user_func_array(
            array($this->_wrapped, $name),
            $arguments
        );
    }
}
