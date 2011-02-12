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
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Test\Praspel\Clause {

/**
 * Class \Hoa\Test\Praspel\Clause\Throwable.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Throwable implements Clause, \Hoa\Visitor\Element {

    /**
     * List of exception names.
     *
     * @var \Hoa\Test\Praspel\Clause\Throwable array
     */
    protected $_list = array();

    /**
     * Make a conjunction between two exception name declarations.
     *
     * @var \Hoa\Test\Praspel\Clause\Throwable object
     */
    public $_comma   = null;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_comma = $this;

        return;
    }

    /**
     * Add an exception name that could be thrown.
     *
     * @access  public
     * @param   string  $name    Exception name.
     * @return  \Hoa\Test\Praspel\Clause\Throwable
     */
    public function couldThrow ( $name ) {

        if(false === in_array($name, $this->getList()))
            $this->_list[] = $name;

        return $this;
    }

    /**
     * Check if an exception is declared in the list.
     *
     * @access  public
     * @param   string  $name    Exception name.
     * @return  bool
     */
    public function exceptionExists ( $name ) {

        if(true === in_array($name, $this->getList()))
            return true;

        foreach($this->getList() as $classname)
            if(true === is_subclass_of($name, $classname))
                return true;

        return false;
    }

    /**
     * Get list of exceptions.
     *
     * @access  public
     * @return  array
     */
    public function getList ( ) {

        return $this->_list;
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
