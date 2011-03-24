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
 * \Hoa\Cache\Frontend
 */
-> import('Cache.Frontend.~');

}

namespace Hoa\Cache\Frontend {

/**
 * Class \Hoa\Cache\Frontend\AClass.
 *
 * Class catching system for frontend cache.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class AClass extends Frontend {

    /**
     * Object to cache.
     *
     * @var \Hoa\Cache\Frontend\AClass mixed
     */
    protected $_object    = null;

    /**
     * Method arguments.
     *
     * @var \Hoa\Cache\Frontend\AClass array
     */
    protected $_arguments = array();



    /**
     * Redirect constructor call to __call method if necessary. Else, it's like
     * the parent constructor.
     *
     * @access  public
     * @return  mixed
     */
    public function __construct ( ) {

        $arguments = func_get_args();

        if(null === $this->_object)
            if(isset($arguments[1]))
                return parent::__construct($arguments[0], $arguments[1]);
            else
                return parent::__construct($arguments[0]);

        return $this->__call('__construct', $arguments);
    }

    /**
     * Overload member class with __call.
     * When we call method on this object, all should be redirected to set
     * object.
     *
     * @access  public
     * @param   string  $method       Method called.
     * @param   array   $arguments    Arguments of method.
     * @return  mixed
     * @throw   \Hoa\Cache\Exception
     */
    public function __call ( $method, Array $arguments ) {

        $gc = is_string($this->_object)
                  ? $this->_object
                  : get_class($this->_object);

        if(!method_exists($this->_object, $method))
            throw new \Hoa\Cache\Exception(
                'Method %s of %s object does not exists.',
                0, array($method, $gc));

        $this->_arguments = $this->ksort($arguments);
        $idExtra          = serialize($this->_arguments);
        $this->makeId($gc . '::' . $method . '/' .  $idExtra);
        $content          = $this->_backend->load();

        if(false !== $content) {

            echo $content[0];   // output

            return $content[1]; // return
        }

        ob_start();
        ob_implicit_flush(false);

        if(is_string($this->_object) && $method == '__construct') {

            $reflection = new \ReflectionClass($this->_object);

            if(!$reflection->isInstantiable())
                throw new \Hoa\Cache\Exception(
                    'Class %s is not instanciable.', 1, $this->_object);

            $this->_object = $reflection->newInstanceArgs($arguments);
            $return        = $this->_object;
        }
        else
            $return = call_user_func_array(
                array($this->_object, $method),
                $arguments
            );

        $output = ob_get_contents();
        ob_end_clean();

        $this->_backend->store(array($output, $return));
        $this->removeId();

        echo $output;

        return $return;
    }

    /**
     * Set object to call.
     *
     * @access  public
     * @param   mixed  $object    Could be an instance or a string for static call.
     * @return  ojbect
     * @throw   \Hoa\Cache\Exception
     */
    public function setCacheObject ( $object = null ) {

        if(is_string($object) || is_object($object)) {

            $this->_object = $object;

            return $this;
        }

        throw new \Hoa\Cache\Exception('%s could be a string or a object.',
            2, $object);
    }
}

}
