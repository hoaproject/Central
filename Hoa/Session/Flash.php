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

namespace Hoa\Session;

/**
 * Class \Hoa\Session\Flash.
 *
 * Flash is a special top-namespace that contains namespaces which are
 * destructed when empty. Each data is destructed after a read.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Flash extends Session
{
    /**
     * Event channel.
     *
     * @const string
     */
    const EVENT_CHANNEL = 'hoa://Event/Session/Flash/';

    /**
     * Top-namespace. See parent::TOP_NAMESPACE for more informations.
     *
     * @const string
     */
    const TOP_NAMESPACE = '__Hoa_Flash__';



    /**
     * Manipulate a namespace.
     * If session has not been previously started, it will be done
     * automatically.
     *
     * @param   string  $namespace      Namespace.
     */
    public function __construct($namespace = '_defaultFlash')
    {
        parent::__construct($namespace, parent::NO_CACHE);

        return;
    }

    /**
     * Get a data.
     *
     * @param   mixed  $offset    Data name.
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        $out = parent::offsetGet($offset);
        $this->offsetUnset($offset);

        return $out;
    }

    /**
     * Unset a data.
     *
     * @param   mixed  $offset    Data name.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        parent::offsetUnset($offset);

        if (true === $this->isEmpty()) {
            $this->delete();
        }

        return;
    }

    /**
     * Iterate over data in the namespace.
     *
     * @return  \ArrayIterator
     */
    public function getIterator()
    {
        $out = parent::getIterator();
        $this->delete();

        return $out;
    }
}
