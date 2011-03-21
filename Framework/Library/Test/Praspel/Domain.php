<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Test\Praspel\Exception
 */
-> import('Test.Praspel.Exception')

/**
 * \Hoa\Test\Praspel\ArrayDescription
 */
-> import('Test.Praspel.ArrayDescription');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel\Domain.
 *
 * Represents a domain.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Domain {

    /**
     * Parent (here: variable or domain).
     *
     * @var \Hoa\Test\Praspel\Variable object
     */
    protected $_parent       = null;

    /**
     * Domain.
     *
     * @var \Hoa\Realdom object
     */
    protected $_domain       = null;

    /**
     * Domain's name.
     *
     * @var \Hoa\Test\Praspel\Domain string
     */
    protected $_name         = null;

    /**
     * Arguments.
     *
     * @var \Hoa\Test\Praspel\Domain array
     */
    protected $_arguments    = array();

    /**
     * Current defining argument.
     * Yes, it is a public access, but we have no choice… It should be friend.
     *
     * @var \Hoa\Test\Praspel\Domain mixed
     */
    public $_currentArgument = null;

    /**
     * Go forward to set the next argument on the current domain (and carry the
     * current used domain).
     *
     * @var \Hoa\Test\Praspel\Domain object
     */
    public $_comma           = null;



    /**
     * Find and build the domain.
     *
     * @access  public
     * @param   mixed   $parent    Parent (here: variable or domain).
     * @param   string  $name      Domain name.
     * @return  void
     * @throws  \Hoa\Test\Praspel\Exception
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
     * @return  \Hoa\Test\Praspel\Variable
     */
    public function with ( $argument ) {

        $name = $this->getName();

        switch($name) {

            case 'constboolean':
            case 'constfloat':
            case 'constinteger':
            case 'conststring':
                $this->_currentArgument = $this->_arguments[] = $argument;
              break;

            default:
                switch(gettype($argument)) {

                    case 'boolean':
                    case 'integer':
                    case 'string':
                        $type = gettype($argument);
                      break;

                    case 'double':
                        $type = 'float';
                      break;

                    default:
                        throw new Exception(
                            'The with() method does not support the type %s.',
                            0, gettype($argument));
                }

                $this->_currentArgument
                    = $this->_arguments[]
                    = $this->_factory('const' . $type, array($argument));;
        }

        return $this;
    }

    /**
     * Add an array argument to the current defining domain.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\ArrayDescription
     */
    public function withArray ( ) {

        $this->_currentArgument = new ArrayDescription($this);
        $this->_arguments[]     = &$this->_currentArgument;

        return $this->_currentArgument;
    }

    /**
     * Add a domain argument to the current defining domain.
     *
     * @access  public
     * @param   string  $name    Domain name.
     * @return  \Hoa\Test\Praspel\Variable
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
     * @return  \Hoa\Test\Praspel\Variable
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
     * @return  \Hoa\Realdom
     * @throws  \Hoa\Core\Exception
     */
    protected function _factory ( $name, Array $arguments ) {

        return dnew('(Hoathis or Hoa)\Realdom\\' . ucfirst($name), $arguments);
    }

    /**
     * Get the found domain.
     *
     * @access  public
     * @return  \Hoa\Realdom
     */
    public function getDomain ( ) {

        if(null !== $this->_domain)
            return $this->_domain;

        return $this->_domain = $this->_factory(
            $this->getName(),
            $this->getArguments()
        );
    }

    /**
     * Set the parent (here: variable or domain).
     *
     * @access  protected
     * @param   mixed  $parent    Parent (here: variable or domain).
     * @return  \Hoa\Test\Praspel\Variable
     */
    protected function setParent ( $parent ) {

        if(   !($parent instanceof Variable)
           && !($parent instanceof Domain)
           && !($parent instanceof ArrayDescription))
           throw new Exception(
                'Parent of a domain must be a variable, a domain or an array ' .
                'description, given %s.',
                1, get_class($parent));

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get the parent (here: variable or domain).
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\Variable
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

        $name = strtolower($name);

        switch($name) {

            case 'empty':
            case 'array':
                $name = '_' . ucfirst($name);
              break;
        }

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

}
