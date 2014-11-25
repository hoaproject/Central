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

namespace Hoa\Promise;

use Hoa\Core;

/**
 * Class \Hoa\Promise\Promise.
 *
 * 
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Promise {

    const STATE_PENDING   = 0;
    const STATE_FULFILLED = 1;
    const STATE_REJECTED  = 2;

    const HANDLER_ONFULFILLED = 0;
    const HANDLER_FULFILL     = 1;

    protected $_state    = self::STATE_PENDING;
    protected $_value    = null;
    protected $_deferred = null;

    public function __construct ( $callback = null ) {

        if(null !== $callback)
            $callback(xcallable($this, 'fulfill'));

        return;
    }

    public function fulfill ( $value ) {

        if($value instanceof self) {

            $value->then(xcallable($this, 'fulfill'));

            return;
        }

        $this->_value = $value;
        $this->_state = self::STATE_FULFILLED;

        if(null === $this->_deferred)
            return;

        $this->handle($this->_deferred);

        return;
    }

    protected function handle ( $handler ) {

        if(self::STATE_PENDING === $this->_state) {

            $this->_deferred = $handler;

            return;
        }

        if(null === $handler[self::HANDLER_ONFULFILLED]) {

            $handler[self::HANDLER_FULFILL]($this->_value);

            return;
        }

        $out = $handler[self::HANDLER_ONFULFILLED]($this->_value);
        $handler[self::HANDLER_FULFILL]($out);

        return;
    }

    public function then ( $onFulfilled = null ) {

        $self = $this;

        return new static(function ( $fulfill ) use ( $self, $onFulfilled ) {

            $self->handle([
                self::HANDLER_ONFULFILLED => $onFulfilled,
                self::HANDLER_FULFILL     => $fulfill
            ]);
        });
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Promise\Promise');
