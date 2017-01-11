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

namespace Hoa\Mail\Content\Encoder;

use Hoa\Mail;

/**
 * Class \Hoa\Mail\Content\Encoder\Base64.
 *
 * Encode and decode a string as described in the RFC4648, RFC2045
 * Section 6.8 and RFC2047.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Base64 implements Encoder
{
    /**
     * Encode into base64.
     *
     * @param   string  $string           String to encode.
     * @param   bool    $isHeaderValue    Whether the string is a header value.
     * @return  string
     */
    public static function encode($string, $isHeaderValue = false)
    {
        $pre  = null;
        $post = null;

        if (true === $isHeaderValue) {
            $pre  = '=?utf-8?B?';
            $post = '?=';
        }

        return
            $pre .
            trim(
                chunk_split(
                    base64_encode($string),
                    76,
                    CRLF
                )
            ) .
            $post;
    }

    /**
     * Decode from base64.
     *
     * @param   string  $string           String to decode.
     * @param   bool    $isHeaderValue    Whether the string is a header value.
     * @return  string
     */
    public static function decode($string, $isHeaderValue = false)
    {
        throw new Mail\Exception('Not implemented.', 0);
    }
}
