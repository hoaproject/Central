<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

/**
 * Class \Hoa\Mail\Content\Encoder\QuotedPrintable.
 *
 * Encode and decode a string as described in the RFC2045 Section 6.7 and
 * RFC2047, Sections 4 and 5.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class QuotedPrintable implements Encoder
{
    /**
     *
     * @param   string  $string    String to encode.
     * @return  string
     */
    public static function encode($string)
    {
        if (0 === preg_match('#[\x80-\xff]+#', $string)) {
            return $string;
        }

        // RFC2045, Section 6.7, rule 1.
        return
            '=?utf-8?Q?' .
            preg_replace_callback(
                '#[\x80-\xff]+#',
                function ($matches) {
                    $substring = $matches[0];
                    $out       = null;

                    for ($i = 0, $max = strlen($substring); $i < $max; ++$i) {
                        $out .= '=' . strtoupper(dechex(ord($substring[$i])));
                    }

                    return strtoupper($out);
                },
                $string
            ) .
            '?=';
    }

    public static function decode($string)
    {
    }
}
