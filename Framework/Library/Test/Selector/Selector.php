<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
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
