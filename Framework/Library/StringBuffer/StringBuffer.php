<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
