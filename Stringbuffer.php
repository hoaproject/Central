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

namespace Hoa\Stringbuffer;

use Hoa\Consistency;
use Hoa\Protocol;
use Hoa\Stream;

/**
 * Class \Hoa\Stringbuffer.
 *
 * This class allows to manipulate a string as a stream.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Stringbuffer
    extends    Stream
    implements Stream\IStream\Bufferable,
               Stream\IStream\Lockable,
               Stream\IStream\Pointable
{
    /**
     * String buffer index.
     *
     * @var int
     */
    private static $_i = 0;



    /**
     * Open a new string buffer.
     *
     * @param   string  $streamName    Stream name.
     * @throws  \Hoa\Stream\Exception
     */
    public function __construct($streamName = null)
    {
        if (null === $streamName) {
            $streamName = 'hoa://Library/Stringbuffer#' . self::$_i++;
        }

        parent::__construct($streamName, null);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @param   string               $streamName    Stream name (here, it is
     *                                              null).
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     * @throws  \Hoa\Stringbuffer\Exception
     */
    protected function &_open($streamName, Stream\Context $context = null)
    {
        if (false === $out = @tmpfile()) {
            throw new Exception('Failed to open a string buffer.', 0);
        }

        return $out;
    }

    /**
     * Close the current stream.
     *
     * @return  bool
     */
    protected function _close()
    {
        return @fclose($this->getStream());
    }

    /**
     * Start a new buffer.
     * The callable acts like a light filter.
     *
     * @param   mixed  $callable    Callable.
     * @param   int    $size        Size.
     * @return  int
     */
    public function newBuffer($callable = null, $size = null)
    {
        $this->setStreamBuffer($size);

        //@TODO manage $callable as a filter?

        return 1;
    }

    /**
     * Flush the output to a stream.
     *
     * @return  bool
     */
    public function flush()
    {
        return fflush($this->getStream());
    }

    /**
     * Delete buffer.
     *
     * @return  bool
     */
    public function deleteBuffer()
    {
        return $this->disableStreamBuffer();
    }

    /**
     * Get bufffer level.
     *
     * @return  int
     */
    public function getBufferLevel()
    {
        return 1;
    }

    /**
     * Get buffer size.
     *
     * @return  int
     */
    public function getBufferSize()
    {
        return $this->getStreamBufferSize();
    }

    /**
     * Portable advisory locking.
     *
     * @param   int     $operation    Operation, use the
     *                                \Hoa\Stream\IStream\Lockable::LOCK_* constants.
     * @return  bool
     */
    public function lock($operation)
    {
        return flock($this->getStream(), $operation);
    }

    /**
     * Rewind the position of a stream pointer.
     *
     * @return  bool
     */
    public function rewind()
    {
        return rewind($this->getStream());
    }

    /**
     * Seek on a stream pointer.
     *
     * @param   int     $offset    Offset (negative value should be supported).
     * @param   int     $whence    When, use the \Hoa\Stream\IStream\Pointable::SEEK_*
     *                             constants.
     * @return  int
     */
    public function seek($offset, $whence = Stream\IStream\Pointable::SEEK_SET)
    {
        return fseek($this->getStream(), $offset, $whence);
    }

    /**
     * Get the current position of the stream pointer.
     *
     * @return  int
     */
    public function tell()
    {
        $stream = $this->getStream();

        if (null === $stream) {
            return 0;
        }

        return ftell($stream);
    }

    /**
     * Initialize the string buffer.
     *
     * @param   string  $string    String.
     * @return  \Hoa\Stringbuffer
     */
    public function initializeWith($string)
    {
        ftruncate($this->getStream(), 0);
        fwrite($this->getStream(), $string, strlen($string));
        $this->rewind();

        return $this;
    }
}

/**
 * Class \Hoa\Stringbuffer\_Protocol.
 *
 * The `hoa://Library/Stringbuffer` node.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class _Protocol extends Protocol\Node
{
    /**
     * Component's name.
     *
     * @var string
     */
    protected $_name = 'Stringbuffer';



    /**
     * ID of the component.
     *
     * @param   string  $id    ID of the component.
     * @return  mixed
     */
    public function reachId($id)
    {
        $stream = resolve(
            'hoa://Library/Stream#hoa://Library/Stringbuffer#' . $id
        );

        if (null === $stream) {
            return null;
        }

        $meta = $stream->getStreamMetaData();

        return $meta['uri'];
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Stringbuffer\Stringbuffer');

/**
 * Add the `hoa://Library/Stringbuffer` node. Help to know the real path of a
 * stringbuffer.
 */
$protocol              = Protocol::getInstance();
$protocol['Library'][] = new _Protocol();
