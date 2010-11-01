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
 * @subpackage  Hoa_Reflection_RClass
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Reflection_Exception
 */
import('Reflection.Exception');

/**
 * Hoa_Reflection_Wrapper
 */
import('Reflection.Wrapper');

/**
 * Hoa_Reflection_RFunction_RMethod
 */
import('Reflection.RFunction.RMethod');

/**
 * Class Hoa_Reflection_RClass.
 *
 * Extending ReflectionClass capacities.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Reflection
 * @subpackage  Hoa_Reflection_RClass
 */

class Hoa_Reflection_RClass extends Hoa_Reflection_Wrapper {

    /**
     * Class file.
     *
     * @var Hoa_Reflection_Rclass string
     */
    protected $_file    = null;

    /**
     * Class name.
     *
     * @var Hoa_Reflection_Rclass string
     */
    protected $_name    = null;

    /**
     * Whether methods were already transformed or not.
     *
     * @var Hoa_Reflection_Rclass bool
     */
    protected $_firstM  = true;

    /**
     * All methods.
     *
     * @var Hoa_Reflection_Rclass array
     */
    protected $_methods = array();



    /**
     * Reflect a class.
     *
     * @access  public
     * @param   mixed   $class    Class name or ReflectionClass instance.
     * @return  void
     */
    public function __construct ( $class ) {

        if($class instanceof ReflectionClass)
            $this->setWrapped($class);
        else
            $this->setWrapped(new ReflectionClass($class));

        $this->setName($this->getWrapped()->getName());

        return;
    }

    /**
     * Set class name.
     *
     * @access  public
     * @return  string
     */
    public function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get class name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get all methods.
     *
     * @access  public
     * @return  array
     */
    public function getMethods ( ) {

        if(false === $this->_firstM)
            return $this->_methods;

        $handle = null;

        foreach($this->getWrapped()->getMethods() as $i => $method) {

            $handle = new Hoa_Reflection_RFunction_RMethod($method);
            $handle->_setFile($this->_file);

            $this->_methods[] = $handle;
        }

        $this->_firstM = false;

        return $this->_methods;
    }

    /**
     * Import a fragment.
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Reflection_Exception
     */
    public function importFragment ( $fragment ) {

        if($fragment instanceof Hoa_Reflection_Fragment_RMethod)
            $this->_methods[] = $fragment;
        else
            throw new Hoa_Reflection_Exception(
                'Unknown fragment %s; cannot import it.',
                0, get_class($fragment));

        return;
    }

    /**
     * Pretty-printer.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = $this->getDocComment() . "\n";

        if(true === $this->isFinal())
            $out .= 'final ';

        if(true === $this->isAbstract())
            $out .= 'abstract ';

        if(true === $this->isInterface())
            $out .= 'interface ';
        else
            $out .= 'class ';

        $out        .= $this->getName() . ' ';
        $parent      = $this->getParentClass();
        $interfaces  = $this->getInterfaceNames();

        if(!empty($parent))
            $out .= 'extends ' . $parent . ' ';

        if(!empty($interfaces))
            $out .= 'implements ' . implode($interface, ', ') . ' ';

        $out .= '{' . "\n";

        // We lost API documentation of constants :-(.
        foreach($this->getConstants() as $name => $value)
            $out .= '    const ' . $name . ' = ' .
                    var_export($value, true) . ";\n";

        // PROPERTIES!!

        foreach($this->getMethods() as $name => $method)
            $out .= $method . "\n";

        return $out . '}';
    }
}
