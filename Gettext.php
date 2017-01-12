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

namespace Hoa\Translate;

use Hoa\Stream;

/**
 * Class \Hoa\Translate\Gettext.
 *
 * GetText format reader.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Gettext extends Translate
{
    /**
     * Endianness.
     *
     * @var bool
     */
    protected $_bigEndian              = false;

    /**
     * Revision.
     *
     * @var int
     */
    protected $_revision               = 0;

    /**
     * Headers.
     *
     * @var array
     */
    protected $_headers                = [];

    /**
     * Plural function ID.
     *
     * @var string
     */
    protected $_plural                 = null;

    /**
     * Plural functions.
     *
     * @var array
     */
    protected static $_pluralFunctions = [];



    /**
     * Constructor.
     *
     * @param   \Hoa\Stream\IStream\In  $stream    Stream.
     */
    public function __construct(Stream\IStream\In $stream)
    {
        parent::__construct($stream);

        if (!($stream instanceof Stream\IStream\Pointable)) {
            throw new Exception(
                'Stream %s must also be pointable.',
                0,
                $stream->getStreamName()
            );
        }

        $stream->seek(0);
        $this->setEndianness();
        $this->unpack();

        return;
    }

    /**
     * Compute endianness.
     *
     * @return  void
     */
    protected function setEndianness()
    {
        $magicNumber = unpack('V1', $this->getStream()->read(4));

        switch (dechex($magicNumber[1])) {
            case '950412de':
                $this->_bigEndian = false;

                break;

            case 'de120495':
                $this->_bigEndian = true;

                break;

            default:
                throw new Exception(
                    '%s is not a GNU MO file.',
                    1,
                    $this->getStream()->getStreamName()
                );
        }

        return;
    }

    /**
     * Unpack data/messages.
     *
     * @return  void
     */
    protected function unpack()
    {
        /**
         * Schema:
         *
         *         byte˼
         *               +------------------------------------------+
         *            0  | magic number = 0x950412de                |
         *               |                                          |
         *            4  | file format revision = 0                 |
         *               |                                          |
         *            8  | number of strings                        |  == N
         *               |                                          |
         *           12  | offset of table with original strings    |  == O
         *               |                                          |
         *           16  | offset of table with translation strings |  == T
         *               |                                          |
         *           20  | size of hashing table                    |  == S
         *               |                                          |
         *           24  | offset of hashing table                  |  == H
         *               |                                          ˼
         *               .                                          .
         *               .    (possibly more entries later)         .
         *               .                                          .
         *               |                                          |
         *            O  | length & offset 0th string  ----------------.
         *        O + 8  | length & offset 1st string  ------------------.
         *                ...                                    ...   | |
         *  O + ((N-1)*8)| length & offset (N-1)th string           |  | |
         *               |                                          |  | |
         *            T  | length & offset 0th translation  ---------------.˼
         *        T + 8  | length & offset 1st translation  -----------------.
         *                ...                                    ...   | | | |
         *  T + ((N-1)*8)| length & offset (N-1)th translation      |  | | | |
         *               |                                          |  | | | |
         *            H  | start hash table                         |  | | | |
         *                ...                                    ...   | | | |
         *    H + S * 4  | end hash table                           |  | | | |
         *               |                                          |  | | | |
         *               | NUL terminated 0th string  <----------------' | | |
         *               |                                          |    | | |
         *               | NUL terminated 1st string  <------------------' | |
         *               |                                          |      | |
         *                ...                                    ...       | |
         *               |                                          |      | |
         *               | NUL terminated 0th translation  <---------------' |
         *               |                                          |        |
         *               | NUL terminated 1st translation  <-----------------'
         *               |                                          |
         *                ...                                    ...
         *               |                                          |
         *               +------------------------------------------+
         */

        $stream   = $this->getStream();
        $out      = &$this->_data;
        $this->setRevision($this->_read(1));
        $notsh = [
            'N' => $this->_read(1),
            'O' => $this->_read(1),
            'T' => $this->_read(1),
            'S' => $this->_read(1),
            'H' => $this->_read(1)
        ];

        $stream->seek($notsh['O']);
        $originalStringOffset = $this->_read(2 * $notsh['N'], null);

        $stream->seek($notsh['T']);
        $translateStringOffset = $this->_read(2 * $notsh['N'], null);

        for ($e = 0, $max = $notsh['N']; $e < $max; ++$e) {
            if (0 === $originalStringOffset[$e * 2 + 1]) {
                $_headers = $this->getHeaders();

                if (!empty($_headers)) {
                    continue;
                }

                $stream->seek($translateStringOffset[$e * 2 + 2]);
                $this->setHeaders(
                    $this->unpackHeaders(
                        $stream->read($translateStringOffset[$e * 2 + 1])
                    )
                );

                continue;
            }

            $stream->seek($originalStringOffset[$e * 2 + 2]);
            $key = $stream->read($originalStringOffset[$e * 2 + 1]);

            $stream->seek($translateStringOffset[$e * 2 + 2]);
            $out[$key] = $stream->read($translateStringOffset[$e * 2 + 1]);
        }

        return;
    }

    /**
     * Read bytes on the stream.
     *
     * @param   int  $bytes      Number of bytes to read.
     * @param   int  $pointer    Pointer/index if read an array.
     * @return  mixed
     */
    protected function _read($bytes, $pointer = 1)
    {
        $buffer = $this->getStream()->read(4 * $bytes);

        if (false === $this->isBigEndian()) {
            $out = unpack('V' . $bytes, $buffer);
        } else {
            $out = unpack('N' . $bytes, $buffer);
        }

        if (isset($out[$pointer])) {
            return $out[$pointer];
        }

        return $out;
    }

    /**
     * Get translation for regular message.
     *
     * @param   string  $message    Message.
     * @param   mixed   …           Parameters.
     * @return  string
     */
    public function _($message)
    {
        if (!isset($this->_data[$message])) {
            return $message;
        }

        $parameters = func_get_args();
        array_shift($parameters);

        if (false === $out = @vsprintf($this->_data[$message], $parameters)) {
            return $message;
        }

        return $out;
    }

    /**
     * Get translation for plural messages.
     * Messages are concatenated by NUL (\0), or \0 or ^@. They can be escaped
     * by \.
     *
     * @param   string  $message    Message.
     * @param   int     $n          n (to select the plural).
     * @param   mixed   …           Parameters.
     * @return  string
     * @throws  \Hoa\Translate\Exception
     */
    public function _n($message, $n)
    {
        if (false === $this->_plural) {
            return $message;
        }

        $n          = max(1, $n);
        $parameters = array_slice(func_get_args(), 2);
        $message    = preg_replace('#(?<!\\\)(\\\0|\^@)#', "\0", $message);
        $message    = preg_replace('#\\\(\\\0|\^@)#',      '\1', $message);

        if (!isset($this->_data[$message])) {
            return $message;
        }

        $plurals = explode("\0", $this->_data[$message]);

        if (!isset($this->_headers['Plural-Forms'])) {
            $this->_plural = false;

            return $message;
        }

        if (null === $this->_plural) {
            $pluralForms = $this->_headers['Plural-Forms'];

            if (false === preg_match('#^nplurals=(\d+);\s*plural=(.+?);?$#s', $pluralForms, $matches)) {
                return $plurals[0];
            }

            list(, $nplurals, $plural) = $matches;
            $_plural                   = preg_replace('#n#',   '$n', $plural);
            $_plural                   = preg_replace('#\s+#', '',   $_plural);
            $id                        = '__hoa_translate_gettext_' . md5($_plural);

            if (!isset(static::$_pluralFunctions[$id])) {
                $handle = @create_function(
                    '$n',
                    'return (int) (' . $_plural . ');'
                );

                if (false === $handle) {
                    throw new Exception(
                        'Something is wrong with your plurial form %s.',
                        2,
                        $plural
                    );
                }

                static::$_pluralFunctions[$id] = $handle;
            }

            $this->_plural = $id;
        }

        $pluralFunction = static::$_pluralFunctions[$this->_plural];
        $i              = $pluralFunction($n);

        if (!isset($plurals[$i])) {
            return $plurals[0];
        }

        if (false === $out = @vsprintf($plurals[$i], $parameters)) {
            return $plurals[$i];
        }

        return $out;
    }

    /**
     * Check if the file is in big endian.
     *
     * @return  bool
     */
    public function isBigEndian()
    {
        return $this->_bigEndian;
    }

    /**
     * Set file format revision.
     *
     * @param   int  $revision    File format revision.
     * @return  int
     */
    protected function setRevision($revision)
    {
        $old             = $this->_revision;
        $this->_revision = $revision;

        return $old;
    }

    /**
     * Get file format revision.
     *
     * @return  int
     */
    public function getRevision()
    {
        return $this->_revision;
    }

    /**
     * Unpack headers from a string.
     *
     * @param   string  $headers    Headers.
     * @return  array
     */
    public static function unpackHeaders($headers)
    {
        $out = [];

        foreach (explode("\n", $headers) as $line) {
            if (empty($line)) {
                continue;
            }

            list($type, $value) = explode(':', $line, 2);
            $out[trim($type)]   = trim($value);
        }

        return $out;
    }

    /**
     * Set headers.
     *
     * @param   array  $headers    Headers.
     * @return  array
     */
    protected function setHeaders(array $headers)
    {
        $old            = $this->_headers;
        $this->_headers = $headers;

        return $old;
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
}
