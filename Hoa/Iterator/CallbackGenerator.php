<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Iterator;

/**
 * Class \Hoa\Iterator\CallbackGenerator.
 *
 * Yield a value based on a callback.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class CallbackGenerator implements Iterator {

    /**
     * Callback.
     *
     * @var \Hoa\Iterator\CallbackGenerator callable
     */
    protected $_callback = null;

    /**
     * Current key.
     *
     * @var \Hoa\Iterator\Demultiplexer mixed
     */
    protected $_key      = 0;

    /**
     * Current computed value.
     *
     * @var \Hoa\Iterator\Demultiplexer mixed
     */
    protected $_current  = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   callable  $callback    Callback.
     * @return  void
     */
    public function __construct ( Callable $callback ) {

        $this->_callback = $callback;

        return;
    }

    /**
     * Return the current element.
     *
     * @access  public
     * @return  mixed
     */
    public function current ( ) {

        $handle = $this->_callback;

        return $this->_current = $handle($this->_key);
    }

    /**
     * Return the key of the current element.
     *
     * @access  public
     * @return  mixed
     */
    public function key ( ) {

        return $this->_key;
    }

    /**
     * Move forward to next element.
     *
     * @access  public
     * @return  void
     */
    public function next ( ) {

        ++$this->_key;

        return;
    }

    /**
     * Rewind the iterator to the first element.
     *
     * @access  public
     * @return  void
     */
    public function rewind ( ) {

        $this->_key     = 0;
        $this->_current = null;

        return;
    }

    /**
     * Check if current position is valid.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        return true;
    }
}