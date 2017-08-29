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

namespace Hoa\Option\Test\Integration;

use Hoa\Option\Option;
use Hoa\Test;
use RuntimeException;
use function Hoa\Option\None;
use function Hoa\Option\Some;

/**
 * Class \Hoa\Option\Test\Integration\Documentation.
 *
 * Test suite of the documentation.
 *
 * @license    New BSD License
 */
class Documentation extends Test\Integration\Suite implements Test\Decorrelated
{
    public function case_readme_0(): void
    {
        $this
            ->given(
                $x = Some(42),
                $y = None()
            )
            ->when($result = $x->isSome())
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $y->isNone())
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function case_readme_1(): void
    {
        $this
            ->when($result = Some(42)->unwrap())
            ->then
                ->integer($result)
                    ->isEqualTo(42)

            ->exception(function (): void {
                None()->unwrap();
            })
                ->isInstanceOf(RuntimeException::class);
    }

    public function case_readme_2(): void
    {
        $this
            ->given($x = None())
            ->exception(function () use ($x): void {
                $x->expect('Damn…');
            })
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage('Damn…');
    }

    public function case_readme_3(): void
    {
        $this
            ->given(
                $x = Some(42),
                $y = None()
            )
            ->when($result = $x->unwrapOr(153))
            ->then
                ->integer($result)
                    ->isEqualTo(42)

            ->when($result = $y->unwrapOr(153))
            ->then
                ->integer($result)
                    ->isEqualTo(153);
    }

    public function case_readme_4(): void
    {
        $this
            ->given(
                $x    = Some(42),
                $y    = None(),
                $else = function (): int {
                    return 153;
                }
            )
            ->when($result = $x->unwrapOrElse($else))
            ->then
                ->integer($result)
                    ->isEqualTo(42)

            ->when($result = $y->unwrapOrElse($else))
            ->then
                ->integer($result)
                    ->isEqualTo(153);
    }

    public function case_readme_5(): void
    {
        $this
            ->given(
                $x = Some('Hello, World!'),
                $y = None()
            )
            ->when($result = $x->map('strlen'))
            ->then
                ->object($result)
                    ->isEqualTo(Some(13))

            ->when($result = $y->map('strlen'))
            ->then
                ->object($result)
                    ->isEqualTo(None());
    }

    public function case_readme_6(): void
    {
        $this
            ->given($x = None())
            ->when($result = $x->mapOr('strlen', 0))
            ->then
                ->object($result)
                    ->isEqualTo(Some(0));
    }

    public function case_readme_7(): void
    {
        $this
            ->given(
                $x = None(),
                $else = function (): int {
                    return 0;
                }
            )
            ->when($result = $x->mapOrElse('strlen', $else))
            ->then
                ->object($result)
                    ->isEqualTo(Some(0));
    }

    public function case_readme_8(): void
    {
        $this
            ->when($result = Some(42)->and(Some(153)))
            ->then
                ->object($result)
                    ->isEqualTo(Some(153))

            ->when($result = Some(42)->and(None()))
            ->then
                ->object($result)
                    ->isEqualTo(None())

            ->when($result = None()->and(Some(153)))
            ->then
                ->object($result)
                    ->isEqualTo(None());
    }

    public function case_readme_9(): void
    {
        $this
            ->given(
                $square = function (int $x): Option {
                    return Some($x * $x);
                },
                $nop = function (): Option {
                    return None();
                }
            )
            ->when($result = Some(2)->andThen($square)->andThen($square))
            ->then
                ->object($result)
                    ->isEqualTo(Some(16))

            ->when($result = Some(2)->andThen($nop)->andThen($square))
            ->then
                ->object($result)
                    ->isEqualTo(None());
    }

    public function case_readme_10(): void
    {
        $this
            ->when($result = Some(42)->or(Some(153)))
            ->then
                ->object($result)
                    ->isEqualTo(Some(42))

            ->when($result = None()->or(Some(153)))
            ->then
                ->object($result)
                    ->isEqualTo(Some(153));
    }

    public function case_readme_11(): void
    {
        $this
            ->given(
                $somebody = function (): Option {
                    return Some('somebody');
                }
            )
            ->when($result = None()->orElse($somebody))
            ->then
                ->object($result)
                    ->isEqualTo(Some('somebody'));
    }
}
