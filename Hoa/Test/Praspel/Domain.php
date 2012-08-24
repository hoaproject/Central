<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
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
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
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
                    = $this->_factory('const' . $type, array($argument));
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
            case 'class':
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
