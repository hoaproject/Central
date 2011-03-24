<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Test\Praspel\DomainDisjunction
 */
-> import('Test.Praspel.DomainDisjunction');

}

namespace Hoa\Test\Praspel {

/**
 * Class \Hoa\Test\Praspel\ArrayDescription.
 *
 * Represents an array description.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class ArrayDescription extends DomainDisjunction {

    /**
     * Parent (here: domain).
     *
     * @var \Hoa\Test\Praspel\Domain
     */
    protected $_parent = null;

    /**
     * Describe an automata A(Q, I, F, α, Γ) where:
     *     Q = {q_0, q_1},     the states;
     *     I = q_0,            the initial state;
     *     F = {q_0},          the final states;
     *     α = {from, to},     the alphabet;
     *     Γ = {(q_0, to,   q_0),
     *          (q_0, from, q_1),
     *          (q_1, to,   q_1)}, the transitions.
     */
    private $_state    = 0;

    /**
     * Current key.
     *
     * @var \Hoa\Test\Praspel\DomainDisjunction array
     */
    protected $_key    = null;

    /**
     * Current value.
     *
     * @var \Hoa\Test\Praspel\DomainDisjunction array
     */
    protected $_value  = null;

    /**
     * Array of keys/values.
     *
     * @var \Hoa\Test\Praspel\ArrayDescription array
     */
    protected $_array  = array();

    /**
     * Go forward to set the next key/value pair.
     *
     * @var \Hoa\Test\Praspel\ArrayDescription object
     */
    public $_comma     = null;



    /**
     * Build an array description.
     *
     * @access  public
     * @param   \Hoa\Test\Praspel\Domain  $parent    Parent (here: domain).
     * @return  void
     */
    public function __construct ( Domain $parent ) {

        parent::__construct();

        $this->_comma = $this;
        $this->setParent($parent);

        return;
    }

    /**
     * Start a key declaration.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\ArrayDescription
     * @throws  \Hoa\Test\Praspel\Exception
     */
    public function from ( ) {

        $this->_to();

        if(0 != $this->_state)
            throw new Exception(
                'Array not well-formed.', 0);

        if(empty($this->_domains))
            return $this;

        return $this;
    }

    /**
     * Close a key declaration.
     *
     * @access  protected
     * @return  void
     */
    protected function _from ( ) {

        $this->_ok();

        if(!empty($this->_domains))
            $this->_key = array_values($this->getDomains());

        $this->_domains = array();
        $this->_state   = 1;

        return;
    }

    /**
     * Start a value declaration.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\ArrayDescription
     */
    public function to ( ) {

        $this->_from();

        return $this;
    }

    /**
     * Close a value declaration.
     *
     * @access  protected
     * @return  void
     */
    protected function _to ( ) {

        if(empty($this->_domains))
            return;

        $this->_ok();
        $this->_value   = array_values($this->getDomains());
        $this->_domains = array();
        $this->_state   = 0;
        $this->_array[] = array($this->_key, $this->_value);

        $this->_key     = null;
        $this->_value   = null;

        return;
    }

    /**
     * Close an array description.
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\Domain
     */
    public function end ( ) {

        $this->_to();

        $this->_parent->_currentArgument = $this->getArray();

        // break the reference.
        unset($this->_parent->_currentArgument);

        return $this->_parent;
    }

    /**
     * Set the parent (here: domain).
     *
     * @access  protected
     * @param   \Hoa\Test\Praspel\Domain  $parent    Parent (here: domain).
     * @return  \Hoa\Test\Praspel\Domain
     */
    protected function setParent ( Domain $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get the parent (here: domain).
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\Domain
     */
    public function getParent ( ) {

        return $this->_parent;
    }

    /**
     * Get the fresh built array.
     *
     * @access  public
     * @return  array
     */
    public function getArray ( ) {

        return $this->_array;
    }
}

}
