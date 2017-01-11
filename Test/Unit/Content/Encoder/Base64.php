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

use Hoa\Mail\Content\Encoder\Base64 as SUT;
use Hoa\Test;

/**
 * Class \Hoa\Mail\Test\Unit\Content\Encoder\Base64.
 *
 * Test suite of the base64 encoder.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Base64 extends Test\Unit\Suite
{
    public function case_basic_encode()
    {
        $this
            ->given(
                $decoded = 'foobar',
                $encoded = 'Zm9vYmFy'
            )
            ->when($result = SUT::encode($decoded))
            ->then
                ->string($result)
                    ->isEqualTo($encoded);
    }

    public function case_long_encode()
    {
        $this
            ->given(
                $decoded = str_repeat('foobar', 15),
                $encoded =
                    'Zm9vYmFyZm9vYmFyZm9vYmFyZm9vYmFyZm9vYmFyZm9vYmFyZm9v' .
                    'YmFyZm9vYmFyZm9vYmFyZm9v' . CRLF .
                    'YmFyZm9vYmFyZm9vYmFyZm9vYmFyZm9vYmFyZm9vYmFy'
            )
            ->when($result = SUT::encode($decoded))
            ->then
                ->string($result)
                    ->isEqualTo($encoded);
    }

    public function case_encode_rfc2047_sections_4_and_5()
    {
        $this
            ->given(
                $decoded = 'foobar',
                $encoded = '=?utf-8?B?Zm9vYmFy?='
            )
            ->when($result = SUT::encode($decoded, true))
            ->then
                ->string($result)
                    ->isEqualTo($encoded);
    }
}
