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
 * @subpackage  Hoa_Test_Praspel_Domain
 *
 */

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel_ArrayDescription
 */
import('Test.Praspel.ArrayDescription');

/**
 * Class Hoa_Test_Praspel_Domain.
 *
 * Represents a domain.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Domain
 */

class Hoa_Test_Praspel_Domain {

    /**
     * Parent (here: variable or domain).
     *
     * @var Hoa_Test_Praspel_Variable object
     */
    protected $_parent       = null;

    /**
     * Domain.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type object
     */
    protected $_domain       = null;

    /**
     * Type's name.
     *
     * @var Hoa_Test_Praspel_Domain string
     */
    protected $_name         = null;

    /**
     * Arguments.
     *
     * @var Hoa_Test_Praspel_Domain array
     */
    protected $_arguments    = array();

    /**
     * Current defining argument.
     * Yes, it is a public access, but we have no choice… It should be friend.
     *
     * @var Hoa_Test_Praspel_Domain mixed
     */
    public $_currentArgument = null;

    /**
     * Go forward to set the next argument on the current domain (and carry the
     * current used domain).
     *
     * @var Hoa_Test_Praspel_Domain object
     */
    public $_comma           = null;



    /**
     * Find and build the domain.
     *
     * @access  public
     * @param   mixed   $parent    Parent (here: variable or domain).
     * @param   string  $name      Domain name.
     * @return  void
     * @throws  Hoa_Test_Praspel_Exception
     */
    public function __construct ( $parent, $name ) {

        $this->setParent($parent);
        $this->setName($name);
        $this->_comma = $this;

        return;
    }

    /**
     * Add an argument to the current defining domain.
     *
     * @access  public
     * @param   mixed  $argument    Argument.
     * @return  Hoa_Test_Praspel_Variable
     */
    public function with ( $argument ) {

        $this->_currentArgument = $this->_arguments[] = $argument;

        return $this;
    }

    /**
     * Add an array argument to the current defining domain.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_ArrayDescription
     */
    public function withArray ( ) {

        $this->_currentArgument = new Hoa_Test_Praspel_ArrayDescription($this);
        $this->_arguments[]     = &$this->_currentArgument;

        return $this->_currentArgument;
    }

    /**
     * Add a domain argument to the current defining domain.
     *
     * @access  public
     * @param   string  $name    Domain name.
     * @return  Hoa_Test_Praspel_Variable
     */
    public function withDomain ( $name ) {

        $this->_currentArgument = new self($this, $name);
        $this->_arguments[]     = &$this->_currentArgument;

        return $this->_currentArgument;
    }

    /**
     * Close a session/context and return the parent.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Variable
     */
    public function _ok ( ) {

        if(!($this->_parent instanceof self))
            return $this->_parent->_ok();

        $this->_parent->_currentArgument = $this->getDomain();

        // break the reference.
        unset($this->_parent->_currentArgument);

        return $this->_parent;
    }

    /**
     * Factory of domain.
     *
     * @access  public
     * @param   string  $name         Domain name.
     * @param   array   $arguments    Domain arguments.
     * @return  void
     * @throws  Hoa_Exception
     */
    protected function _factory ( $name, Array $arguments ) {

        $name  = ucfirst($name);
        $class = 'Hoa_Realdom_' . $name;

        import('Realdom.' . $name);

        try {

            $reflection  = new ReflectionClass($class);

            if(true === $reflection->hasMethod('__construct'))
                $this->_domain = $reflection->newInstanceArgs($arguments);
            else
                $this->_domain = $reflection->newInstance();
        }
        catch ( ReflectionException $e ) {

            throw new Hoa_Test_Praspel_Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return;
    }

    /**
     * Get the found domain.
     *
     * @access  public
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function getDomain ( ) {

        if(null !== $this->_domain)
            return $this->_domain;

        $this->_factory($this->getName(), $this->getArguments());

        return $this->_domain;
    }

    /**
     * Set the parent (here: variable or domain).
     *
     * @access  protected
     * @param   mixed  $parent    Parent (here: variable or domain).
     * @return  Hoa_Test_Praspel_Variable
     */
    protected function setParent ( $parent ) {

        if(   !($parent instanceof Hoa_Test_Praspel_Variable)
           && !($parent instanceof Hoa_Test_Praspel_Domain)
           && !($parent instanceof Hoa_Test_Praspel_ArrayDescription))
           throw new Hoa_Test_Praspel_Exception(
                'Parent of a domain must be a variable, a domain or an array ' .
                'description, given %s.',
                0, get_class($parent));

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get the parent (here: variable or domain).
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Variable
     */
    public function getParent ( ) {

        return $this->_parent;
    }

    /**
     * Set the domain's name.
     *
     * @access  protected
     * @param   string  $name    Domain's name.
     * @return  string
     */
    protected function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the domain's name.
     *
     * @access  protected
     * @return  string
     */
    protected function getName ( ) {

        return $this->_name;
    }

    /**
     * Get domain's arguments.
     *
     * @access  protected
     * @return  array
     */
    protected function getArguments ( ) {

        return $this->_arguments;
    }
}
