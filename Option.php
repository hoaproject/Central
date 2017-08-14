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

namespace Hoa\Option;

use Hoa\Consistency;

/**
 * Class \Hoa\Option.
 *
 * …
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Option
{
    protected $_value = null;

    private function __construct($value)
    {
        $this->_value = $value;
    }

    public static function some($value): self
    {
        return new self($value);
    }

    public static function none(): self
    {
        return new self(null);
    }

    public function isSome(): bool
    {
        return false === $this->isNone();
    }

    public function isNone(): bool
    {
        return null === $this->_value;
    }

    public function expect(string $errorMessage)
    {
        if (true === $this->isNone()) {
            throw new \RuntimeException($errorMessage);
        }

        return $this->_value;
    }

    public function unwrap()
    {
        return $this->expect('Unwrap a null value.');
    }

    public function unwrapOr($defaultValue)
    {
        if (true === $this->isNone()) {
            return $defaultValue;
        }

        return $this->_value;
    }

    public function unwrapOrElse(callable $defaultValueGenerator)
    {
        if (true === $this->isNone()) {
            return $defaultValueGenerator();
        }

        return $this->_value;
    }

    public function map(callable $mapper): self
    {
        if (true === $this->isNone()) {
            $value = $this->_value;
        } else {
            $value = $mapper($this->_value);
        }

        return new self($value);
    }

    public function mapOr(callable $mapper, $defaultValue): self
    {
        if (true === $this->isNone()) {
            $value = $defaultValue;
        } else {
            $value = $mapper($this->_value);
        }

        return new self($value);
    }

    public function mapOrElse(callable $mapper, callable $defaultValueGenerator): self
    {
        if (true === $this->isNone()) {
            $value = $defaultValueGenerator();
        } else {
            $value = $mapper($this->_value);
        }

        return new self($value);
    }

    public function and(self $rightOption): self
    {
        if (true === $this->isNone()) {
            return self::none();
        }

        return $rightOption;
    }

    public function andThen(callable $mapper): self
    {
        if (true === $this->isNone()) {
            return self::none();
        }

        return new self($mapper($this->_value));
    }

    public function or(self $rightOption): self
    {
        if (true === $this->isNone()) {
            return $rightOption;
        }

        return $this;
    }

    public function orElse(callable $defaultValueGenerator): self
    {
        if (true === $this->isNone()) {
            return new self($defaultValueGenerator());
        }

        return $this;
    }
}

function Some($value): Option
{
    return Option::some($value);
}

function None(): Option
{
    return Option::none();
}

/**
 * Flex entity.
 */
Consistency::flexEntity(Option::class);
