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

namespace Hoa\Xml;

use Hoa\Event;
use Hoa\Stream;

/**
 * Class \Hoa\Xml\ReadWrite.
 *
 * Read/write a XML element.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class          ReadWrite
    extends    Xml
    implements Stream\IStream\In,
               Stream\IStream\Out
{
    /**
     * Whether we should auto-save the document.
     *
     * @var bool
     */
    protected $_autoSave = true;



    /**
     * Start the stream reader/writer as if it is a XML document.
     *
     * @param   \Hoa\Stream\IStream\In  $stream                 Stream to
     *                                                          read/write.
     * @param   bool                    $initializeNamespace    Whether we
     *                                                          initialize
     *                                                          namespaces.
     * @param   mixed                   $entityResolver         Entity resolver.
     * @param   bool                    $autoSave               Whether we
     *                                                          should
     *                                                          auto-save.
     * @throws  \Hoa\Xml\Exception
     */
    public function __construct(
        Stream\IStream\In $stream,
        $initializeNamespace = true,
        $entityResolver      = null,
        $autoSave            = true
    ) {
        if (true === $autoSave && !($stream instanceof Stream\IStream\Out)) {
            throw new Exception(
                'The stream %s (that has opened %s) must implement ' .
                '\Hoa\Stream\IStream\In and \Hoa\Stream\IStream\Out interfaces.',
                0,
                [get_class($stream), $stream->getStreamName()]
            );
        }

        parent::__construct(
            '\Hoa\Xml\Element\ReadWrite',
            $stream,
            $initializeNamespace,
            $entityResolver
        );

        if (true === $autoSave) {
            Event::getEvent(
                'hoa://Event/Stream/' . $stream->getStreamName() .
                ':close-before'
            )->attach(xcallable($this, '_close'));
        }

        $this->_autoSave = $autoSave;

        return;
    }

    /**
     * Do not use this method. It is called from the
     * hoa://Event/Stream/...:close-before event.
     * It transforms the XML tree as a XML string, truncates the stream to zero
     * and writes all this string into the stream.
     *
     * @param   \Hoa\Event\Bucket  $bucket    Event's bucket.
     * @return  void
     */
    public function _close(Event\Bucket $bucket)
    {
        if (false === $this->isAutoSaveEnabled()) {
            return;
        }

        $handle = $this->getStream()->selectRoot()->asXML();

        if (true === $this->getInnerStream()->truncate(0)) {
            $this->getInnerStream()->writeAll($handle);
        }

        return;
    }

    /**
     * Test for end-of-file.
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->getStream()->eof();
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
        return $this->getStream()->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString($length)
    {
        return $this->getStream()->readString($length);
    }

    /**
     * Read a character.
     *
     * @return  string
     */
    public function readCharacter()
    {
        return $this->getStream()->readCharacter();
    }

    /**
     * Read a boolean.
     *
     * @return  bool
     */
    public function readBoolean()
    {
        return $this->getStream()->readBoolean();
    }

    /**
     * Read an integer.
     *
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger($length = 1)
    {
        return $this->getStream()->readInteger($length);
    }

    /**
     * Read a float.
     *
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat($length = 1)
    {
        return $this->getStream()->readFloat($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray($argument = null)
    {
        return $this->getStream()->readArray($argument);
    }

    /**
     * Read a line.
     *
     * @return  string
     */
    public function readLine()
    {
        return $this->getStream()->readLine();
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @param   int  $offset    Offset.
     * @return  string
     */
    public function readAll($offset = 0)
    {
        return $this->getStream()->readAll($offset);
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf($format)
    {
        return $this->getStream()->scanf($format);
    }

    /**
     * Read content as a DOM tree.
     *
     * @return  \DOMElement
     */
    public function readDOM()
    {
        return $this->getStream()->readDOM();
    }

    /**
     * Write n characters.
     *
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throws  \Hoa\Xml\Exception
     */
    public function write($string, $length)
    {
        return $this->getStream()->write($string, $length);
    }

    /**
     * Write a string.
     *
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString($string)
    {
        return $this->getStream()->writeString($string);
    }

    /**
     * Write a character.
     *
     * @param   string  $char    Character.
     * @return  mixed
     */
    public function writeCharacter($char)
    {
        return $this->getStream()->writeCharacter($char);
    }

    /**
     * Write a boolean.
     *
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean($boolean)
    {
        return $this->getStream()->writeBoolean($boolean);
    }

    /**
     * Write an integer.
     *
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger($integer)
    {
        return $this->getStream()->writeInteger($integer);
    }

    /**
     * Write a float.
     *
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat($float)
    {
        return $this->getStream()->writeFloat($float);
    }

    /**
     * Write an array.
     *
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray(array $array)
    {
        return $this->getStream()->writeArray($array);
    }

    /**
     * Write a line.
     *
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine($line)
    {
        return $this->getStream()->writeLine($line);
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll($string)
    {
        return $this->getStream()->writeAll($string);
    }

    /**
     * Truncate to a given length.
     *
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate($size)
    {
        return $this->getStream()->truncate($size);
    }

    /**
     * Write a DOM tree.
     *
     * @param   \DOMNode  $dom    DOM tree.
     * @return  mixed
     */
    public function writeDOM(\DOMNode $dom)
    {
        return $this->getStream()->writeDOM($dom);
    }

    /**
     * Write attributes.
     *
     * @param   array   $attributes    Attributes.
     * @return  void
     */
    public function writeAttributes(array $attributes)
    {
        return $this->getStream()->writeAttributes($attributes);
    }

    /**
     * Write an attribute.
     *
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  void
     */
    public function writeAttribute($name, $value)
    {
        return $this->getStream()->writeAttribute($name, $value);
    }

    /**
     * Check if we should auto-save the document.
     *
     * @return  string
     */
    public function isAutoSaveEnabled()
    {
        return $this->_autoSave;
    }
}
