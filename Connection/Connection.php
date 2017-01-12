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

namespace Hoa\Socket\Connection;

use Hoa\Consistency;
use Hoa\Socket;
use Hoa\Stream;

/**
 * Class \Hoa\Socket\Connection.
 *
 * Abstract connection, useful for client and server.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
abstract class Connection
    extends    Stream
    implements Stream\IStream\In,
               Stream\IStream\Out,
               Stream\IStream\Pathable,
               \Iterator
{
    /**
     * Read data out-of-band.
     *
     * @const int
     */
    const READ_OUT_OF_BAND = STREAM_OOB;

    /**
     * Read data but do not consume them.
     *
     * @const int
     */
    const READ_PEEK        = STREAM_PEEK;

    /**
     * Socket.
     *
     * @var \Hoa\Socket
     */
    protected $_socket        = null;

    /**
     * Timeout.
     *
     * @var int
     */
    protected $_timeout       = 30;

    /**
     * Flag.
     *
     * @var int
     */
    protected $_flag          = 0;

    /**
     * Context ID.
     *
     * @var string
     */
    protected $_context       = null;

    /**
     * Node name.
     *
     * @var string
     */
    protected $_nodeName      = 'Hoa\Socket\Node';

    /**
     * Current node.
     *
     * @var \Hoa\Socket\Node
     */
    protected $_node          = null;

    /**
     * List of nodes (connections) when selecting.
     *
     * @var array
     */
    protected $_nodes         = [];

    /**
     * Whether the stream is quiet.
     *
     * @var bool
     */
    protected $_quiet        = false;

    /**
     * Whether the stream is mute.
     *
     * @var bool
     */
    protected $_mute          = false;

    /**
     * Whether the stream is disconnected.
     *
     * @var bool
     */
    protected $_disconnect    = true;

    /**
     * Whether we should consider remote address or not.
     *
     * @var bool
     */
    protected $_remote        = false;

    /**
     * Remote address.
     *
     * @var string
     */
    protected $_remoteAddress = null;

    /**
     * Temporize selected connections when selecting.
     *
     * @var array
     */
    protected $_iterator      = [];



    /**
     * Start a connection.
     *
     * @param   string  $socket     Socket URI.
     * @param   int     $timeout    Timeout.
     * @param   int     $flag       Flag, see the child::* constants.
     * @param   string  $context    Context ID (please, see the
     *                              \Hoa\Stream\Context class).
     */
    public function __construct($socket, $timeout, $flag, $context = null)
    {
        // Children could setSocket() before __construct.
        if (null !== $socket) {
            $this->setSocket($socket);
        }

        $this->setTimeout($timeout);
        $this->setFlag($flag);
        $this->setContext($context);

        return;
    }

    /**
     * Connect.
     *
     * @return  \Hoa\Socket\Connection
     */
    public function connect()
    {
        parent::__construct(
            $this->getSocket()->__toString(),
            $this->getContext()
        );
        $this->_disconnect = false;

        return $this;
    }

    /**
     * Select connections.
     *
     * @return  \Hoa\Socket\Connection
     */
    abstract public function select();

    /**
     * Consider another connection when selecting connection.
     *
     * @param   \Hoa\Socket\Connection  $other    Other connection.
     * @return  \Hoa\Socket\Connection
     */
    abstract public function consider(Connection $other);

    /**
     * Check if the current node belongs to a specific server.
     *
     * @param   \Hoa\Socket\Connection  $connection    Connection.
     * @return  bool
     */
    abstract public function is(Connection $connection);

    /**
     * Get iterator values.
     *
     * @return  array
     */
    protected function &getIteratorValues()
    {
        return $this->_iterator;
    }

    /**
     * Set the current selected connection.
     *
     * @return  resource
     */
    protected function _current()
    {
        $current = current($this->getIteratorValues());
        $this->_setStream($current);

        return $current;
    }

    /**
     * Set and get the current selected connection.
     *
     * @return  \Hoa\Socket\Node
     */
    //abstract public function current();

    /**
     * Get the current selected connection index.
     *
     * @return  int
     */
    public function key()
    {
        return key($this->getIteratorValues());
    }

    /**
     * Advance the internal pointer of the connection iterator and return the
     * current selected connection.
     *
     * @return  mixed
     */
    public function next()
    {
        return next($this->getIteratorValues());
    }

    /**
     * Rewind the internal iterator pointer and the first connection.
     *
     * @return  mixed
     */
    public function rewind()
    {
        return reset($this->getIteratorValues());
    }

    /**
     * Check if there is a current connection after calls to the rewind() or the
     * next() methods.
     *
     * @return  bool
     */
    public function valid()
    {
        $iteratorValues = &$this->getIteratorValues();

        if (empty($iteratorValues)) {
            return false;
        }

        $key    = key($iteratorValues);
        $return = (bool) next($iteratorValues);
        prev($iteratorValues);

        if (false === $return) {
            end($iteratorValues);
            if ($key === key($iteratorValues)) {
                $return = true;
            } else {
                $iteratorValues = [];
            }
        }

        return $return;
    }

    /**
     * Disable further receptions.
     *
     * @return  bool
     */
    public function quiet()
    {
        return $this->_quiet =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_RD);
    }

    /**
     * Disable further transmissions.
     *
     * @return  bool
     */
    public function mute()
    {
        return $this->_mute =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_WR);
    }

    /**
     * Disable further receptions and transmissions, i.e. disconnect.
     *
     * @return  bool
     */
    public function quietAndMute()
    {
        return $this->_disconnect =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_RDWR);
    }

    /**
     * Disconnect.
     *
     * @return  void
     */
    public function disconnect()
    {
        $this->_disconnect = $this->close();

        return;
    }

    /**
     * Set socket.
     *
     * @param   string  $socketUri    Socket URI.
     * @return  \Hoa\Socket
     * @throws  \Hoa\Socket\Exception
     */
    protected function setSocket($socketUri)
    {
        if (false === $pos = strpos($socketUri, '://')) {
            $socket = new Socket($socketUri);
        } else {
            $transport = substr($socketUri, 0, $pos);
            $factory   = Socket\Transport::getFactory($transport);
            $socket    = $factory($socketUri);

            if (!($socket instanceof Socket)) {
                throw new Socket\Exception(
                    'The transport registered for scheme “%s” is not valid: ' .
                    'It must return a valid Hoa\Socket\Socket instance.',
                    0,
                    $transport
                );
            }
        }

        $old           = $this->_socket;
        $this->_socket = $socket;

        return $old;
    }

    /**
     * Set timeout.
     *
     * @param   int        $timeout    Timeout.
     * @return  int
     */
    protected function setTimeout($timeout)
    {
        $old            = $this->_timeout;
        $this->_timeout = $timeout;

        return $old;
    }

    /**
     * Set flag.
     *
     * @param   int        $flag    Flag.
     * @return  int
     */
    protected function setFlag($flag)
    {
        $old         = $this->_flag;
        $this->_flag = $flag;

        return $old;
    }

    /**
     * Set context.
     *
     * @param   string     $context    Context ID.
     * @return  string
     */
    protected function setContext($context)
    {
        $old            = $this->_context;
        $this->_context = $context;

        return $old;
    }

    /**
     * Set node name.
     *
     * @param   string  $node    Node name.
     * @return  string
     */
    public function setNodeName($node)
    {
        $old             = $this->_nodeName;
        $this->_nodeName = $node;

        return $old;
    }

    /**
     * Whether we should consider remote address or not.
     *
     * @param   bool  $consider    Should we consider remote address or not.
     * @return  bool
     */
    public function considerRemoteAddress($consider)
    {
        $old           = $this->_remote;
        $this->_remote = $consider;

        return $old;
    }

    /**
     * Enable or disable encryption.
     *
     * @param   bool        $enable           Whether enable encryption.
     * @param   int         $type             Type of encryption (please, see
     *                                        children ENCRYPTION_* constants).
     * @param   resource    $sessionStream    Seed the stream with settings from
     *                                        this session stream.
     * @return  bool
     */
    public function enableEncryption(
        $enable,
        $type          = null,
        $sessionStream = null
    ) {
        $currentNode = $this->getCurrentNode();

        if (null === $currentNode) {
            return false;
        }

        if (null === $type &&
            null === $type = $currentNode->getEncryptionType()) {
            return stream_socket_enable_crypto($this->getStream(), $enable);
        }

        $currentNode->setEncryptionType($type);

        if (null === $sessionStream) {
            return stream_socket_enable_crypto(
                $this->getStream(),
                $enable,
                $type
            );
        }

        return stream_socket_enable_crypto(
            $this->getStream(),
            $enable,
            $type,
            $sessionStream
        );
    }

    /**
     * Check if the connection is encrypted or not.
     *
     * @return  mixed
     */
    public function isEncrypted()
    {
        $currentNode = $this->getCurrentNode();

        if (null === $currentNode) {
            return false;
        }

        return null !== $currentNode->getEncryptionType();
    }

    /**
     * Get socket.
     *
     * @return  \Hoa\Socket
     */
    public function getSocket()
    {
        return $this->_socket;
    }

    /**
     * Get timeout.
     *
     * @return  int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * Get flag.
     *
     * @return  int
     */
    public function getFlag()
    {
        return $this->_flag;
    }

    /**
     * Get context.
     *
     * @return  string
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Get node name.
     *
     * @return  string
     */
    public function getNodeName()
    {
        return $this->_nodeName;
    }

    /**
     * Get node ID.
     *
     * @param   resource  $resource    Resource.
     * @return  string
     */
    protected function getNodeId($resource)
    {
        return md5((int) $resource);
    }

    /**
     * Get current node.
     *
     * @return  \Hoa\Socket\Node
     */
    public function getCurrentNode()
    {
        return $this->_node;
    }

    /**
     * Get nodes list.
     *
     * @return  array
     */
    public function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * Check if the stream is quiet.
     *
     * @return  bool
     */
    public function isQuiet()
    {
        return $this->_quiet;
    }

    /**
     * Check if the stream is mute.
     *
     * @return  bool
     */
    public function isMute()
    {
        return $this->_mute;
    }

    /**
     * Check if the stream is disconnected.
     *
     * @return  bool
     */
    public function isDisconnected()
    {
        return false !== $this->_disconnect;
    }

    /**
     * Check if we should consider remote address or not.
     *
     * @return  bool
     */
    public function isRemoteAddressConsidered()
    {
        return $this->_remote;
    }

    /**
     * Get remote address.
     *
     * @return  string
     */
    public function getRemoteAddress()
    {
        return $this->_remoteAddress;
    }

    /**
     * Read n characters.
     * Warning: if this method returns false, it means that the buffer is empty.
     * You should use the Hoa\Stream::setStreamBlocking(true) method.
     *
     * @param   int    $length    Length.
     * @param   int    $flags     Flags.
     * @return  string
     * @throws  \Hoa\Socket\Exception
     */
    public function read($length, $flags = 0)
    {
        if (null === $this->getStream()) {
            throw new Socket\Exception(
                'Cannot read because socket is not established, ' .
                'i.e. not connected.',
                1
            );
        }

        if (0 > $length) {
            throw new Socket\Exception(
                'Length must be greater than 0, given %d.',
                2,
                $length
            );
        }

        if (true === $this->isEncrypted()) {
            return fread($this->getStream(), $length);
        }

        if (false === $this->isRemoteAddressConsidered()) {
            return stream_socket_recvfrom($this->getStream(), $length, $flags);
        }

        $out = stream_socket_recvfrom(
            $this->getStream(),
            $length,
            $flags,
            $address
        );
        $this->_remoteAddress = !empty($address) ? $address : null;

        return $out;
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
     * It is equivalent to $this->read(1).
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
     * Read an array.
     * Alias of the $this->scanf() method.
     *
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function readArray($format = null)
    {
        return $this->scanf($format);
    }

    /**
     * Read a line.
     *
     * @return  string
     */
    public function readLine()
    {
        if (true === $this->isEncrypted()) {
            return rtrim(fgets($this->getStream(), 1 << 15), "\n");
        }

        return stream_get_line($this->getStream(), 1 << 15, "\n");
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @param   int  $offset    Offset (not used).
     * @return  string
     */
    public function readAll($offset = -1)
    {
        return stream_get_contents($this->getStream());
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

    /**
     * Write n characters.
     *
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throws  \Hoa\Socket\Exception
     * @throws  \Hoa\Socket\Exception\BrokenPipe
     */
    public function write($string, $length)
    {
        if (null === $this->getStream()) {
            throw new Socket\Exception(
                'Cannot write because socket is not established, ' .
                'i.e. not connected.',
                3
            );
        }

        if (0 > $length) {
            throw new Socket\Exception(
                'Length must be greater than 0, given %d.',
                4,
                $length
            );
        }

        if (strlen($string) > $length) {
            $string = substr($string, 0, $length);
        }

        if (true === $this->isEncrypted()) {
            $out = fwrite($this->getStream(), $string, $length);
        } else {
            if (false === $this->isRemoteAddressConsidered() ||
                null  === $remote = $this->getRemoteAddress()) {
                $out = @stream_socket_sendto($this->getStream(), $string);
            } else {
                $out = @stream_socket_sendto(
                    $this->getStream(),
                    $string,
                    0,
                    $remote
                );
            }
        }

        if (-1 === $out) {
            throw new Socket\Exception\BrokenPipe(
                'Pipe is broken, cannot write data.',
                5
            );
        }

        return $out;
    }

    /**
     * Write a string.
     *
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString($string)
    {
        $string = (string) $string;

        return $this->write($string, strlen($string));
    }

    /**
     * Write a character.
     *
     * @param   string  $char    Character.
     * @return  mixed
     */
    public function writeCharacter($char)
    {
        return $this->write((string) $char[0], 1);
    }

    /**
     * Write a boolean.
     *
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean($boolean)
    {
        return $this->write((string) (bool) $boolean, 1);
    }

    /**
     * Write an integer.
     *
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger($integer)
    {
        $integer = (string) (int) $integer;

        return $this->write($integer, strlen($integer));
    }

    /**
     * Write a float.
     *
     * @param   float   $float    Float.
     * @return  mixed
     */
   public function writeFloat($float)
   {
       $float = (string) (float) $float;

       return $this->write($float, strlen($float));
   }

    /**
     * Write a line.
     *
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine($line)
    {
        if (false === $n = strpos($line, "\n")) {
            return $this->write($line . "\n", strlen($line) + 1);
        }

        ++$n;

        return $this->write(substr($line, 0, $n), $n);
    }

    /**
     * Write an array.
     *
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray(array $array)
    {
        $array = serialize($array);

        return $this->write($array, strlen($array));
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll($string)
    {
        return $this->write($string, strlen($string));
    }

    /**
     * Truncate a file to a given length.
     *
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate($size)
    {
        return false;
    }

    /**
     * Test for end-of-file.
     *
     * @return  bool
     */
    public function eof()
    {
        return feof($this->getStream());
    }

    /**
     * Get filename component of path.
     *
     * @return  string
     */
    public function getBasename()
    {
        return basename($this->getSocket()->__toString());
    }

    /**
     * Get directory name component of path.
     *
     * @return  string
     */
    public function getDirname()
    {
        return dirname($this->getSocket()->__toString());
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity('Hoa\Socket\Connection\Connection');
