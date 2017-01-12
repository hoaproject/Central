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
 * Class \Hoa\Xml\Write.
 *
 * Write into a XML element.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Write extends Xml implements Stream\IStream\Out
{
    /**
     * Start the stream reader/writer as if it is a XML document.
     *
     * @param   \Hoa\Stream\IStream\Out  $stream                 Stream to
     *                                                           read/write.
     * @param   bool                     $initializeNamespace    Whether we
     *                                                           initialize
     *                                                           namespaces.
     * @param   mixed                    $entityResolver         Entity resolver.
     */
    public function __construct(
        Stream\IStream\Out $stream,
        $initializeNamespace = true,
        $entityResolver      = null
    ) {
        parent::__construct(
            '\Hoa\Xml\Element\Write',
            $stream,
            $initializeNamespace,
            $entityResolver
        );

        Event::getEvent('hoa://Event/Stream/' . $stream->getStreamName() . ':close-before')
            ->attach(xcallable($this, '_close'));

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
        $handle = $this->getStream()->selectRoot()->asXML();

        if (true === $this->getInnerStream()->truncate(0)) {
            $this->getInnerStream()->writeAll($handle);
        }

        return;
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
}
