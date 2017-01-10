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

namespace Hoa\Fastcgi;

/**
 * Class \Hoa\Fastcgi\Connection.
 *
 * A FastCGI connection; mainly pack & unpack methods.
 * Specification can be found here:
 * http://fastcgi.com/devkit/doc/fcgi-spec.html.
 * Inspired by PHP SAPI code: php://sapi/cgi/fastcgi.*.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Connection
{
    /**
     * Header: version.
     *
     * @const int
     */
    const HEADER_VERSION        = 0;

    /**
     * Header: type.
     *
     * @const int
     */
    const HEADER_TYPE           = 1;

    /**
     * Header: request ID.
     *
     * @const int
     */
    const HEADER_REQUEST_ID     = 2;

    /**
     * Header: content length.
     *
     * @const int
     */
    const HEADER_CONTENT_LENGTH = 3;

    /**
     * Header: padding length.
     *
     * @const int
     */
    const HEADER_PADDING_LENGTH = 4;

    /**
     * Header: reserved.
     *
     * @const int
     */
    const HEADER_RESERVED       = 5;

    /**
     * Header: content.
     *
     * @const int
     */
    const HEADER_CONTENT        = 6;



    /**
     * Pack data to a packet.
     *
     * @param   int     $type       Packet's type.
     * @param   string  $content    Content.
     * @param   id      $id         Packet's ID.
     * @return  string
     */
    public function pack($type, $content, $id = 1)
    {
        $length = strlen($content);

        return
            chr(1) .                     // version
            chr($type) .                 // type
            chr(($id     >> 8) & 0xff) . // ID B1
            chr($id            & 0xff) . // ID B0
            chr(($length >> 8) & 0xff) . // length B1
            chr($length        & 0xff) . // length b0
            chr(0) .                     // padding length
            chr(0) .                     // reserved
            $content;
    }

    /**
     * Pack pairs (key/value).
     *
     * @param   array  $pairs    Keys/values array.
     * @return  string
     */
    public function packPairs(array $pairs)
    {
        $out = null;

        foreach ($pairs as $key => $value) {
            foreach ([$key, $value] as $handle) {
                $length = strlen($handle);

                // B0
                if ($length < 0x80) {
                    $out .= chr($length);
                }
                // B3 & B2 & B1 & B0
                else {
                    $out .=
                        chr(($length >> 24) | 0x80) .
                        chr(($length >> 16) & 0xff) .
                        chr(($length >>  8) & 0xff) .
                        chr($length         & 0xff);
                }
            }

            $out .= $key . $value;
        }

        return $out;
    }

    /**
     * Unpack pairs (key/value).
     *
     * @param   string  $pack    Packet to unpack.
     * @return  string
     */
    public function unpackPairs($pack)
    {
        if (null === $length) {
            $length = strlen($pack);
        }

        $out = [];
        $i   = 0;

        for ($i = 0; $length >= $i; $i += $keyLength + $valueLength) {
            $keyLength = ord($pack[$i++]);

            if ($keyLength >= 0x80) {
                $keyLength =
                    ($keyLength & 0x7f << 24)
                  | (ord($pack[$i++])  << 16)
                  | (ord($pack[$i++])  <<  8)
                  |  ord($pack[$i++]);
            }

            $valueLength = ord($pack[$i++]);

            if ($valueLength >= 0x80) {
                $valueLength =
                    ($valueLength & 0x7f << 24)
                  | (ord($pack[$i++])    << 16)
                  | (ord($pack[$i++])    <<  8)
                  |  ord($pack[$i++]);
            }

            $out[substr($pack, $i, $keyLength)]
                = substr($pack, $i + $keyLength, $valueLength);
        }

        return $out;
    }

    /**
     * Read a packet.
     *
     * @return  array
     */
    protected function readPack()
    {
        if ((null === $pack = $this->read(8)) ||
            empty($pack)) {
            return false;
        }

        $headers = [
            self::HEADER_VERSION        => ord($pack[0]),
            self::HEADER_TYPE           => ord($pack[1]),
            self::HEADER_REQUEST_ID     => (ord($pack[2]) << 8) +
                                            ord($pack[3]),
            self::HEADER_CONTENT_LENGTH => (ord($pack[4]) << 8) +
                                            ord($pack[5]),
            self::HEADER_PADDING_LENGTH => ord($pack[6]),
            self::HEADER_RESERVED       => ord($pack[7]),
            self::HEADER_CONTENT        => null
        ];
        $length =
            $headers[self::HEADER_CONTENT_LENGTH] +
            $headers[self::HEADER_PADDING_LENGTH];

        if (0 === $length) {
            return $headers;
        }

        $headers[self::HEADER_CONTENT] = substr(
            $this->read($length),
            0,
            $headers[self::HEADER_CONTENT_LENGTH]
        );

        return $headers;
    }

    /**
     * Read data.
     *
     * @param   int     $length    Length of data to read.
     * @return  string
     */
    abstract protected function read($length);
}
