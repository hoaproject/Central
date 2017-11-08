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

namespace Hoa\Socket;

/**
 * Class \Hoa\Socket\Transport.
 *
 * Transports manipulation. Can be used to register new transports. A URI is of
 * kind `scheme://uri`. A callable is associated to a `scheme` and represents a
 * factory building valid `Hoa\Socket\Socket` instances (so with `tcp://` or
 * `udp://` “native” schemes).
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Transport
{
    /**
     * Additionnal transports (scheme to callable).
     *
     * @var array
     */
    protected static $_transports = [];



    /**
     * Get all enabled transports.
     *
     * @return  array
     */
    public static function get()
    {
        return array_merge(
            stream_get_transports(),
            array_keys(static::$_transports)
        );
    }

    /**
     * Check if a transport exists.
     *
     * @param   string  $transport    Transport to check.
     * @return  bool
     */
    public static function exists($transport)
    {
        return in_array(strtolower($transport), static::get());
    }

    /**
     * Register a new transport.
     * Note: It is possible to override a standard transport.
     *
     * @param  string    $transport    Transport name.
     * @param  callable  $factory      Associated factory to build a valid
     *                                 `Hoa\Socket\Socket` object.
     * @return void
     */
    public static function register($transport, callable $factory)
    {
        static::$_transports[$transport] = $factory;

        return;
    }

    /**
     * Get the factory associated to a specific transport.
     *
     * @param  string  $transport    Transport.
     * @return callable
     */
    public static function getFactory($transport)
    {
        if (false === static::exists($transport) ||
            !isset(static::$_transports[$transport])) {
            return function ($socketUri) {
                return new Socket($socketUri);
            };
        }

        return static::$_transports[$transport];
    }
}
