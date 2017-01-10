<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Cache\Frontend;

use Hoa\Cache;

/**
 * Class \Hoa\Cache\Frontend\AClass.
 *
 * Class catching system for frontend cache.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class AClass extends Frontend
{
    /**
     * Object to cache.
     *
     * @var mixed
     */
    protected $_object    = null;

    /**
     * Method arguments.
     *
     * @var array
     */
    protected $_arguments = [];



    /**
     * Redirect constructor call to __call method if necessary. Else, it's like
     * the parent constructor.
     *
     */
    public function __construct()
    {
        $arguments = func_get_args();

        if (null === $this->_object) {
            if (isset($arguments[1])) {
                return parent::__construct($arguments[0], $arguments[1]);
            } else {
                return parent::__construct($arguments[0]);
            }
        }

        return $this->__call('__construct', $arguments);
    }

    /**
     * Overload member class with __call.
     * When we call method on this object, all should be redirected to set
     * object.
     *
     * @param   string  $method       Method called.
     * @param   array   $arguments    Arguments of method.
     * @return  mixed
     * @throws  \Hoa\Cache\Exception
     */
    public function __call($method, array $arguments)
    {
        $gc =
            is_string($this->_object)
                ? $this->_object
                : get_class($this->_object);

        if (!method_exists($this->_object, $method)) {
            throw new Cache\Exception(
                'Method %s of %s object does not exists.',
                0,
                [$method, $gc]
            );
        }

        $this->_arguments = $this->ksort($arguments);
        $idExtra          = serialize($this->_arguments);
        $this->makeId($gc . '::' . $method . '/' . $idExtra);
        $content = $this->_backend->load();

        if (false !== $content) {
            echo $content[0];   // output

            return $content[1]; // return
        }

        ob_start();
        ob_implicit_flush(false);

        if (is_string($this->_object) && $method == '__construct') {
            $reflection = new \ReflectionClass($this->_object);

            if (!$reflection->isInstantiable()) {
                throw new Cache\Exception(
                    'Class %s is not instanciable.',
                    1,
                    $this->_object
                );
            }

            $this->_object = $reflection->newInstanceArgs($arguments);
            $return        = $this->_object;
        } else {
            $return = call_user_func_array(
                [$this->_object, $method],
                $arguments
            );
        }

        $output = ob_get_contents();
        ob_end_clean();

        $this->_backend->store([$output, $return]);
        $this->removeId();

        echo $output;

        return $return;
    }

    /**
     * Set object to call.
     *
     * @param   mixed  $object    Could be an instance or a string for static call.
     * @return  ojbect
     * @throws  \Hoa\Cache\Exception
     */
    public function setCacheObject($object = null)
    {
        if (is_string($object) || is_object($object)) {
            $this->_object = $object;

            return $this;
        }

        throw new Cache\Exception(
            '%s could be a string or a object.',
            2,
            $object
        );
    }
}
