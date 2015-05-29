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

namespace Hoa\Http;

/**
 * Class \Hoa\Http\Runtime.
 *
 * Runtime informations.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Runtime
{
    /**
     * Get HTTP method.
     *
     * @return  string
     */
    public static function getMethod()
    {
        if ('cli' === php_sapi_name()) {
            return 'get';
        }

        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get URI.
     *
     * @return  string
     */
    public static function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get data.
     *
     * @param   bool  $extended    Whether we want a larger support of
     *                             content-type for example.
     * @return  mixed
     */
    public static function getData($extended = true)
    {
        switch (static::getMethod()) {
            case Request::METHOD_GET:
                return $_GET;

            case Request::METHOD_POST:
                $contentType = static::getHeader('Content-Type');

                switch ($contentType) {
                    case 'application/x-www-form-urlencoded':
                        return $_POST;

                    case 'application/json':
                        $input = file_get_contents('php://input');

                        if (true !== $extended ||
                            true !== function_exists('json_decode')) {
                            return $input;
                        }

                        $json = json_decode($input, true);

                        if (JSON_ERROR_NONE !== json_last_error()) {
                            return $input;
                        }

                        return $json;

                    default:
                        return file_get_contents('php://input');
                }

                break;

            case Request::METHOD_PUT:
            case Request::METHOD_PATCH:
                return file_get_contents('php://input');

            default:
                return null;
        }
    }

    /**
     * Whether there is data or not.
     *
     * @return  bool
     */
    public static function hasData()
    {
        if (Request::METHOD_GET === static::getMethod()) {
            return !empty($_GET);
        }

        return 0 < intval(static::getHeader('Content-Length'));
    }

    /**
     * Get all headers.
     *
     * @return  array
     */
    public static function getHeaders()
    {
        static $_headers = [];

        if (!empty($_headers)) {
            return $_headers;
        }

        if (true === function_exists('apache_request_headers')) {
            foreach (apache_request_headers() as $header => $value) {
                $_headers[strtolower($header)] = $value;
            }
        } else {
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $_headers['content-type'] = $_SERVER['CONTENT_TYPE'];
            }

            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $_headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
            }

            foreach ($_SERVER as $key => $value) {
                if ('HTTP_' === substr($key, 0, 5)) {
                    $_headers[strtolower(str_replace('_', '-', substr($key, 5)))]
                        = $value;
                }
            }
        }

        return $_headers;
    }

    /**
     * Get a specific header.
     *
     * @param   string  $header    Header name.
     * @return  string
     */
    public static function getHeader($header)
    {
        $headers = static::getHeaders();
        $header  = strtolower($header);

        if (true !== array_key_exists($header, $headers)) {
            return null;
        }

        return $headers[$header];
    }
}
