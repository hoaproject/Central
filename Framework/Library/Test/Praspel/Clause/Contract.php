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
 * \Hoa\Test\Praspel\Clause
 */
-> import('Test.Praspel.Clause.~')

/**
 * \Hoa\Test\Praspel\Variable
 */
-> import('Test.Praspel.Variable')

/**
 * \Hoa\Test\Praspel\Constructor\Old
 */
-> import('Test.Praspel.Constructor.Old')

/**
 * \Hoa\Test\Praspel\Constructor\Result
 */
-> import('Test.Praspel.Constructor.Result')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Test\Praspel\Clause {

/**
 * Class \Hoa\Test\Praspel\Clause\Contract.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

abstract class Contract implements Clause, \Hoa\Visitor\Element {

    /**
     * Parent (here: the root).
     *
     * @var \Hoa\Test\Praspel\Contract object
     */
    protected $_parent    = null;

    /**
     * Collection of variables.
     *
     * @var \Hoa\Test\Praspel\Clause\Contract array
     */
    protected $_variables = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Test\Praspel\Contract  $parent    Parent (here: the root).
     * @return  void
     */
    public function __construct ( \Hoa\Test\Praspel\Contract $parent ) {

        $this->setParent($parent);

        return;
    }

    /**
     * Declare a variable, or get it.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  \Hoa\Test\Praspel\Variable
     */
    public function variable ( $name ) {

        if(true === $this->variableExists($name))
            return $this->_variables[$name];

        if(0 !== preg_match('#\\\old\(\s*(\w+)\s*\)#i', $name, $matches))
            return $this->_variables[$name] = new \Hoa\Test\Praspel\Constructor\Old(
                $this,
                $matches[1]
            );
        elseif($name == '\result')
            return $this->_variables[$name] = new \Hoa\Test\Praspel\Constructor\Result(
                $this,
                $name
            );

        return $this->_variables[$name] = new \Hoa\Test\Praspel\Variable(
            $this,
            $name
        );
    }

    /**
     * Check if a variable already exists or not.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  \Hoa\Test\Praspel\Variable
     */
    public function variableExists ( $name ) {

        return true === array_key_exists($name, $this->getVariables());
    }

    /**
     * Get a specific variable.
     *
     * @access  public
     * @param   string  $name    Variable name.
     * @return  \Hoa\Test\Praspel\Variable
     * @throw   \Hoa\Test\Praspel\Exception
     */
    public function getVariable ( $name ) {

        if(false === $this->variableExists($name))
            throw new \Hoa\Test\Praspel\Exception(
                'Variable %s is not found.', 0, $name);

        return $this->_variables[$name];
    }

    /**
     * Get all variables.
     *
     * @access  public
     * @return  array
     */
    public function getVariables ( ) {

        return $this->_variables;
    }

    /**
     * Set the parent (here: the root).
     *
     * @access  protected
     * @param   \Hoa\Test\Praspel\Contract  $parent    Parent (here: the root).
     * @return  \Hoa\Test\Praspel\Contract
     */
    protected function setParent ( \Hoa\Test\Praspel\Contract $parent ) {

        $old           = $this->_parent;
        $this->_parent = $parent;

        return $old;
    }

    /**
     * Get the parent (here: the root).
     *
     * @access  public
     * @return  \Hoa\Test\Praspel\Contract
     */
    public function getParent ( ) {

        return $this->_parent;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
