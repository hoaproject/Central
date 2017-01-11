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

namespace Hoa\Mail\Test\Unit\Content\Encoder;

use Hoa\Mail\Content\Encoder\QuotedPrintable as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Mail\Test\Unit\Content\Encoder\QuotedPrintable.
 *
 * Test suite of the quoted-printable encoder.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class QuotedPrintable extends Test\Unit\Suite
{
    public function case_encode_rfc2045_section_6_7_rule1()
    {
        $this
            ->given($datum = 'abc')
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo('abc')

            // 8bits.
            ->given($datum = 'fôöbàr')
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo('f=C3=B4=C3=B6b=C3=A0r')

            ->given($datum = '😄!')
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo('=F0=9F=98=84!')

            ->given($datum = 'abc' . CRLF . 'def')
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo('abc' . CRLF . 'def');
    }

    public function case_encode_rfc2045_section6_7_rule2()
    {
        $this
            ->given(
                $notEncoded = array_map(
                    'chr',
                    array_merge(
                        range(33, 60),
                        range(62, 126)
                    )
                )
            )
            ->when(function () use ($notEncoded) {
                foreach ($notEncoded as $char) {
                    $this
                        ->string($char)
                            ->isEqualTo(SUT::encode($char));
                }
            });
    }

    public function case_encode_rfc2045_section6_7_rule3()
    {
        $this
            ->given($tab = 'abc' . "\t\t" . 'def')
            ->when($result = SUT::encode($tab))
            ->then
                ->string($result)
                    ->isEqualTo('abc' . "\t\t" . 'def')

            ->given($tab = 'abc' . "\t\t" . CRLF)
            ->when($result = SUT::encode($tab))
            ->then
                ->string($result)
                    ->isEqualTo('abc' . "\t" . '=09' . CRLF)

            ->given($space = 'abc  def')
            ->when($result = SUT::encode($space))
            ->then
                ->string($result)
                    ->isEqualTo('abc  def')

            ->given($space = 'abc  ' . CRLF)
            ->when($result = SUT::encode($space))
            ->then
                ->string($result)
                    ->isEqualTo('abc =20' . CRLF);
    }

    public function case_encode_rfc2045_section_6_7_rule4()
    {
        $this
            ->given($datum = 'abc' . CRLF . 'def' . CRLF . 'ghi' . CRLF)
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo($datum);
    }

    public function case_encode_rfc2045_section_6_7_rule5()
    {
        $this
            ->given($datum = 'abc')
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo('abc')

            ->given(
                $datum =
                    'abc def ghi jkl mno pqr stu vwx yz ' .
                    'abc def ghi jkl mno pqr stu vwx yz ' .
                    'abc def ghi jkl mno pqr stu vwx yz' . CRLF
            )
            ->when($result = SUT::encode($datum))
            ->then
                ->string($result)
                    ->isEqualTo(
                        'abc def ghi jkl mno pqr stu vwx yz abc def ghi jkl mno pqr stu vwx yz abc =' . CRLF .
                        'def ghi jkl mno pqr stu vwx yz' . CRLF
                    );
    }

    public function case_encode_rfc2047_sections_4_and_5()
    {
        $this
            ->given(
                $decoded = '😄!',
                $encoded = '=?utf-8?Q?=F0=9F=98=84!?='
            )
            ->when($result = SUT::encode($decoded, true))
            ->then
                ->string($result)
                    ->isEqualTo($encoded);
    }
}
