<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Stream
 */
-> import('Stream.~')

/**
 * \Hoa\Stream\IStream\Bufferable
 */
-> import('Stream.I~.Bufferable')

/**
 * \Hoa\Stream\IStream\Lockable
 */
-> import('Stream.I~.Lockable')

/**
 * \Hoa\Stream\IStream\Pointable
 */
-> import('Stream.I~.Pointable');

}

namespace Hoa\StringBuffer {

/**
 * Class \Hoa\StringBuffer.
 *
 * 
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

abstract class StringBuffer
    extends    \Hoa\Stream
    implements \Hoa\Stream\IStream\Bufferable,
               \Hoa\Stream\IStream\Lockable,
               \Hoa\Stream\IStream\Pointable {

    /**
     * String buffer index.
     *
     * @var \Hoa\StringBuffer int
     */
    private static $_i = 0;



    /**
     * Open a new string buffer.
     *
     * @access  public
     * @param   string  $streamName    Stream name.
     * @return  void
     * @throw   \Hoa\Stream\Exception
     */
    public function __construct ( $streamName = null ) {

        if(null === $streamName)
            $streamName = 'hoa://Library/StringBuffer/' .
                          'StringBuffer.php#' . self::$_i++;

        parent::__construct($streamName, null);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string              $streamName    Stream name (here, it is
     *                                             null).
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     * @throw   \Hoa\StringBuffer\Exception
     */
    protected function &_open ( $streamName, \Hoa\Stream\Context $context = null ) {

        if(false === $out = @tmpfile())
            throw new Exception(
                'Failed to open a string buffer.', 0);

        return $out;
    }

    /**
     * Close the current stream.
     *
     * @access  protected
     * @return  bool
     */
    protected function _close ( ) {

        return @fclose($this->getStream());
    }

    /**
     * Flush the output to a stream.
     *
     * @access  public
     * @return  bool
     */
    public function flush ( ) {

        return fflush($this->getStream());
    }

    /**
     * Portable advisory locking.
     *
     * @access  public
     * @param   int     $operation    Operation, use the
     *                                \Hoa\Stream\IStream\Lockable::LOCK_* constants.
     * @return  bool
     */
    public function lock ( $operation ) {

        return flock($this->getStream(), $operation);
    }

    /**
     * Rewind the position of a stream pointer.
     *
     * @access  public
     * @return  bool
     */
    public function rewind ( ) {

        return rewind($this->getStream());
    }

    /**
     * Seek on a stream pointer.
     *
     * @access  public
     * @param   int     $offset    Offset (negative value should be supported).
     * @param   int     $whence    When, use the \Hoa\Stream\IStream\Pointable::SEEK_*
     *                             constants.
     * @return  int
     */
    public function seek ( $offset, $whence = \Hoa\Stream\IStream\Pointable::SEEK_SET ) {

        return fseek($this->getStream(), $offset, $whence);
    }

    /**
     * Get the current position of the stream pointer.
     *
     * @access  public
     * @return  int
     */
    public function tell ( ) {

        $stream = $this->getStream();

        if(null === $stream)
            return 0;

        return ftell($stream);
    }

    /**
     * Initialize the string buffer.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  \Hoa\StringBuffer
     */
    public function initializeWith ( $string ) {

        ftruncate($this->getStream(), 0);
        fwrite($this->getStream(), $string, strlen($string));

        $this->seek(0, \Hoa\Stream\IStream\Pointable::SEEK_SET);

        return $this;
    }
}

}
