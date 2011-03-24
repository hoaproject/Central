<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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

namespace {

from('Hoa')

/**
 * \Hoa\Xml\Exception
 */
-> import('Xml.Exception')

/**
 * \Hoa\Xml\Element\Basic
 */
-> import('Xml.Element.Basic')

/**
 * \Hoa\Stream\IStream\In
 */
-> import('Stream.I~.In')

/**
 * \Hoa\Stream\IStream\Out
 */
-> import('Stream.I~.Out')

/**
 * \Hoa\StringBuffer\ReadWrite
 */
-> import('StringBuffer.ReadWrite');

}

namespace Hoa\Xml\Element {

/**
 * Class \Hoa\Xml\Element\ReadWrite.
 *
 * Read/write a XML element.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class          ReadWrite
    extends    Basic
    implements \Hoa\Stream\IStream\In,
               \Hoa\Stream\IStream\Out {

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @return  bool
     */
    public function eof ( ) {

        if(null === parent::$_buffer)
            return true;

        return parent::$_buffer->eof();
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     * @throw   \Hoa\Xml\Exception
     */
    public function read ( $length ) {

        if($length <= 0)
            throw new \Hoa\Xml\Exception(
                'Length must be greather than 0, given %d.', 0, $length);

        if(null === parent::$_buffer) {

            parent::$_buffer = new \Hoa\StringBuffer\ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        return parent::$_buffer->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString ( $length ) {

        return $this->read($length);
    }

    /**
     * Read a character.
     *
     * @access  public
     * @return  string
     */
    public function readCharacter ( ) {

        return $this->read(1);
    }

    /**
     * Read a boolean.
     *
     * @access  public
     * @return  bool
     */
    public function readBoolean ( ) {

        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger ( $length = 1 ) {

        return (int) $this->read($length);
    }

    /**
     * Read a float.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat ( $length = 1 ) {

        return (float) $this->read($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @access  public
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray ( $argument = null ) {

        return (array) $this;
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        $handle = $this->readAll();
        $n      = strpos($handle, "\n");

        if(false === $n)
            return $handle;

        return substr($handle, 0, $n);
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        return $this->__toString();
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf ( $format ) {

        return sscanf($this->readAll(), $format);
    }

    /**
     * Read content as a DOM tree.
     *
     * @access  public
     * @return  DOMElement
     */
    public function readDOM ( ) {

        return dom_import_simplexml($this);
    }

    /**
     * Write n characters.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throw   \Hoa\Xml\Exception
     */
    public function write ( $string, $length ) {

        if($length < 0)
            throw new \Hoa\Xml\Exception(
                'Length must be greather than or equal to 0, given %d.',
                1, $length);

        if(null === parent::$_buffer) {

            parent::$_buffer = new \Hoa\StringBuffer\ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        $l = parent::$_buffer->write($string, $length);

        if($l !== $length)
            return false;

        $this[0] = parent::$_buffer->readAll();

        return $l;
    }

    /**
     * Write a string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString ( $string ) {

        $string = (string) $string;

        return $this->write($string, strlen($string));
    }

    /**
     * Write a character.
     *
     * @access  public
     * @param   string  $char    Character.
     * @return  mixed
     */
    public function writeCharacter ( $char ) {

        return $this->write((string) $char[0], 1);
    }

    /**
     * Write a boolean.
     *
     * @access  public
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean ( $boolean ) {

        return $this->write((string) (bool) $boolean, 1);
    }

    /**
     * Write an integer.
     *
     * @access  public
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger ( $integer ) {

        $integer = (string) (int) $integer;

        return $this->write($integer, strlen($integer));
    }

    /**
     * Write a float.
     *
     * @access  public
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat ( $float ) {

        $float = (string) (float) $float;

        return $this->write($float, strlen($float));
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @return  mixed
     * @TODO    readArray does not return attributes and so…
     */
    public function writeArray ( Array $array ) {

        $document = $this->readDOM()->ownerDocument;

        foreach($array as $name => $value) {

            if(is_object($value)) {

                if(!isset($this->{$name}))
                    $this->addChild($name);

                $this->{$name}->readDOM()->parentNode->appendChild(
                    $document->importNode(clone $value->readDOM(), true)
                );
            }
            elseif(is_array($value) && !empty($value)) {

                if(!isset($value[0]))
                    $handle = $this->addChild($name);

                foreach($value as $subname => $subvalue)
                    if(is_object($subvalue)) {

                        if(!isset($this->{$name}))
                            $this->addChild($name);

                        $this->{$name}->readDOM()->parentNode->appendChild(
                            $document->importNode(clone $subvalue->readDOM(), true)
                        );
                    }
                    else {

                        if(!isset($this->{$name}))
                            $this->addChild($name);

                        if(is_array($subvalue)) {

                            $handle->addChild($subname, null)
                                   ->writeArray($subvalue);

                            continue;
                        }

                        if(is_bool($subvalue))
                            $subvalue = $subvalue ? 'true' : 'false';

                        if(is_string($subname))
                            $handle->addChild($subname, $subvalue);
                        else
                            $this->addChild($name, $subvalue);
                    }
            }
        }

        return;
    }

    /**
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine ( $line ) {

        if(false === $n = strpos($line, "\n"))
            return $this->write($line . "\n", strlen($line) + 1);

        ++$n;

        return $this->write(substr($line, 0, $n), $n);
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll ( $string ) {

        return $this->write($string, strlen($string));
    }

    /**
     * Truncate to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate ( $size ) {

        if(null === parent::$_buffer) {

            parent::$_buffer = new \Hoa\StringBuffer\ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        return parent::$_buffer->truncate($size);
    }

    /**
     * Write a DOM tree.
     *
     * @access  public
     * @param   \DOMNode  $dom    DOM tree.
     * @return  mixed
     */
    public function writeDOM ( \DOMNode $dom ) {

        $sx = simplexml_import_dom($dom, get_class($this));

        throw new \Hoa\Xml\Exception(
            'Hmm, TODO?', 42);

        return true;
    }

    /**
     * Write attributes.
     * If an attribute does not exist, it will be created.
     *
     * @access  public
     * @param   array   $attributes    Attributes.
     * @return  void
     */
    public function writeAttributes ( Array $attributes ) {

        foreach($attributes as $name => $value)
            $this->writeAttribute($name, $value);

        return;
    }

    /**
     * Write an attribute.
     * If the attribute does not exist, it will be created.
     *
     * @access  public
     * @param   string  $name     Name.
     * @param   string  $value    Value.
     * @return  void
     */
    public function writeAttribute ( $name, $value ) {

        $this[$name] = $value;

        return;
    }

    /**
     * Remove an attribute.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  void
     */
    public function removeAttribute ( $name ) {

        unset($this[$name]);

        return;
    }
}

}
