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
 * Class \Hoa\Http\Request.
 *
 * HTTP request support.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Request extends Http
{
    /**
     * Method: CONNECT.
     *
     * @const string
     */
    const METHOD_CONNECT  = 'connect';

    /**
     * Method: DELETE.
     *
     * @const string
     */
    const METHOD_DELETE   = 'delete';

    /**
     * Method: GET.
     *
     * @const string
     */
    const METHOD_GET      = 'get';

    /**
     * Method: HEAD.
     *
     * @const string
     */
    const METHOD_HEAD     = 'head';

    /**
     * Method: OPTIONS.
     *
     * @const string
     */
    const METHOD_OPTIONS  = 'options';

    /**
     * Method: PATCH.
     *
     * @const string
     */
    const METHOD_PATCH    = 'patch';

    /**
     * Method: POST.
     *
     * @const string
     */
    const METHOD_POST     = 'post';

    /**
     * Method: PUT.
     *
     * @const string
     */
    const METHOD_PUT      = 'put';

    /**
     * Method: TRACE.
     *
     * @const string
     */
    const METHOD_TRACE    = 'trace';

    /**
     * Other methods.
     *
     * @const string
     */
    const METHOD_EXTENDED = 'extended';

    /**
     * Request method (please, see self::METHOD_* constants).
     *
     * @var string
     */
    protected $_method = null;

    /**
     * Request URL.
     *
     * @var string
     */
    protected $_url    = null;



    /**
     * Parse a HTTP packet.
     *
     * @param   string  $packet    HTTP packet.
     * @return  void
     * @throws  \Hoa\Http\Exception
     */
    public function parse($packet)
    {
        $headers = explode("\r\n", $packet);
        $http    = array_shift($headers);
        $this->setBody(null);

        foreach ($headers as $i => $header) {
            if ('' == trim($header)) {
                unset($headers[$i]);
                $this->setBody(
                    trim(
                        implode("\r\n", array_splice($headers, $i))
                    )
                );

                break;
            }
        }

        if (0 === preg_match('#^([^\s]+)\s+([^\s]+)\s+HTTP/(1\.(?:0|1))$#i', $http, $matches)) {
            throw new Exception(
                'HTTP headers are not well-formed: %s',
                0,
                $http
            );
        }

        switch ($method = strtolower($matches[1])) {
            case self::METHOD_CONNECT:
            case self::METHOD_DELETE:
            case self::METHOD_GET:
            case self::METHOD_HEAD:
            case self::METHOD_OPTIONS:
            case self::METHOD_PATCH:
            case self::METHOD_POST:
            case self::METHOD_PUT:
            case self::METHOD_TRACE:
                $this->_method = $method;

                break;

            default:
                $this->_method = self::METHOD_EXTENDED;
        }

        $this->setUrl($matches[2]);
        $this->setHttpVersion((float) $matches[3]);

        $this->_parse($headers);

        return;
    }

    /**
     * Set request method.
     *
     * @param   string  $method    Method (please, see self::METHOD_*
     *                             constants).
     * @return  string
     */
    public function setMethod($method)
    {
        $old           = $this->_method;
        $this->_method = $method;

        return $old;
    }

    /**
     * Get request method.
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set request URL.
     *
     * @param   string  $url    URL.
     * @return  string
     */
    public function setUrl($url)
    {
        $old        = $this->_url;
        $this->_url = $url;

        return $old;
    }

    /**
     * Get request URL.
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Dump (parse^-1).
     *
     * @return  string
     */
    public function __toString()
    {
        return
            strtoupper($this->getMethod()) . ' ' .
            $this->getUrl() . ' ' .
            'HTTP/' . $this->getHttpVersion() . CRLF .
            parent::__toString() . CRLF;
    }
}
