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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Iterator
 */
-> import('Iterator.~');

}

namespace Hoa\Test\Selector {

/**
 * Class \Hoa\Test\Selector.
 *
 * .
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Selector implements \Hoa\Iterator {

    /**
     * Selections of domains for variables.
     *
     * @var \Hoa\Test\Selector array
     */
    protected $_selections = array();



    /**
     *
     */
    abstract public function __construct ( Array $variables );

    /**
     * Get the current collection for the iterator.
     *
     * @access  public
     * @return  mixed
     */
    public function current ( ) {

        return current($this->_selections);
    }

    /**
     * Get the current collection name for the iterator.
     *
     * @access  public
     * @return  mixed
     */
    public function key ( ) {

        return key($this->_selections);
    }

    /**
     * Advance the internal collection pointer, and return the current
     * collection.
     *
     * @access  public
     * @return  mixed
     */
    public function next ( ) {

        return next($this->_selections);
    }

    /**
     * Rewind the internal collection pointer, and return the first collection.
     *
     * @access  public
     * @return  mixed
     */
    public function rewind ( ) {

        return reset($this->_selections);
    }

    /**
     * Check if there is a current element after calls to the rewind() or the
     * next() methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty($this->_selections))
            return false;

        $key    = key($this->_selections);
        $return = (bool) next($this->_selections);
        prev($this->_selections);

        if(false === $return) {

            end($this->_selections);

            if($key === key($this->_selections))
                $return = true;
        }

        return $return;
    }
}

}
