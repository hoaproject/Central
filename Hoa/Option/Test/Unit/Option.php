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

namespace Hoa\Option\Test\Unit;

use Hoa\Option\Option as SUT;
use Hoa\Test;
use RuntimeException;
use function Hoa\Option\None;
use function Hoa\Option\Some;

/**
 * Class \Hoa\Option\Test\Unit\Option.
 *
 * Test suite of the option class.
 *
 * @license    New BSD License
 */
class Option extends Test\Unit\Suite
{
    public function case_function_some(): void
    {
        $this
            ->when($result = Some(42))
            ->then
                ->object($result)
                    ->isInstanceOf(SUT::class)
                ->boolean($result->isSome())
                    ->isTrue()
                ->integer($result->unwrap())
                    ->isEqualTo(42);
    }

    public function case_function_none(): void
    {
        $this
            ->when($result = None())
            ->then
                ->object($result)
                    ->isInstanceOf(SUT::class)
                ->boolean($result->isNone())
                    ->isTrue()
                ->integer($result->unwrapOr(42))
                    ->isEqualTo(42);
    }

    public function case_some(): void
    {
        $this
            ->when($result = Some(42))
            ->then
                ->boolean($result->isSome())
                    ->isTrue()
                ->integer($result->unwrap())
                    ->isEqualTo(42);
    }

    public function case_some_null(): void
    {
        $this
            ->exception(function (): void {
                Some(null);
            })
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage(
                    'Called `' . SUT::class . '::some` with a `null` value, forbidden. ' .
                    'Use `' . SUT::class . '::none` instead.'
                );
    }

    public function case_none(): void
    {
        $this
            ->when($result = None())
            ->then
                ->boolean($result->isNone())
                    ->isTrue()
                ->integer($result->unwrapOr(42))
                    ->isEqualTo(42);
    }

    public function case_some_is_some(): void
    {
        $this
            ->given($option = Some(42))
            ->when($result = $option->isSome())
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_none_is_some(): void
    {
        $this
            ->given($option = None())
            ->when($result = $option->isSome())
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_some_is_none(): void
    {
        $this
            ->given($option = Some(42))
            ->when($result = $option->isNone())
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function case_none_is_none(): void
    {
        $this
            ->given($option = None())
            ->when($result = $option->isNone())
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_some_expect(): void
    {
        $this
            ->given($option = Some(42))
            ->when($result = $option->expect('foo'))
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    public function case_none_expect(): void
    {
        $this
            ->given($option = None())
            ->exception(function () use ($option): void {
                $option->expect('foo');
            })
            ->isInstanceOf(RuntimeException::class)
                ->hasMessage('foo');
    }

    public function case_some_unwrap(): void
    {
        $this
            ->given($option = Some(42))
            ->when($result = $option->unwrap())
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    public function case_none_unwrap(): void
    {
        $this
            ->given($option = None())
            ->exception(function () use ($option): void {
                $option->unwrap();
            })
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage('Called `' . SUT::class . '::unwrap` on a none value.');
    }

    public function case_some_unwrap_or(): void
    {
        $this
            ->given($option = Some(42))
            ->when($result = $option->unwrapOr(153))
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    public function case_none_unwrap_or(): void
    {
        $this
            ->given($option = None())
            ->when($result = $option->unwrapOr(153))
            ->then
                ->integer($result)
                    ->isEqualTo(153);
    }

    public function case_some_unwrap_or_else(): void
    {
        $this
            ->given(
                $option = Some(42),
                $else   = function (): int {
                    return 153;
                }
            )
            ->when($result = $option->unwrapOrElse($else))
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    public function case_none_unwrap_or_else(): void
    {
        $this
            ->given(
                $option = None(),
                $else   = function (): int {
                    return 153;
                }
            )
            ->when($result = $option->unwrapOrElse($else))
            ->then
                ->integer($result)
                    ->isEqualTo(153);
    }

    public function case_some_map(): void
    {
        $this
            ->given(
                $option = Some(42),
                $mapper = function (int $x): int {
                    return $x * 2;
                }
            )
            ->when($result = $option->map($mapper))
                ->object($result)
                    ->isInstanceOf(SUT::class)
                    ->isNotIdenticalTo($option)
                ->integer($result->unwrap())
                    ->isEqualTo(84);
    }

    public function case_none_map(): void
    {
        $this
            ->given(
                $option = None(),
                $mapper = function (int $x): int {
                    return $x * 2;
                }
            )
            ->when($result = $option->map($mapper))
                ->object($result)
                    ->isInstanceOf(SUT::class)
                    ->isNotIdenticalTo($option)
                ->integer($result->unwrapOr(153))
                    ->isEqualTo(153);
    }

    public function case_some_map_or(): void
    {
        $this
            ->given(
                $option = Some(42),
                $mapper = function (int $x): int {
                    return $x * 2;
                }
            )
            ->when($result = $option->mapOr($mapper, 153))
                ->object($result)
                    ->isInstanceOf(SUT::class)
                    ->isNotIdenticalTo($option)
                ->integer($result->unwrap())
                    ->isEqualTo(84);
    }

    public function case_none_map_or(): void
    {
        $this
            ->given(
                $option = None(),
                $mapper = function (int $x): int {
                    return $x * 2;
                }
            )
            ->when($result = $option->mapOr($mapper, 153))
                ->object($result)
                    ->isInstanceOf(SUT::class)
                    ->isNotIdenticalTo($option)
                ->integer($result->unwrap())
                    ->isEqualTo(153);
    }

    public function case_some_map_or_else(): void
    {
        $this
            ->given(
                $option = Some(42),
                $else   = function (): int {
                    return 153;
                },
                $mapper = function (int $x): int {
                    return $x * 2;
                }
            )
            ->when($result = $option->mapOrElse($mapper, $else))
                ->object($result)
                    ->isInstanceOf(SUT::class)
                    ->isNotIdenticalTo($option)
                ->integer($result->unwrap())
                    ->isEqualTo(84);
    }

    public function case_none_map_or_else(): void
    {
        $this
            ->given(
                $option = None(),
                $else   = function (): int {
                    return 153;
                },
                $mapper = function (int $x): int {
                    return $x * 2;
                }
            )
            ->when($result = $option->mapOrElse($mapper, $else))
                ->object($result)
                    ->isInstanceOf(SUT::class)
                    ->isNotIdenticalTo($option)
                ->integer($result->unwrap())
                    ->isEqualTo(153);
    }

    public function case_some_and_some(): void
    {
        $this
            ->given(
                $option      = Some(42),
                $rightOption = Some(153)
            )
            ->when($result = $option->and($rightOption))
            ->then
                ->object($result)
                    ->isIdenticalTo($rightOption);
    }

    public function case_some_and_none(): void
    {
        $this
            ->given(
                $option      = Some(42),
                $rightOption = None()
            )
            ->when($result = $option->and($rightOption))
            ->then
                ->object($result)
                    ->isIdenticalTo($rightOption);
    }

    public function case_none_and_some(): void
    {
        $this
            ->given(
                $option      = None(),
                $rightOption = Some(153)
            )
            ->when($result = $option->and($rightOption))
            ->then
                ->object($result)
                    ->isEqualTo($option)
                    ->isNotIdenticalTo($option);
    }

    public function case_none_and_none(): void
    {
        $this
            ->given(
                $option      = None(),
                $rightOption = None()
            )
            ->when($result = $option->and($rightOption))
            ->then
                ->object($result)
                    ->isEqualTo($option)
                    ->isNotIdenticalTo($option);
    }

    public function case_some_and_then_some(): void
    {
        $this
            ->given(
                $option = Some(42),
                $mapper = function (): SUT {
                    return Some(153);
                }
            )
            ->when($result = $option->andThen($mapper))
            ->then
                ->object($result)
                    ->isEqualTo(Some(153));
    }

    public function case_some_and_then_none(): void
    {
        $this
            ->given(
                $option = Some(42),
                $mapper = function (): SUT {
                    return None();
                }
            )
            ->when($result = $option->andThen($mapper))
            ->then
                ->object($result)
                    ->isEqualTo(None());
    }

    public function case_none_and_then_some(): void
    {
        $this
            ->given(
                $option = None(),
                $mapper = function (): SUT {
                    return Some(153);
                }
            )
            ->when($result = $option->andThen($mapper))
            ->then
                ->object($result)
                    ->isEqualTo(None())
                    ->isNotIdenticalTo($option);
    }

    public function case_none_and_then_none(): void
    {
        $this
            ->given(
                $option = None(),
                $mapper = function (): SUT {
                    return Some(153);
                }
            )
            ->when($result = $option->andThen($mapper))
            ->then
                ->object($result)
                    ->isEqualTo(None())
                    ->isNotIdenticalTo($option);
    }

    public function case_some_or_some(): void
    {
        $this
            ->given(
                $option      = Some(42),
                $rightOption = Some(153)
            )
            ->when($result = $option->or($rightOption))
            ->then
                ->object($result)
                    ->isIdenticalTo($option);
    }

    public function case_some_or_none(): void
    {
        $this
            ->given(
                $option      = Some(42),
                $rightOption = None()
            )
            ->when($result = $option->or($rightOption))
            ->then
                ->object($result)
                    ->isIdenticalTo($option);
    }

    public function case_none_or_some(): void
    {
        $this
            ->given(
                $option      = None(),
                $rightOption = Some(153)
            )
            ->when($result = $option->or($rightOption))
            ->then
                ->object($result)
                    ->isIdenticalTo($rightOption);
    }

    public function case_none_or_none(): void
    {
        $this
            ->given(
                $option      = None(),
                $rightOption = None()
            )
            ->when($result = $option->or($rightOption))
            ->then
                ->object($result)
                    ->isIdenticalTo($rightOption);
    }

    public function case_some_or_then_some(): void
    {
        $this
            ->given(
                $option = Some(42),
                $else   = function (): SUT {
                    return Some(153);
                }
            )
            ->when($result = $option->orElse($else))
            ->then
                ->object($result)
                    ->isIdenticalTo($option);
    }

    public function case_some_or_then_none(): void
    {
        $this
            ->given(
                $option = Some(42),
                $else   = function (): SUT {
                    return None();
                }
            )
            ->when($result = $option->orElse($else))
            ->then
                ->object($result)
                    ->isIdenticalTo($option);
    }

    public function case_none_or_then_some(): void
    {
        $this
            ->given(
                $option = None(),
                $else   = function (): SUT {
                    return Some(153);
                }
            )
            ->when($result = $option->orElse($else))
            ->then
                ->object($result)
                    ->isEqualTo(Some(153));
    }

    public function case_none_or_then_none(): void
    {
        $this
            ->given(
                $option = None(),
                $else   = function (): SUT {
                    return Some(153);
                }
            )
            ->when($result = $option->orElse($else))
            ->then
                ->object($result)
                    ->isEqualTo(Some(153));
    }
}
