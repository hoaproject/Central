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
 * \Hoa\Php\Io\Exception
 */
-> import('Php.Io.Exception')

/**
 * \Hoa\Stream
 */
-> import('Stream.~')

/**
 * \Hoa\Stream\IStream\In
 */
-> import('Stream.I~.In');

/**
 * Whether it is not defined.
 */
_define('STDIN', fopen('php://stdin', 'rb'));

}

namespace Hoa\Php\Io {

/**
 * Class \Hoa\Php\Io\In.
 *
 * Manage the php://stdin stream.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class In extends \Hoa\Stream implements \Hoa\Stream\IStream\In {

    /**
     * Open a stream to php://stdin.
     * Actually, it is a kind of singleton because the stream resource is
     * defined in the STDIN constant.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        parent::__construct('php://stdin', null);
        $this->alwaysUseStreamResource(true);
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string               $streamName    Stream name (e.g. path or URL).
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     */
    protected function &_open ( $streamName, \Hoa\Stream\Context $context = null ) {

        $out = STDIN;

        return $out;
    }

    /**
     * Close the current stream.
     * Do not want to close the STDIN stream.
     *
     * @access  public
     * @return  bool
     */
    public function _close ( ) {

        return true;
    }

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @return  bool
     */
    public function eof ( ) {

        return feof($this->getStream());
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     * @throw   \Hoa\File\Exception
     */
    public function read ( $length ) {

        if($length <= 0)
            throw new Exception(
                'Length must be greather than 0, given %d.', 3, $length);

        return fread($this->getStream(), $length);
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

        return fgetc($this->getStream());
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
     * Read an array.
     * Alias of the $this->scanf() method.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function readArray ( $format = null ) {

        return $this->scanf($format);
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        return fgets($this->getStream());
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        if(true === $this->isStreamResourceMustBeUsed())
            return trim($this->readLine());

        if(PHP_VERSION_ID < 60000)
            $second = true;
        else
            $second = 0;

        if(null === $this->getStreamContext())
            $third  = null;
        else
            $third  = $this->getStreamContext()->getContext();

        return file_get_contents(
            $this->getStreamName(),
            $second,
            $third,
            0
        );
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf ( $format ) {

        return fscanf($this->getStream(), $format);
    }
}

}
