<?php

declare(strict_types=1);

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

namespace Hoa\Http;

use Hoa\Consistency;

/**
 * Class \Hoa\Http.
 *
 * Generic class to manage HTTP headers (parse, set, get) only.
 */
abstract class Http implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Whether PHP is running with FastCGI or not.
     */
    protected static $_fcgi = null;

    /**
     * Request HTTP version.
     */
    protected $_httpVersion = 1.1;

    /**
     * Headers (not sent).
     */
    protected $_headers     = [];

    /**
     * Request body.
     */
    protected $_body        = null;



    /**
     * Constructor.
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
     */
    public function setHttpVersion(float $version): float
    {
        $old                = $this->_httpVersion;
        $this->_httpVersion = $version;

        return $old;
    }

    /**
     * Get request HTTP version.
     */
    public function getHttpVersion(): float
    {
        return $this->_httpVersion;
    }

    /**
     * Parse a HTTP packet.
     */
    abstract public function parse(string $packet): void;

    /**
     * Helper to parse HTTP headers and distribute them in array accesses.
     */
    protected function _parse(array $headers): array
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
     */
    public function getHeaders(): array
    {
        return $this->_headers;
    }

    /**
     * Get headers (formatted).
     */
    public function getHeadersFormatted(): array
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
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->_headers);
    }

    /**
     * Get a header's value.
     */
    public function offsetGet($offset): ?string
    {
        if (false === $this->offsetExists($offset)) {
            return null;
        }

        return $this->_headers[$offset];
    }

    /**
     * Set a value to a header.
     */
    public function offsetSet($offset, $value): void
    {
        $this->_headers[$offset] = $value;
    }

    /**
     * Unset a header.
     */
    public function offsetUnset($offset): void
    {
        unset($this->_headers[$offset]);
    }

    /**
     * Get iterator.
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getHeaders());
    }

    /**
     * Count number of headers.
     */
    public function count(): int
    {
        return count($this->getHeaders());
    }

    /**
     * Set request body.
     */
    public function setBody(?string $body): ?string
    {
        $old         = $this->_body;
        $this->_body = $body;

        return $old;
    }

    /**
     * Get request body.
     */
    public function getBody(): ?string
    {
        return $this->_body;
    }

    /**
     * Dump (parse^-1).
     */
    public function __toString(): string
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
Consistency::flexEntity(Http::class);
