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
 * Class \Hoa\Mail\Content\Encoder\QuotedPrintable.
 *
 * Encode and decode a string as described in the RFC2045 Section 6.7 and
 * RFC2047, Sections 4 and 5.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class QuotedPrintable implements Encoder
{
    /**
     * Encode into quoted-printable format.
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
            $pre  = '=?utf-8?Q?';
            $post = '?=';
        }

        // RFC2045, Section 6.7, rules 1 and 2.
        $string = preg_replace_callback(
            // 0x00 to 0xff minus:
            //   (from rule 1)
            //   * 0x0a,
            //   * 0x0d,
            //   (from rule 2)
            //   * 0x21 to 0x3c,
            //   * 0x3e to 0x7e,
            //   (from rule 3)
            //   * 0x09,
            //   * 0x20.
            '#[\x00-\x08\x0b\x0c\x0e-\x1f\x3d-\x3d\x7f-\xff]#',
            function ($matches) {
                $substring = $matches[0];
                $out       = null;

                for ($i = 0, $max = strlen($substring); $i < $max; ++$i) {
                    $out .= vsprintf('=%02X', ord($substring[$i]));
                }

                return $out;
            },
            $string
        );

        // RFC2045, Section 6.7, rule 3.
        $string = preg_replace_callback(
            '#([\x09\x20])' . CRLF . '#',
            function ($matches) {
                return vsprintf('=%02X', ord($matches[1])) . CRLF;
            },
            $string
        );

        // RFC2045, Section 6.7, rule 4.
        //     CRLF is not encoded.

        // RFC2045, Section 6.7, rule 5.
        $string = wordwrap(
            $string,
            75,
            ' =' . CRLF,
            false
        );

        return $pre . $string . $post;
    }

    /**
     * Decode from quoted-printable format.
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
