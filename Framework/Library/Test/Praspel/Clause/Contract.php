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
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
