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

namespace Hoa\Reflection {

/**
 * Class \Hoa\Reflection\Wrapper.
 *
 * Wrap some class and emulate inheritance.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Wrapper {

    /**
     * The wrapped object.
     *
     * @var \Hoa\Reflection\Wrapped object
     */
    private $_wrapped = null;



    /**
     * Set the wrapped object.
     *
     * @access  protected
     * @param   object     $wrapped    Wrapped object.
     * @return  object
     */
    protected function setWrapped ( $wrapped ) {

        $old            = $this->_wrapped;
        $this->_wrapped = $wrapped;

        return $old;
    }

    /**
     * Get the wrapped object.
     *
     * @access  protected
     * @return  object
     */
    protected function getWrapped ( ) {

        return $this->_wrapped;
    }

    /**
     * Magic setter.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   mixed   $value    Value.
     * @return  mixed
     */
    public function __set ( $name, $value ) {

        if(null === $this->_wrapped)
            return null;

        return $this->_wrapped->$name = $value;
    }

    /**
     * Magic getter.
     *
     * @access  public
     * @return  mixed
     */
    public function __get ( $name ) {

        if(null === $this->_wrapped)
            return null;

        return $this->_wrapped->$name;
    }

    /**
     * Magic caller.
     *
     * @access  public
     * @param   string  $name         Method name.
     * @param   array   $arguments    Method arguments.
     * @return  mixed
     */
    public function __call ( $name, Array $arguments ) {

        if(null === $this->_wrapped)
            return null;

        return call_user_func_array(
            array($this->_wrapped, $name),
            $arguments
        );
    }

    /**
     * Magic static caller.
     *
     * @access  public
     * @param   string  $name         Method name.
     * @param   array   $arguments    Method arguments.
     * @return  mixed
     */
    public static function __callStatic ( $name, Array $arguments ) {

        if(null === $this->_wrapped)
            return null;

        return call_user_func_array(
            array($this->_wrapped, $name),
            $arguments
        );
    }
}

}
