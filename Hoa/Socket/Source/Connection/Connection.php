<?php

declare(strict_types=1);

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
 */
abstract class Connection extends Stream implements Stream\IStream\In, Stream\IStream\Out, Stream\IStream\Pathable, \Iterator
{
    /**
     * Read data out-of-band.
     */
    public const READ_OUT_OF_BAND = STREAM_OOB;

    /**
     * Read data but do not consume them.
     */
    public const READ_PEEK        = STREAM_PEEK;

    /**
     * Socket.
     */
    protected $_socket        = null;

    /**
     * Timeout.
     */
    protected $_timeout       = 30;

    /**
     * Flag.
     */
    protected $_flag          = 0;

    /**
     * Context ID.
     */
    protected $_context       = null;

    /**
     * Node name.
     */
    protected $_nodeName      = Socket\Node::class;

    /**
     * Current node.
     */
    protected $_node          = null;

    /**
     * List of nodes (connections) when selecting.
     */
    protected $_nodes         = [];

    /**
     * Whether the stream is quiet.
     */
    protected $_quiet        = false;

    /**
     * Whether the stream is mute.
     */
    protected $_mute          = false;

    /**
     * Whether the stream is disconnected.
     */
    protected $_disconnect    = true;

    /**
     * Whether we should consider remote address or not.
     */
    protected $_remote        = false;

    /**
     * Remote address.
     */
    protected $_remoteAddress = null;

    /**
     * Temporize selected connections when selecting.
     */
    protected $_iterator      = [];



    /**
     * Start a connection.
     */
    public function __construct(?string $socket, int $timeout, int $flag, string $context = null)
    {
        // Children could setSocket() before __construct.
        if (null !== $socket) {
            $this->setSocket($socket);
        }

        $this->setTimeout($timeout);
        $this->setFlag($flag);

        if (null !== $context) {
            $this->setContext($context);
        }

        return;
    }

    /**
     * Connect.
     */
    public function connect(): self
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
     */
    abstract public function select(): iterable;

    /**
     * Consider another connection when selecting connection.
     */
    abstract public function consider(self $other): self;

    /**
     * Check if the current node belongs to a specific server.
     */
    abstract public function is(self $connection): bool;

    /**
     * Get iterator values.
     */
    protected function &getIteratorValues(): array
    {
        return $this->_iterator;
    }

    /**
     * Set the current selected connection.
     */
    protected function _current()
    {
        $current = current($this->getIteratorValues());
        $this->_setStream($current);

        return $current;
    }

    /**
     * Set and get the current selected connection.
     */
    //abstract public function current();

    /**
     * Get the current selected connection index.
     */
    public function key(): int
    {
        return key($this->getIteratorValues());
    }

    /**
     * Advance the internal pointer of the connection iterator and return the
     * current selected connection.
     */
    public function next(): void
    {
        next($this->getIteratorValues());
    }

    /**
     * Rewind the internal iterator pointer and the first connection.
     */
    public function rewind(): void
    {
        reset($this->getIteratorValues());
    }

    /**
     * Check if there is a current connection after calls to the rewind() or the
     * next() methods.
     */
    public function valid(): bool
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
     */
    public function quiet(): bool
    {
        return $this->_quiet =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_RD);
    }

    /**
     * Disable further transmissions.
     */
    public function mute(): bool
    {
        return $this->_mute =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_WR);
    }

    /**
     * Disable further receptions and transmissions, i.e. disconnect.
     */
    public function quietAndMute(): bool
    {
        return $this->_disconnect =
            stream_socket_shutdown($this->getStream(), STREAM_SHUT_RDWR);
    }

    /**
     * Disconnect.
     */
    public function disconnect(): void
    {
        $this->_disconnect = $this->close();
    }

    /**
     * Set socket.
     */
    protected function setSocket(string $socketUri): ?Socket
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
     */
    protected function setTimeout(int $timeout): int
    {
        $old            = $this->_timeout;
        $this->_timeout = $timeout;

        return $old;
    }

    /**
     * Set flag.
     */
    protected function setFlag(int $flag): int
    {
        $old         = $this->_flag;
        $this->_flag = $flag;

        return $old;
    }

    /**
     * Set context.
     */
    protected function setContext(string $context): ?string
    {
        $old            = $this->_context;
        $this->_context = $context;

        return $old;
    }

    /**
     * Set node name.
     */
    public function setNodeName(string $node): string
    {
        $old             = $this->_nodeName;
        $this->_nodeName = $node;

        return $old;
    }

    /**
     * Whether we should consider remote address or not.
     */
    public function considerRemoteAddress(bool $consider): bool
    {
        $old           = $this->_remote;
        $this->_remote = $consider;

        return $old;
    }

    /**
     * Enable or disable encryption.
     */
    public function enableEncryption(
        bool $enable,
        int $type      = null,
        $sessionStream = null
    ): bool {
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
     */
    public function isEncrypted(): bool
    {
        $currentNode = $this->getCurrentNode();

        if (null === $currentNode) {
            return false;
        }

        return null !== $currentNode->getEncryptionType();
    }

    /**
     * Get socket.
     */
    public function getSocket(): ?Socket
    {
        return $this->_socket;
    }

    /**
     * Get timeout.
     */
    public function getTimeout(): int
    {
        return $this->_timeout;
    }

    /**
     * Get flag.
     */
    public function getFlag(): int
    {
        return $this->_flag;
    }

    /**
     * Get context.
     */
    public function getContext(): ?string
    {
        return $this->_context;
    }

    /**
     * Get node name.
     */
    public function getNodeName(): string
    {
        return $this->_nodeName;
    }

    /**
     * Get node ID.
     */
    protected function getNodeId($resource): string
    {
        return sha1((string) (int) $resource, true);
    }

    /**
     * Get current node.
     */
    public function getCurrentNode(): ?Socket\Node
    {
        return $this->_node;
    }

    /**
     * Get nodes list.
     */
    public function getNodes(): array
    {
        return $this->_nodes;
    }

    /**
     * Check if the stream is quiet.
     */
    public function isQuiet(): bool
    {
        return $this->_quiet;
    }

    /**
     * Check if the stream is mute.
     */
    public function isMute(): bool
    {
        return $this->_mute;
    }

    /**
     * Check if the stream is disconnected.
     */
    public function isDisconnected(): bool
    {
        return false !== $this->_disconnect;
    }

    /**
     * Check if we should consider remote address or not.
     */
    public function isRemoteAddressConsidered(): bool
    {
        return $this->_remote;
    }

    /**
     * Get remote address.
     */
    public function getRemoteAddress(): string
    {
        return $this->_remoteAddress;
    }

    /**
     * Read n characters.
     * Warning: if this method returns false, it means that the buffer is empty.
     * You should use the Hoa\Stream::setStreamBlocking(true) method.
     */
    public function read(int $length, int $flags = 0)
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
     */
    public function readString(int $length)
    {
        return $this->read($length);
    }

    /**
     * Read a character.
     * It is equivalent to $this->read(1).
     */
    public function readCharacter()
    {
        return $this->read(1);
    }

    /**
     * Read a boolean.
     */
    public function readBoolean()
    {
        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     */
    public function readInteger(int $length = 1)
    {
        return (int) $this->read($length);
    }

    /**
     * Read a float.
     */
    public function readFloat(int $length = 1)
    {
        return (float) $this->read($length);
    }

    /**
     * Read an array.
     * Alias of the $this->scanf() method.
     */
    public function readArray(string $format = null)
    {
        return $this->scanf($format);
    }

    /**
     * Read a line.
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
     */
    public function readAll(int $offset = -1)
    {
        return stream_get_contents($this->getStream());
    }

    /**
     * Parse input from a stream according to a format.
     */
    public function scanf(string $format): array
    {
        return sscanf($this->readAll(), $format);
    }

    /**
     * Write n characters.
     */
    public function write(string $string, int $length)
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
                null === $remote = $this->getRemoteAddress()) {
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
     */
    public function writeString(string $string)
    {
        $string = (string) $string;

        return $this->write($string, strlen($string));
    }

    /**
     * Write a character.
     */
    public function writeCharacter(string $char)
    {
        return $this->write((string) $char[0], 1);
    }

    /**
     * Write a boolean.
     */
    public function writeBoolean(bool $boolean)
    {
        return $this->write((string) (bool) $boolean, 1);
    }

    /**
     * Write an integer.
     */
    public function writeInteger(int $integer)
    {
        $integer = (string) (int) $integer;

        return $this->write($integer, strlen($integer));
    }

    /**
     * Write a float.
     */
    public function writeFloat(float $float)
    {
        $float = (string) (float) $float;

        return $this->write($float, strlen($float));
    }

    /**
     * Write a line.
     */
    public function writeLine(string $line)
    {
        if (false === $n = strpos($line, "\n")) {
            return $this->write($line . "\n", strlen($line) + 1);
        }

        ++$n;

        return $this->write(substr($line, 0, $n), $n);
    }

    /**
     * Write an array.
     */
    public function writeArray(array $array)
    {
        $array = serialize($array);

        return $this->write($array, strlen($array));
    }

    /**
     * Write all, i.e. as much as possible.
     */
    public function writeAll(string $string)
    {
        return $this->write($string, strlen($string));
    }

    /**
     * Truncate a file to a given length.
     */
    public function truncate(int $size): bool
    {
        return false;
    }

    /**
     * Test for end-of-file.
     */
    public function eof(): bool
    {
        return feof($this->getStream());
    }

    /**
     * Get filename component of path.
     */
    public function getBasename(): string
    {
        return basename($this->getSocket()->__toString());
    }

    /**
     * Get directory name component of path.
     */
    public function getDirname(): string
    {
        return dirname($this->getSocket()->__toString());
    }
}

/**
 * Flex entity.
 */
Consistency::flexEntity(Connection::class);
