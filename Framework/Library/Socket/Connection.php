<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Connection
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Socket_Exception
 */
import('Socket.Exception');

/**
 * Hoa_Stream
 */
import('Stream.~');

/**
 * Hoa_Stream_Io
 */
import('Stream.Io');

/**
 * Class Hoa_Socket_Connection.
 *
 * Abstract connection, usefull for client and server.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Socket
 * @subpackage  Hoa_Socket_Connection
 */

abstract class Hoa_Socket_Connection
    extends    Hoa_Stream
    implements Hoa_Stream_Io {

    /**
     * Socket.
     *
     * @var Hoa_Socket_Interface object
     */
    protected $_socket     = null;

    /**
     * Timeout.
     *
     * @var Hoa_Socket_Connection int
     */
    protected $_timeout    = 30;

    /**
     * Flag.
     *
     * @var Hoa_Socket_Connection int
     */
    protected $_flag       = 0;

    /**
     * Context ID.
     *
     * @var Hoa_Socket_Connection string
     */
    protected $_context    = null;

    /**
     * Whether the stream is quiet.
     *
     * @var Hoa_Socket_Connection bool
     */
    protected $_quiet      = false;

    /**
     * Whether the stream is mute.
     *
     * @var Hoa_Socket_Connection bool
     */
    protected $_mute       = false;

    /**
     * Whether the stream is disconnected.
     *
     * @var Hoa_Socket_Connection bool
     */
    protected $_disconnect = false;



    /**
     * Constructor.
     * Configure a socket.
     *
     * @access  public
     * @param   Hoa_Socket_Interface  $socket     Socket.
     * @param   int                   $timeout    Timeout.
     * @param   int                   $flag       Flag, see the child::* constants.
     * @param   string                $context    Context ID (please, see the
     *                                Hoa_Stream_Context class).
     * @return  void
     */
    public function __construct ( Hoa_Socket_Interface $socket, $timeout, $flag,
                                  $context = null ) {

        $this->setSocket($socket);
        $this->setTimeout($timeout);
        $this->setFlag($flag);
        $this->setContext($context);

        return;
    }

    /**
     * Connect.
     *
     * @access  public
     * @return  void
     */
    public function connect ( ) {

        parent::__construct(
            $this->getSocket()->__toString(),
            $this->getContext()
        );

        return;
    }

    /**
     * Disable further receptions.
     *
     * @access  public
     * @return  bool
     */
    public function quiet ( ) {

        return $this->_quiet =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_RD);
    }

    /**
     * Disable further transmissions.
     *
     * @access  public
     * @return  bool
     */
    public function mute ( ) {

        return $this->_mute =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_WR);
    }

    /**
     * Disable further receptions and transmissions, i.e. disconnect.
     *
     * @access  public
     * @return  bool
     */
    public function quietAndMute ( ) {

        return $this->_disconnect =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_RDWR);
    }

    /**
     * Disconnect.
     *
     * @access  public
     * @return  void
     */
    public function disconnect ( ) {

        $this->_disconnect = $this->close();

        return;
    }

    /**
     * Set socket.
     *
     * @access  protected
     * @param   Hoa_Socket_Interface  $socket     Socket.
     * @return  Hoa_Socket_Interface
     */
    protected function setSocket ( Hoa_Socket_Interface $socket ) {

        $old           = $this->_socket;
        $this->_socket = $socket;

        return $old;
    }

    /**
     * Set timeout.
     *
     * @access  protected
     * @param   int        $timeout    Timeout.
     * @return  int
     */
    protected function setTimeout ( $timeout ) {

        $old            = $this->_timeout;
        $this->_timeout = $timeout;

        return $old;
    }

    /**
     * Set flag.
     *
     * @access  protected
     * @param   int        $flag    Flag.
     * @return  int
     */
    protected function setFlag ( $flag ) {

        $old         = $this->_flag;
        $this->_flag = $flag;

        return $old;
    }

    /**
     * Set context.
     *
     * @access  protected
     * @param   string     $context    Context ID.
     * @return  string
     */
    protected function setContext ( $context ) {

        $old            = $this->_context;
        $this->_context = $context;

        return $old;
    }

    /**
     * Get socket.
     *
     * @access  public
     * @return  Hoa_Socket_Socket
     */
    public function getSocket ( ) {

        return $this->_socket;
    }

    /**
     * Get timeout.
     *
     * @access  public
     * @return  int
     */
    public function getTimeout ( ) {

        return $this->_timeout;
    }

    /**
     * Get flag.
     *
     * @access  public
     * @return  int
     */
    public function getFlag ( ) {

        return $this->_flag;
    }

    /**
     * Get context.
     *
     * @access  public
     * @return  string
     */
    public function getContext ( ) {

        return $this->_context;
    }

    /**
     * Check if the stream is quiet.
     *
     * @access  public
     * @return  bool
     */
    public function isQuiet ( ) {

        return $this->_quiet;
    }

    /**
     * Check if the stream is mute.
     *
     * @access  public
     * @return  bool
     */
    public function isMute ( ) {

        return $this->_mute;
    }

    /**
     * Check if the stream is disconnected.
     *
     * @access  public
     * @return  bool
     */
    public function isDisconnected ( ) {

        return $this->_disconnect;
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     */
    public function read ( $length ) {

        if(null === $this->getStream())
            throw new Hoa_Socket_Exception(
                'Cannot read because socket is not established, ' .
                'i.e. not connected.', 0);

        return stream_socket_recvfrom($this->getStream(), $length);
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
     * It is equivalent to $this->read(1).
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
     * Read an array.
     * Alias of the $this->scanf() method.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function readArray ( $format ) {

        return $this->scanf($format);
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        $out = null;
        $tmp = null;

        while(('' != $tmp = $this->readCharacter()) && $tmp != "\n")
            $out .= $tmp;

        return $out;
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        $out = null;
        $tmp = null;

        while('' != $tmp = $this->readCharacter())
            $out .= $tmp;

        return $out;
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
     * Write n characters.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throw   Hoa_Socket_Exception
     */
    public function write ( $string, $length ) {

        if(null === $this->getStream())
            throw new Hoa_Socket_Exception(
                'Cannot write because socket is not established, ' .
                'i.e. not connected.', 1);

        if($length <= 0)
            throw new Hoa_Socket_Exception(
                'Length must be greather than 0, given %d.', 0, $length);

        if(strlen($string) > $length)
            $string = substr($string, 0, $length);

        return stream_socket_sendto($this->getStream(), $string);
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
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine ( $line ) {

        if(false === $n = strpos($line, "\n"))
            return $this->write($line . "\n", strlen($line) + 1);

        $n++;

        return $this->write(substr($line, 0, $n), $n);
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray ( $array) {

        $array = serialize($array);

        return $this->write($array, strlen($array));
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
     * Truncate a file to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate ( $size ) {

        return false;
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
     * Get filename component of path.
     *
     * @access  public
     * @return  string
     */
    public function getBasename ( ) {

        return basename($this->getSocket()->__toString());
    }

    /**
     * Get directory name component of path.
     *
     * @access  public
     * @return  string
     */
    public function getDirname ( ) {

        return dirname($this->getSocket()->__toString());
    }

    /**
     * Get size.
     *
     * @access  public
     * @return  int
     */
    public function getSize ( ) {

        return Hoa_Stream_Io::SIZE_UNDEFINED;
    }
}
