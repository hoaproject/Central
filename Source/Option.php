<?php

declare(strict_types=1);

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
use RuntimeException;

/**
 * Class \Hoa\Option.
 *
 * This class is an implementation of the famous [`Option` polymorphic
 * type](https://en.wikipedia.org/wiki/Option_type) (also called `Maybe`). An
 * `Option` represents an optional value, either there is `Some` value, or
 * there is `None` which is the equivalent of `null`. This is a convenient and
 * safe way to manipulate an optional value.
 *
 * @license    New BSD License
 */
final class Option
{
    /**
     * Some value or none.
     *
     * The value can be anything. A `null` value is considered as None,
     * everything else is Some.
     */
    protected $_value = null;

    /**
     * Allocates a new option.
     *
     * The state of the option (Some or None) is based on the value itself.
     */
    private function __construct($value)
    {
        $this->_value = $value;
    }

    /**
     * Allocates a new option with some value.
     *
     * # Examples
     *
     * In this example, `Option::some(42)` and `Some(42)` are strictly
     * equivalent.
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\Some(42);
     *
     * assert($x->isSome());
     * assert($y->isSome());
     * ```
     */
    public static function some($value): self
    {
        if (null === $value) {
            throw new RuntimeException(
                'Called `' . __METHOD__ . '` with a `null` value, forbidden. ' .
                'Use `' . __CLASS__ . '::none` instead.'
            );
        }

        return new self($value);
    }

    /**
     * Allocates a new option with no value.
     *
     * # Examples
     *
     * In this example, `Option::none()` and `None()` are strictly equivalent.
     *
     * ```php
     * $x = Hoa\Option\None();
     * $y = Hoa\Option\None();
     *
     * assert($x->isNone());
     * assert($y->isNone());
     * ```
     */
    public static function none(): self
    {
        return new self(null);
    }

    /**
     * Returns `true` if the option contains some value.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\None();
     *
     * assert(true  === $x->isSome());
     * assert(false === $y->isSome());
     * ```
     */
    public function isSome(): bool
    {
        return false === $this->isNone();
    }

    /**
     * Returns `true` if the option contains no value.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\None();
     *
     * assert(false === $x->isNone());
     * assert(true  === $y->isNone());
     * ```
     */
    public function isNone(): bool
    {
        return null === $this->_value;
    }

    /**
     * Unwraps the option, yielding its content if there is some value.
     *
     * If there is no value, then throw a `RuntimeException` exception with a
     * specific message.
     *
     * # Examples
     *
     * There is some value (`42`), so `expect` unwraps successfully:
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     *
     * assert($x->expect('damn!') === 42);
     * ```
     *
     * There is no value, so a `RuntimeException` is thrown:
     *
     * ```php,must_throw(RuntimeException)
     * $x = Hoa\Option\None();
     *
     * assert($x->expect('damn!') === 42);
     * ```
     */
    public function expect(string $errorMessage)
    {
        if (true === $this->isNone()) {
            throw new RuntimeException($errorMessage);
        }

        return $this->_value;
    }

    /**
     * Unwraps the option, yielding its content if there is some value.
     *
     * If there is no value, then throw a `RuntimeException` exception with a
     * default message.
     *
     * In general, because of the unexpected exception, its use is
     * discouraged. Prefer to use either: `Hoa\Option\Option::expect`,
     * `Hoa\Option\Option::unwrapOr`, `Hoa\Option\Option::isSome`, or
     * `Hoa\Option\Option::isNone`.
     *
     * # Examples
     *
     * There is some value (`42`), so `unwrap` is successful:
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     *
     * assert($x->unwrap() === 42);
     * ```
     *
     * There is no value, so a `RuntimeException` is thrown:
     *
     * ```php,must_throw(RuntimeException)
     * $x = Hoa\Option\None();
     *
     * assert($x->unwrap() === 42);
     * ```
     */
    public function unwrap()
    {
        return $this->expect('Called `' . __METHOD__ . '` on a none value.');
    }

    /**
     * Unwraps the option, yielding its content if there is some value, or a
     * default value else.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\None();
     *
     * assert($x->unwrapOr(153) === 42);
     * assert($y->unwrapOr(153) === 153);
     * ```
     */
    public function unwrapOr($defaultValue)
    {
        if (true === $this->isNone()) {
            return $defaultValue;
        }

        return $this->_value;
    }

    /**
     * Unwraps the option, yielding its content if there is some value, or
     * compute a default value from a callable.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\None();
     *
     * $else = function () { return 153; };
     *
     * assert($x->unwrapOrElse($else) === 42);
     * assert($y->unwrapOrElse($else) === 153);
     * ```
     */
    public function unwrapOrElse(callable $defaultValueGenerator)
    {
        if (true === $this->isNone()) {
            return $defaultValueGenerator();
        }

        return $this->_value;
    }

    /**
     * Maps an option to another option by applying a callable to the value if
     * some.
     *
     * # Examples
     *
     * ```php
     * $maybeMessage       = Hoa\Option\Some('Hello, World!');
     * $maybeMessageLength = $maybeMessage->map(
     *     function (string $message): int {
     *         return strlen($message);
     *     }
     * );
     *
     * assert($maybeMessageLength->unwrap() === 13);
     * ```
     */
    public function map(callable $mapper): self
    {
        if (true === $this->isNone()) {
            return self::none();
        }

        return new self($mapper($this->_value));
    }

    /**
     * Maps an option to another option by applying a callable to the value if
     * some, or use a default value else.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some('Hello, World!');
     * $y = Hoa\Option\None();
     *
     * assert($x->mapOr('strlen', 42)->unwrap() === 13);
     * assert($y->mapOr('strlen', 42)->unwrap() === 42);
     * ```
     */
    public function mapOr(callable $mapper, $defaultValue): self
    {
        if (true === $this->isNone()) {
            $value = $defaultValue;
        } else {
            $value = $mapper($this->_value);
        }

        return new self($value);
    }

    /**
     * Maps an option to another option by applying a callable to the value if
     * some, or compute a default value from a callable.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some('Hello, World!');
     * $y = Hoa\Option\None();
     *
     * $else = function () { return 42; };
     *
     * assert($x->mapOrElse('strlen', $else)->unwrap() === 13);
     * assert($y->mapOrElse('strlen', $else)->unwrap() === 42);
     * ```
     */
    public function mapOrElse(callable $mapper, callable $defaultValueGenerator): self
    {
        if (true === $this->isNone()) {
            $value = $defaultValueGenerator();
        } else {
            $value = $mapper($this->_value);
        }

        return new self($value);
    }

    /**
     * Returns a none option if the option has no value, otherwise returns the
     * `$rightOption`.
     *
     * # Examples
     *
     * The `$x` option contains some value, so it returns respectively `$y`
     * and `$z`:
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\Some(42);
     * $z = Hoa\Option\None();
     *
     * assert($x->and($y) === $y);
     * assert($x->and($z) === $z);
     * ```
     *
     * The `$x` option contains no value, so it returns a new option with no value.
     *
     * ```php
     * $x = Hoa\Option\None();
     * $y = Hoa\Option\Some(42);
     *
     * assert($x->and($y)->isNone());
     * ```
     */
    public function and(self $rightOption): self
    {
        if (true === $this->isNone()) {
            return self::none();
        }

        return $rightOption;
    }

    /**
     * Returns a none option if the option has no value, otherwise returns a
     * new option computed by a callable.
     *
     * Some languages call this operation `flatmap`.
     *
     * # Examples
     *
     * ```php
     * $x      = Hoa\Option\Some(2);
     * $square = function (int $x): Hoa\Option\Option {
     *     return Hoa\Option\Some($x * $x);
     * };
     * $nop = function(): Hoa\Option\Option {
     *     return Hoa\Option\None();
     * };
     *
     * assert($x->andThen($square)->andThen($square)->unwrap() === 16);
     * assert($x->andThen($nop)->andThen($square)->isNone());
     * ```
     */
    public function andThen(callable $then): self
    {
        if (true === $this->isNone()) {
            return self::none();
        }

        return $then($this->_value);
    }

    /**
     * Returns the option if it has some value, otherwise returns the
     * `$rightOption`.
     *
     * # Examples
     *
     * ```php
     * $x = Hoa\Option\Some(42);
     * $y = Hoa\Option\Some(153);
     * $z = Hoa\Option\None();
     *
     * assert($x->or($y) === $x);
     * assert($z->or($y) === $y);
     * ```
     */
    public function or(self $rightOption): self
    {
        if (true === $this->isNone()) {
            return $rightOption;
        }

        return $this;
    }

    /**
     * Returns the option if it has some value, otherwise returns a new option
     * computed by a callable.
     *
     * # Examples
     *
     * ```php
     * $x      = Hoa\Option\None();
     * $y      = Hoa\Option\Some('me');
     * $nobody = function (): Hoa\Option\Option {
     *     return Hoa\Option\None();
     * };
     * $somebody = function(): Hoa\Option\Option {
     *     return Hoa\Option\Some('somebody');
     * };
     *
     * assert($x->orElse($somebody)->unwrap() === 'somebody');
     * assert($y->orElse($somebody) === $y);
     * assert($y->orElse($nobody)   === $y);
     * ```
     */
    public function orElse(callable $defaultValueGenerator): self
    {
        if (true === $this->isNone()) {
            return $defaultValueGenerator();
        }

        return $this;
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity(Option::class);
