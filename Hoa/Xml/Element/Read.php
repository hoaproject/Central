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

namespace Hoa\Xml\Element;

use Hoa\Stream;
use Hoa\Stringbuffer;
use Hoa\Xml;

/**
 * Class \Hoa\Xml\Element\Read.
 *
 * Read a XML element.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Read extends Basic implements Stream\IStream\In
{
    /**
     * Test for end-of-file.
     *
     * @return  bool
     */
    public function eof()
    {
        if (null === parent::$_buffer) {
            return true;
        }

        return parent::$_buffer->eof();
    }

    /**
     * Read n characters.
     *
     * @param   int     $length    Length.
     * @return  string
     * @throws  \Hoa\Xml\Exception
     */
    public function read($length)
    {
        if (0 > $length) {
            throw new Xml\Exception(
                'Length must be greater than 0, given %d.',
                0,
                $length
            );
        }

        if (null === parent::$_buffer) {
            parent::$_buffer = new Stringbuffer\ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        return parent::$_buffer->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString($length)
    {
        return $this->read($length);
    }

    /**
     * Read a character.
     *
     * @return  string
     */
    public function readCharacter()
    {
        return $this->read(1);
    }

    /**
     * Read a boolean.
     *
     * @return  bool
     */
    public function readBoolean()
    {
        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     *
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger($length = 1)
    {
        return (int) $this->read($length);
    }

    /**
     * Read a float.
     *
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat($length = 1)
    {
        return (float) $this->read($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray($argument = null)
    {
        return (array) $this;
    }

    /**
     * Read a line.
     *
     * @return  string
     */
    public function readLine()
    {
        $handle = $this->readAll();
        $n      = strpos($handle, "\n");

        if (false === $n) {
            return $handle;
        }

        return substr($handle, 0, $n);
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @param   int  $offset    Offset (not used).
     * @return  string
     */
    public function readAll($offset = 0)
    {
        return $this->__toString();
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf($format)
    {
        return sscanf($this->readAll(), $format);
    }
}
