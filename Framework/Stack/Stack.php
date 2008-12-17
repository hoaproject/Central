<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 * @package     Hoa_Stack
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Class Hoa_Stack.
 *
 * This class helps to manage array and built a stack.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Stack
 */

class Hoa_Stack {

    /**
     * Stack.
     *
     * @var Hoa_Stack array
     */
    protected $stack = array();



	/**
	 * push
	 * Push one or more elements onto the end of array.
	 *
	 * @access  public
	 * @param   e       mixed    Element to add.
	 * @return  int
	 */
	public function push ( $e ) {

		return array_push($this->stack, $e);
	}

	/**
	 * unshift
	 * Prepend one or more elements to the beginning of an array.
	 *
	 * @access  public
	 * @param   e       mixed    Element to add.
	 * @return  int
	 */
	public function unshift ( $e ) {

		return array_unshift($this->stack, $e);
	}

	/**
	 * shift
	 * Shift an element off the beginning of array.
	 *
	 * @access  public
	 * @return  mixed
	 */
	public function shift ( ) {

		return array_shift($this->stack);
	}

	/**
	 * pop
	 * Pop the element off the end of array.
	 *
	 * @access  public
	 * @return  mixed
	 */
	public function pop ( ) {

		return array_pop($this->stack);
	}

	/**
	 * peek
	 * Set and return the internal pointer of an array to its last element.
	 *
	 * @access  public
	 * @return  mixed
	 */
	public function peek ( ) {

		return end($this->stack);
	}

	/**
	 * get
	 * Return this stack.
	 *
	 * @access  public
	 * @return  array
	 */
	public function get ( ) {

		return $this->stack;
	}

	/**
	 * search
	 * Searches the array for a given value and returns the corresponding key if successful.
	 *
	 * @access  public
	 * @param   e       mixed    Element to search.
	 * @return  mixed
	 */
	public function search ( $e ) {

		return array_search($e, $this->stack, true);
	}

	/**
	 * cnt
	 * Count elements in an array, or properties in an object.
	 *
	 * @access  public
	 * @return  int
	 */
	public function cnt ( ) {

		return count($this->stack);
	}

	/**
	 * rset
	 * Reset stack.
	 *
	 * @access  public
	 * @return  array
	 */
	public function rset ( ) {

		return $this->stack = array();
	}

}
