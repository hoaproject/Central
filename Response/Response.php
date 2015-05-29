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

namespace Hoa\Http\Response;

use Hoa\Core;
use Hoa\Http;
use Hoa\Stream;

/**
 * Class \Hoa\Http\Response.
 *
 * HTTP response support.
 *
 * @TODO Follow http://tools.ietf.org/html/draft-nottingham-http-new-status-03.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class          Response
    extends    Http
    implements Stream\IStream\Out,
               Stream\IStream\Bufferable
{
    /**
     * Continue (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_CONTINUE                          = '100 Continue';

    /**
     * Switching protocols (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_SWITCHING_PROTOCOLS               = '101 Switching Protocols';

    /**
     * Processing (please, see RFC2518).
     *
     * @const string
     */
    const STATUS_PROCESSING                        = '102 Processing';

    /**
     * OK (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_OK                                = '200 Ok';

    /**
     * Created (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_CREATED                           = '201 Created';

    /**
     * Accepted (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_ACCEPTED                          = '202 Accepted';

    /**
     * Non-authoritative information (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_NON_AUTHORITATIVE_INFORMATION     = '203 Non Authoritative Information';

    /**
     * No content (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_NO_CONTENT                        = '204 No Content';

    /**
     * Reset content (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_RESET_CONTENT                     = '205 Reset Content';

    /**
     * Partial content (please, see RFC7233).
     *
     * @const string
     */
    const STATUS_PARTIAL_CONTENT                   = '206 Partial Content';

    /**
     * Multi-status (please, see RFC4918).
     *
     * @const string
     */
    const STATUS_MULTI_STATUS                      = '207 Multi-Status';

    /**
     * Already Reported (please, see RFC5842).
     *
     * @const string
     */
    const STATUS_ALREADY_REPORTED                  = '208 Already Reported';

    /**
     * IM used (please, see RFC3229).
     *
     * @const string
     */
    const STATUS_IM_USED                           = '226 IM Used';

    /**
     * Multiple choices (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_MULTIPLE_CHOICES                  = '300 Multiple Choices';

    /**
     * Moved permanently (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_MOVED_PERMANENTLY                 = '301 Moved Permanently';

    /**
     * Found (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_FOUND                             = '302 Found';

    /**
     * See other (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_SEE_OTHER                         = '303 See Other';

    /**
     * Not modified (please, see RFC7232).
     *
     * @const string
     */
    const STATUS_NOT_MODIFIED                      = '304 Not Modified';

    /**
     * Use proxy (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_USE_PROXY                         = '305 Use Proxy';

    /**
     * Temporary redirect (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_TEMPORARY_REDIRECT                = '307 Temporary Redirect';

    /**
     * Permanent redirect (please, see RFC7238).
     *
     * @const string
     */
    const STATUS_PERMANENT_REDIRECT                = '308 Permanent Redirect';

    /**
     * Bad request (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_BAD_REQUEST                       = '400 Bad Request';

    /**
     * Unauthorized (please, see RFC7235).
     *
     * @const string
     */
    const STATUS_UNAUTHORIZED                      = '401 Unauthorized';

    /**
     * Payment required (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_PAYMENT_REQUIRED                  = '402 Payment Required';

    /**
     * Forbidden (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_FORBIDDEN                         = '403 Forbidden';

    /**
     * Not found (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_NOT_FOUND                         = '404 Not Found';

    /**
     * Method not allowed (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_METHOD_NOT_ALLOWED                = '405 Method Not Allowed';

    /**
     * Not acceptable (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_NOT_ACCEPTABLE                    = '406 Not Acceptable';

    /**
     * Proxy authentification required (please, see RFC7235).
     *
     * @const string
     */
    const STATUS_PROXY_AUTHENTIFICATION_REQUIRED   = '407 Proxy Authentification Required';

    /**
     * Request time-out (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_REQUEST_TIME_OUT                  = '408 Request Timeout';

    /**
     * Conflict (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_CONFLICT                          = '409 Conflict';

    /**
     * Gone (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_GONE                              = '410 Gone';

    /**
     * Length required (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_LENGTH_REQUIRED                   = '411 Length Required';

    /**
     * Precondition failed (please, see RFC7232).
     *
     * @const string
     */
    const STATUS_PRECONDITION_FAILED               = '412 Precondition Failed';

    /**
     * Request entity too large (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_REQUEST_ENTITY_TOO_LARGE          = '413 Request Entity Too Large';

    /**
     * Request URI too large (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_REQUEST_URI_TOO_LARGE             = '414 Request URI Too Large';

    /**
     * Unsupported media type (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_UNSUPPORTED_MEDIA_TYPE            = '415 Unsupported Media Type';

    /**
     * Requested range not satisfiable (please, see RFC7233).
     *
     * @const string
     */
    const STATUS_REQUESTED_RANGE_NOT_SATISFIABLE   = '416 Requested Range Not Satisfiable';

    /**
     * Expectation failed (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_EXPECTATION_FAILED                = '417 Expectation Failed';

    /**
     * I'm a teapot (see RFC2324, April Fool's joke).
     *
     * @const string
     */
    const STATUS_IM_A_TEAPOT                       = '418 I\'m a teapot';

    /**
     * Unprocessable Entity (please, see RFC4918).
     *
     * @const string
     */
    const STATUS_UNPROCESSABLE_ENTITY              = '422 Unprocessable Entity';

    /**
     * Locked (please, see RFC4918).
     *
     * @const string
     */
    const STATUS_LOCKED                            = '423 Locked';

    /**
     * Failed Dependency (please, see RFC4918).
     *
     * @const string
     */
    const STATUS_FAILED_DEPENDENCY                 = '424 Failed Dependency';

    /**
     * Upgrade required (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_UPGRADE_REQUIRED                  = '426 Upgrade Required';

    /**
     * Precondition Required (please, see RFC6585).
     *
     * @const string
     */
    const STATUS_PRECONDITION_REQUIRED             = '428 Precondition Required';

    /**
     * Too Many Requests (please, see RFC6585).
     *
     * @const string
     */
    const STATUS_TOO_MANY_REQUESTS                 = '429 Too Many Requests';

    /**
     * Request Header Fields Too Large (please, see RFC6585).
     *
     * @const string
     */
    const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE   = '431 Request Header Fields Too Large';

    /**
     * Internal server error (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_INTERNAL_SERVER_ERROR             = '500 Internal Server Error';

    /**
     * Not implemented (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_NOT_IMPLEMENTED                   = '501 Not Implemented';

    /**
     * Bad gateway (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_BAD_GATEWAY                       = '502 Bad Gateway';

    /**
     * Service unavailable (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_SERVICE_UNAVAILABLE               = '503 Service Unavailable';

    /**
     * Gateway time-out (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_GATEWAY_TIME_OUT                  = '504 Gateway Time Out';

    /**
     * HTTP version not supported (please, see RFC7231).
     *
     * @const string
     */
    const STATUS_HTTP_VERSION_NOT_SUPPORTED        = '505 HTTP Version Not Supported';

    /**
     * Variant Also Negotiates (please, see RFC2295).
     *
     * @const string
     */
    const STATUS_VARIANT_ALSO_NEGOTIATES           = '506 Variant Also Negotiates';

    /**
     * Insufficient Storage (please, see RFC4918).
     *
     * @const string
     */
    const STATUS_INSUFFICIENT_STORAGE              = '507 Insufficient Storage';

    /**
     * Loop Detected (please, see RFC5842).
     *
     * @const string
     */
    const STATUS_LOOP_DETECTED                     = '508 Loop Detected';

    /**
     * Not Extended (please, see RFC2774).
     *
     * @const string
     */
    const STATUS_NOT_EXTENDED                      = '510 Not Extended';

    /**
     * Network Authentification Required (please, see RFC6585).
     *
     * @const string
     */
    const STATUS_NETWORK_AUTHENTIFICATION_REQUIRED = '511 Network Authentification Required';

    /**
     * Status (different ordering).
     *
     * @var arra
     */
    private $_status       = [];

    /**
     * This object hash.
     *
     * @var string
     */
    private $_hash         = null;

    /**
     * ob_*() is stateless, so we manage a stack to avoid cross-buffers
     * manipulations.
     *
     * @var array
     */
    private static $_stack = [];



    /**
     * Constructor.
     *
     * @param   bool    $newBuffer    Whether we run $this->newBuffer().
     *                                Following arguments are for this
     *                                method.
     * @param   mixed   $callable     Callable.
     * @param   int     $size         Size.
     * @return  void
     */
    public function __construct($newBuffer = true, $callable = null, $size = null)
    {
        parent::__construct();
        $this->_hash = spl_object_hash($this);

        if (true === $newBuffer) {
            $this->newBuffer($callable, $size);
        }

        if (empty($this->_status)) {
            $reflection = new \ReflectionClass($this);

            foreach ($reflection->getConstants() as $value) {
                $this->_status[$this->getStatus($value)] = $value;
            }
        }

        return;
    }

    /**
     * Parse a HTTP packet.
     *
     * @param   string  $packet    HTTP packet.
     * @return  void
     */
    public function parse($packet)
    {
        $headers = explode("\r\n", $packet);
        $status  = array_shift($headers);
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

        if (0 === preg_match('#^HTTP/(1\.(?:0|1))\s+(\d{3})#i', $status, $matches)) {
            throw new \Hoa\Http\Exception(
                'HTTP status is not well-formed: %s.',
                0,
                $status
            );
        }

        if (!isset($this->_status[$matches[2]])) {
            throw new \Hoa\Http\Exception(
                'Unknow HTTP status %d in %s.',
                1,
                [$matches[2], $status]
            );
        }

        $this->setHttpVersion((float) $matches[1]);
        $this->_parse($headers);
        $this['status'] = $this->_status[$matches[2]];

        return;
    }

    /**
     * Get real status from static::STATUS_* constants.
     *
     * @return  int
     */
    public static function getStatus($status)
    {
        return (int) substr($status, 0, 3);
    }

    /**
     * Send a new status.
     *
     * @param   string  $status     Status. Please, see static::STATUS_*
     *                              constants.
     * @param   bool    $replace    Whether replace an existing sent header.
     * @return  void
     */
    public function sendStatus($status, $replace = true)
    {
        return $this->sendHeader('status', $status, $replace, $status);
    }

    /**
     * Send a new header.
     *
     * @param   string  $header     Header.
     * @param   string  $value      Value.
     * @param   bool    $replace    Whether replace an existing sent header.
     * @param   string  $status     Force a specific status. Please, see
     *                              static::STATUS_* constants.
     * @return  void
     */
    public function sendHeader(
        $header,
        $value,
        $replace = true,
        $status  = null
    ) {
        if (0     === strcasecmp('status', $header) &&
            false === self::$_fcgi) {
            header(
                'HTTP/1.1 ' . $value,
                $replace,
                static::getStatus($value)
            );

            return;
        }

        header(
            $header . ': ' . $value,
            $replace,
            null !== $status ? static::getStatus($status) : null
        );

        return;
    }

    /**
     * Send all headers.
     *
     * @return  void
     */
    public function sendHeaders()
    {
        foreach ($this->_headers as $header => $value) {
            $this->sendHeader($header, $value);
        }

        return;
    }

    /**
     * Get send headers.
     *
     * @return  void
     */
    public function getSentHeaders()
    {
        return implode("\r\n", headers_list());
    }

    /**
     * Start a new buffer.
     * The callable acts like a filter.
     *
     * @param   mixed   $callable    Callable.
     * @param   int     $size        Size.
     * @return  int
     */
    public function newBuffer($callable = null, $size = null)
    {
        $last = current(self::$_stack);
        $hash = $this->getHash();

        if (false === $last || $hash != $last[0]) {
            self::$_stack[] = [
                0 => $hash,
                1 => 1
            ];
        } else {
            ++self::$_stack[key(self::$_stack)][1];
        }

        end(self::$_stack);

        if (null === $callable) {
            ob_start();
        } else {
            ob_start(xcallable($callable), null === $size ? 0 : $size);
        }

        return $this->getBufferLevel();
    }

    /**
     * Flush the buffer.
     *
     * @param   bool  $force    Force to flush the output buffer.
     * @return  void
     */
    public function flush($force = false)
    {
        if (0 >= $this->getBufferSize()) {
            return;
        }

        ob_flush();

        if (true === $force) {
            flush();
        }

        return;
    }

    /**
     * Delete buffer.
     *
     * @return  bool
     * @throws  \Hoa\Http\Exception\CrossBufferization
     */
    public function deleteBuffer()
    {
        $key = key(self::$_stack);

        if ($this->getHash() != self::$_stack[$key][0]) {
            throw new \Hoa\Http\Exception\CrossBufferization(
                'Cannot delete this buffer because it was not opened by this ' .
                'class (%s, %s).',
                2,
                [get_class($this), $this->getHash()]
            );
        }

        $out = ob_end_clean();

        if (false === $out) {
            return false;
        }

        --self::$_stack[$key][1];

        if (0 >= self::$_stack[$key][1]) {
            unset(self::$_stack[$key]);
        }

        return true;
    }

    /**
     * Get buffer level.
     *
     * @return  int
     */
    public function getBufferLevel()
    {
        return ob_get_level();
    }

    /**
     * Get buffer size.
     *
     * @return  int
     */
    public function getBufferSize()
    {
        return ob_get_length();
    }

    /**
     * Write n characters.
     *
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throws  \Hoa\Http\Exception
     */
    public function write($string, $length)
    {
        if (0 > $length) {
            throw new \Hoa\Http\Exception(
                'Length must be greater than 0, given %d.',
                3,
                $length
            );
        }

        if (strlen($string) > $length) {
            $string = substr($string, 0, $length);
        }

        echo $string;

        return;
    }

    /**
     * Write a string.
     *
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString($string)
    {
        echo (string) $string;

        return;
    }

    /**
     * Write a character.
     *
     * @param   string  $character    Character.
     * @return  mixed
     */
    public function writeCharacter($character)
    {
        echo $character[0];

        return;
    }

    /**
     * Write a boolean.
     *
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean($boolean)
    {
        echo (string) (bool) $boolean;

        return;
    }

    /**
     * Write an integer.
     *
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger($integer)
    {
        echo (string) (int) $integer;

        return;
    }

    /**
     * Write a float.
     *
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat($float)
    {
        echo (string) (float) $float;

        return;
    }

    /**
     * Write an array.
     *
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray(Array $array)
    {
        echo var_export($array, true);

        return;
    }

    /**
     * Write a line.
     *
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine($line)
    {
        if (false !== $n = strpos($line, "\n")) {
            $line = substr($line, 0, $n + 1);
        }

        echo $line;

        return;
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll($string)
    {
        echo $string;

        return;
    }

    /**
     * Truncate a file to a given length.
     *
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate($size)
    {
        if (0 === $size) {
            ob_clean();

            return true;
        }

        $bSize = $this->getBufferSize();

        if ($size >= $bSize) {
            return true;
        }

        echo substr(ob_get_clean(), 0, $size);

        return true;
    }

    /**
     * Get this object hash.
     *
     * @return  string
     */
    public function getHash()
    {
        return $this->_hash;
    }

    /**
     * Delete head buffer.
     *
     * @return  void
     */
    public function __destruct()
    {
        $last = current(self::$_stack);

        if ($this->getHash() != $last[0]) {
            return;
        }

        for ($i = 0, $max = $last[1]; $i < $max; ++$i) {
            $this->flush();

            if (0 < $this->getBufferLevel()) {
                $this->deleteBuffer();
            }
        }

        return;
    }
}

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Http\Response\Response');
