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

namespace Hoa\Core {

/**
 * Class \Hoa\Core\Data.
 *
 * Universal data structure.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Data implements \ArrayAccess {

    /**
     * Data as intuitive structure.
     *
     * @var \Hoa\Core\Data array
     */
    protected $_data = array();

    /**
     * Temporize the branch name.
     *
     * @var \Hoa\Core\Data string
     */
    protected $_temp = null;



    /**
     * Get a branch.
     *
     * @access  public
     * @param   string  $name    Branch name.
     * @return  \Hoa\Core\Data
     */
    public function __get ( $name ) {

        $this->_temp = $name;

        return $this;
    }

    /**
     * Set a branch.
     * Notice that it will always reach the (n+1)-th branch.
     *
     * @access  public
     * @param   string  $name     Branch name.
     * @param   mixed   $value    Branch value (scalar or array value).
     * @return  \Hoa\Core\Data
     */
    public function __set ( $name, $value ) {

        $this->_temp = $name;

        return $this->offsetSet(null, $value);
    }

    /**
     * Check if the n-th branch exists.
     *
     * @access  public
     * @param   mixed   $offset    Branch index.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        if(null === $this->_temp || !is_int($offset))
            return false;

        return true === array_key_exists($offset, $this->_data[$this->_temp]);
    }

    /**
     * Get the n-th branch.
     *
     * @access  public
     * @param   mixed   $offset    Branch index. Could be null to
     *                             auto-increment.
     * @return  \Hoa\Core\Data
     */
    public function offsetGet ( $offset ) {

        if(null === $this->_temp)
            return;

        $handle      = $this->_temp;
        $this->_temp = null;

        if(false === array_key_exists($handle, $this->_data)) {

            $this->_data[$handle] = array();

            if(null === $offset)
                return $this->_data[$handle][] = new self();

            return $this->_data[$handle][$offset] = new self();
        }

        if(   null  === $offset
           || false === array_key_exists($offset, $this->_data[$handle]))
            return $this->_data[$handle][] = new self();

        return $this->_data[$handle][$offset];
    }

    /**
     * Set the n-th branch.
     *
     * @access  public
     * @param   mixed   $offset    Branch index. Could be null to
     *                             auto-increment.
     * @param   mixed   $value     Branche value (scalar or array value).
     * @return  \Hoa\Core\Data
     */
    public function offsetSet ( $offset, $value ) {

        if(null === $this->_temp)
            return;

        if(null === $offset)
            if(is_array($value))
                foreach($value as $k => $v)
                    $this->_data[$this->_temp][] = $v;
            else
                $this->_data[$this->_temp][]     = $value;
        else
            $this->_data[$this->_temp][$offset]  = $value;

        $this->_temp                             = null;

        return;
    }

    /**
     * Unset the n-th branch.
     *
     * @access  public
     * @param   mixed   $offset    Branch index.
     * @return  \Hoa\Core\Data
     */
    public function offsetUnset ( $offset ) {

        if(null === $this->_temp)
            return;

        if(null === $offset)
            return;

        unset($this->_data[$this->_temp][$offset]);
        $this->_temp = null;

        return;
    }

    /**
     * Transform data as universal structure.
     *
     * @access  public
     * @return  array
     */
    public function toArray ( ) {

        $out = array();

        foreach($this->_data as $key => $ii)
            foreach($ii as $i => $value)
                if(is_object($value))
                    $out[$i][$key] = $value->toArray();
                else
                    $out[$i][$key] = $value;

        return $out;
    }
}

}
