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

use Hoa\Core;

/**
 * Class \Hoa\Http.
 *
 * Generic class to manage HTTP headers (parse, set, get) only.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
abstract class Http implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Whether PHP is running with FastCGI or not.
     *
     * @var bool
     */
    protected static $_fcgi = null;

    /**
     * Request HTTP version.
     *
     * @var float
     */
    protected $_httpVersion = 1.1;

    /**
     * Headers (not sent).
     *
     * @var array
     */
    protected $_headers     = [];

    /**
     * Request body.
     *
     * @var string
     */
    protected $_body        = null;



    /**
     * Constructor.
     *
     * @return  void
     */
    public function __construct()
    {
        if (null === self::$_fcgi) {
            self::$_fcgi = 'cgi-fcgi' === PHP_SAPI;
        }

        return;
    }

    /**
     * Set request HTTP version.
     *
     * @param   float  $version    HTTP version.
     * @return  float
     */
    public function setHttpVersion($version)
    {
        $old                = $this->_httpVersion;
        $this->_httpVersion = $version;

        return $old;
    }

    /**
     * Get request HTTP version.
     *
     * @return  float
     */
    public function getHttpVersion()
    {
        return $this->_httpVersion;
    }

    /**
     * Parse a HTTP packet.
     *
     * @param   string  $packet    HTTP packet.
     * @return  void
     * @throws  \Hoa\Http\Exception
     */
    abstract public function parse($packet);

    /**
     * Helper to parse HTTP headers and distribute them in array accesses.
     *
     * @param   array  $hedaers    Headers to parse and distribute.
     * @return  array
     */
    protected function _parse(Array $headers)
    {
        unset($this->_headers);
        $this->_headers = [];

        foreach ($headers as $header) {
            list($name, $value)                = explode(':', $header, 2);
            $this->_headers[strtolower($name)] = trim($value);
        }

        return $this->_headers;
    }

    /**
     * Get headers.
     *
     * @return  array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get headers (formatted).
     *
     * @return  array
     */
    public function getHeadersFormatted()
    {
        $out = [];

        foreach ($this->getHeaders() as $header => $value) {
            if ('x-' == strtolower(substr($header, 0, 2))) {
                $header = 'http_' . $header;
            }

            $out[strtoupper(str_replace('-', '_', $header))] = $value;
        }

        return $out;
    }

    /**
     * Check if header exists.
     *
     * @param   string  $offset    Header.
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_headers);
    }

    /**
     * Get a header's value.
     *
     * @param   string  $offset    Header.
     * @return  string
     */
    public function offsetGet($offset)
    {
        if (false === $this->offsetExists($offset)) {
            return null;
        }

        return $this->_headers[$offset];
    }

    /**
     * Set a value to a header.
     *
     * @param   string  $offset    Header.
     * @param   string  $value     Value.
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        $this->_headers[$offset] = $value;

        return;
    }

    /**
     * Unset a header.
     *
     * @param   string  $offset    Header.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        unset($this->_headers[$offset]);

        return;
    }

    /**
     * Get iterator.
     *
     * @return  \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getHeaders());
    }

    /**
     * Count number of headers.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->getHeaders());
    }

    /**
     * Set request body.
     *
     * @param   string  $body   Body.
     * @return  string
     */
    public function setBody($body)
    {
        $old         = $this->_body;
        $this->_body = $body;

        return $old;
    }

    /**
     * Get request body.
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Dump (parse^-1).
     *
     * @return  string
     */
    public function __toString()
    {
        $out = null;

        foreach ($this->getHeaders() as $key => $value) {
            $out .= $key . ': ' . $value . CRLF;
        }

        return $out;
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Http\Http');
