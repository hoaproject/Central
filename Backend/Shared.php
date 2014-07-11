<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Worker\Backend\Exception
 */
-> import('Worker.Backend.Exception')

/**
 * \Hoa\Worker\Run
 */
-> import('Worker.Run')

/**
 * \Hoa\Zombie
 */
-> import('Zombie.~')

/**
 * \Hoa\Socket\Client
 */
-> import('Socket.Client')

/**
 * \Hoa\Socket\Server
 */
-> import('Socket.Server')

/**
 * \Hoa\Fastcgi\Responder
 */
-> import('Fastcgi.Responder');

}

namespace Hoa\Worker\Backend {

/**
 * Class \Hoa\Worker\Backend\Shared.
 *
 * A shared worker is like a daemon, but it builds an internal server that
 * receives messages. According to these messages, an action is performed
 * by the help of the “message” listener.
 * A shared worker behaves like a daemon without the need of fork. It must run
 * behind PHP-FPM because we need the fastcgi_finish_request() function that
 * close the current FastCGI request but not the program execution. Then, the
 * internal server is started and continues to live in a PHP-FPM process. Well,
 * we have a daemon :-).
 * How to use it? Easy.
 * Your program:
 *     • __construct;
 *     • run.
 * Your “worker starter” (see $ hoa worker:start):
 *     • start.
 * Your “worker stopper” (see $ hoa worker:stop):
 *     • __construct;
 *     • stop.
 * To construct the worker, we need a socketable object for the internal server.
 * To start the worker, we need a socketable object to the PHP-FPM server.
 * When the shared worker is stopped, the associated .wid file (if exists) is
 * removed.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Shared implements \Hoa\Core\Event\Listenable {

    /**
     * Message type: stop.
     *
     * @const int
     */
    const TYPE_STOP         = 0;

    /**
     * Message type: message (normal).
     *
     * @const int
     */
    const TYPE_MESSAGE      = 1;

    /**
     * Message type: informations.
     *
     * @const int
     */
    const TYPE_INFORMATIONS = 2;

    /**
     * Socket URI.
     *
     * @var \Hoa\Worker\Backend\Shared string
     */
    protected $_socket      = null;

    /**
     * Worker ID.
     *
     * @var \Hoa\Worker\Backend\Shared string
     */
    protected $_wid         = null;

    /**
     * Listeners.
     *
     * @var \Hoa\Core\Event\Listener object
     */
    protected $_on          = null;

    /**
     * Worker's password (needed to stop the worker).
     *
     * @var \Hoa\Worker\Backend\Shared string
     */
    protected $_password    = null;

    /**
     * Start time.
     *
     * @var \Hoa\Worker\Backend\Shared float
     */
    protected $_startTime   = 0;

    /**
     * Number of received messages.
     *
     * @var \Hoa\Worker\Backend\Shared float
     */
    protected $_messages    = 0;

    /**
     * Last received message time.
     *
     * @var \Hoa\Worker\Backend\Shared float
     */
    protected $_lastMessage = 0;



    /**
     * Construct a worker.
     *
     * @access  public
     * @param   mixed   $workerId    Worker ID or a socket client (i.e. a
     *                               \Hoa\Socket\Client object).
     * @param   string  $password    Worker's password.
     * @return  void
     * @throw   \Hoa\Worker\Exception
     * @throw   \Hoa\Worker\Backend\Exception
     */
    public function __construct ( $workerId, $password ) {

        if(   !is_string($workerId)
           && !($workerId instanceof \Hoa\Socket\Client))
            throw new Exception(
                'Either you give a worker ID or you give an object of type ' .
                '\Hoa\Socket\Client, but not anything else; given %s',
                0, is_object($workerId) ? get_class($workerId) : $workerId);

        if(is_string($workerId)) {

            $this->_wid = $workerId;
            $handle     = \Hoa\Worker\Run::get($workerId);
            $workerId   = $handle['socket'];
        }

        set_time_limit(0);

        $this->_socket    = $workerId;
        $this->_on        = new \Hoa\Core\Event\Listener($this, array('message'));
        $this->_password  = sha1($password);
        $this->_startTime = microtime(true);

        return;
    }

    /**
     * Attach a callable to this listenable object.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $callable      Callable.
     * @return  \Hoa\Worker\Backend\Shared
     * @throw   \Hoa\Core\Exception
     */
    public function on ( $listenerId, $callable ) {

        $this->_on->attach($listenerId, $callable);

        return $this;
    }

    /**
     * Run the shared worker.
     * It creates a zombie with \Hoa\Zombie.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Worker\Backend\Exception
     */
    public function run ( ) {

        $server = new \Hoa\Socket\Server($this->_socket);
        $server->connectAndWait();

        \Hoa\Zombie::fork();

        $_eom   = pack('C', 0);

        while(true) foreach($server->select() as $node) {

            $request = unpack('nr', $server->read(2));
            $length  = unpack('Nl', $server->read(4));
            $message = unserialize($server->read($length['l']));
            $eom     = unpack('Ce', $server->read(1));

            if($eom['e'] != $_eom) {

                $server->disconnect();

                continue;
            }

            switch($request['r']) {

                case static::TYPE_MESSAGE:
                    $this->_on->fire('message', new \Hoa\Core\Event\Bucket(array(
                        'message' => $message
                    )));
                    ++$this->_messages;
                    $this->_lastMessage = time();
                  break;

                case static::TYPE_STOP:
                    if($this->_password === $message) {

                        $server->disconnect();

                        break 3;
                    }
                  break;

                case static::TYPE_INFORMATIONS:
                    $message = array(
                        'id'                    => $this->_wid,
                        'socket'                => $this->_socket,
                        'start'                 => $this->_startTime,
                        'pid'                   => getmypid(),
                        'memory'                => memory_get_usage(true),
                        'memory_allocated'      => memory_get_usage(),
                        'memory_peak'           => memory_get_peak_usage(true),
                        'memory_allocated_peak' => memory_get_usage(),
                        'messages'              => $this->_messages,
                        'last_message'          => $this->_lastMessage,
                        'filename'              => $_SERVER['SCRIPT_FILENAME']
                    );
                    $server->writeAll(
                        static::pack(static::TYPE_MESSAGE, $message)
                    );
                  break;
            }

            $server->disconnect();
        }

        $server->disconnect();

        if(null !== $this->_wid)
            \Hoa\Worker\Run::unregister($this->_wid);

        return;
    }

    /**
     * Start the shared worker.
     *
     * @access  public
     * @param   string  $socket             Socket URI to PHP-FPM server.
     * @param   string  $workerPath         Path to the shared worker program.
     * @param   array   $fastcgiParameters  Array of additional parameters for FastCGI.
     * @throw  \Hoa\Worker\Backend\Exception
     * @return  bool
     */
    public static function start ( $socket, $workerPath, array $fastcgiParameters = array() ) {

        $server = new \Hoa\Fastcgi\Responder(
            new \Hoa\Socket\Client($socket)
        );

        $headers = [
            'GATEWAY_INTERFACE' => 'FastCGI/1.0',
            'SERVER_PROTOCOL'   => 'HTTP/1.1',
            'REQUEST_URI'       => $workerPath,
            'SCRIPT_FILENAME'   => $workerPath,
            'SCRIPT_NAME'       => DS . dirname($workerPath)
        ];

        $fastcgiParameters = array_merge([
            'REQUEST_METHOD'    => 'GET'
        ], $fastcgiParameters);

        return $server->send(array_merge($fastcgiParameters, $headers));
    }

    /**
     * Stop the shared worker.
     *
     * @access  public
     * @return  bool
     */
    public function stop ( ) {

        $client = new \Hoa\Socket\Client($this->_socket);
        $client->connect();
        $client->writeAll(static::pack(static::TYPE_STOP, $this->_password));
        $client->disconnect();

        return true;
    }

    /**
     * Pack a message.
     *
     * @access  public
     * @param   int     $type       Please, see self::TYPE_* constants.
     * @param   mixed   $message    Whatever you want.
     * @return  string
     */
    public static function pack ( $type, $message ) {

        $message = serialize($message);

        return pack('n', $type ) .
               pack('N', strlen($message)) .
               $message .
               pack('C', 0);
    }
}

}
