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

namespace Hoa\Promise;

use Hoa\Consistency;

/**
 * Class \Hoa\Promise\Promise.
 *
 *
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Promise
{
    const STATE_PENDING   = 0;
    const STATE_FULFILLED = 1;
    const STATE_REJECTED  = 2;

    const HANDLER_ONFULFILLED = 0;
    const HANDLER_FULFILL     = 1;
    const HANDLER_ONREJECTED  = 2;
    const HANDLER_REJECT      = 3;

    protected $_state    = self::STATE_PENDING;
    protected $_value    = null;
    protected $_deferred = null;

    public function __construct($executor = null)
    {
        if (null !== $executor) {
            $executor(
                xcallable($this, 'resolve'),
                xcallable($this, 'reject')
            );
        }

        return;
    }

    public function resolve($value)
    {
        if (self::STATE_PENDING !== $this->_state) {
            throw new Exception(
                'This promise is not pending, cannot resolve it.',
                0
            );
        }

        try {
            if ($value instanceof self) {
                $value->then(
                    xcallable($this, 'resolve'),
                    xcallable($this, 'reject')
                );

                return;
            }

            $this->setValue($value);
            $this->_state = self::STATE_FULFILLED;

            if (null === $this->_deferred) {
                return;
            }

            $this->handle($this->_deferred);
        } catch (\Exception $e) {
            $this->reject($e);
        }

        return;
    }

    public function reject($reason)
    {
        if (self::STATE_PENDING !== $this->_state) {
            throw new Exception(
                'This promise is not pending, cannot reject it.',
                1
            );
        }

        $this->setValue($reason);
        $this->_state = self::STATE_REJECTED;

        if (null === $this->_deferred) {
            return;
        }

        $this->handle($this->_deferred);

        return;
    }

    protected function handle($handler)
    {
        if (self::STATE_PENDING === $this->_state) {
            $this->_deferred = $handler;

            return;
        }

        $handlerOn = null;

        if (true === $this->isFulfilled()) {
            $handlerOn = $handler[self::HANDLER_ONFULFILLED];
        } else {
            $handlerOn = $handler[self::HANDLER_ONREJECTED];
        }

        $out = null;

        if (null === $handlerOn) {
            $out = $this->getValue();
        } else {
            try {
                $out = $handlerOn($this->getValue());
            } catch (\Exception $e) {
                $handler[self::HANDLER_REJECT]($e);

                return;
            }
        }

        if (true === $this->isFulfilled()) {
            $handler[self::HANDLER_FULFILL]($out);
        } else {
            $handler[self::HANDLER_REJECT]($out);
        }

        return;
    }

    public function then($onFulfilled = null, $onRejected = null)
    {
        $self = $this;

        return new static(
            function ($fulfill, $reject) use (
                $self,
                $onFulfilled,
                $onRejected
            ) {
                $self->handle([
                    self::HANDLER_ONFULFILLED => $onFulfilled,
                    self::HANDLER_FULFILL     => $fulfill,
                    self::HANDLER_ONREJECTED  => $onRejected,
                    self::HANDLER_REJECT      => $reject
                ]);

                return;
            }
        );
    }

    public function __call($name, $arguments)
    {
        if ('catch' === $name) {
            if (!isset($arguments[0])) {
                throw new Exception(
                    'The catch method must have one argument.',
                    2
                );
            }

            return $this->then(null, $arguments[0]);
        }

        throw new Exception(
            'Method %s does not exist.',
            3,
            $name
        );
    }

    public function isPending()
    {
        return self::STATE_PENDING === $this->_state;
    }

    public function isFulfilled()
    {
        return self::STATE_FULFILLED === $this->_state;
    }

    public function isRejected()
    {
        return self::STATE_REJECTED === $this->_state;
    }

    protected function setValue($value)
    {
        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    public function getValue()
    {
        return $this->_value;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Promise\Promise');
