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
 * \Hoa\Worker\Backend\Exception
 */
-> import('Worker.Backend.Exception')

/**
 * \Hoa\Worker\Run
 */
-> import('Worker.Run')

/**
 * \Hoa\Socket\Connection\Client
 */
-> import('Socket.Connection.Client')

/**
 * \Hoa\Socket\Connection\Server
 */
-> import('Socket.Connection.Server')

/**
 * \Hoa\FastCgi\Responder
 */
-> import('FastCgi.Responder');

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
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Shared implements \Hoa\Core\Event\Listenable {

    /**
     * Message type: stop.
     *
     * @var \Hoa\Worker\Backend\Shared int
     */
    const TYPE_STOP    = 0;

    /**
     * Message type: message (normal).
     *
     * @var \Hoa\Worker\Backend\Shared int
     */
    const TYPE_MESSAGE = 1;

    /**
     * Socketable object.
     *
     * @var \Hoa\Socket\Socketable object
     */
    protected $_socket   = null;

    /**
     * Worker ID.
     *
     * @var \Hoa\Worker\Backend\Shared string
     */
    protected $_wid      = null;

    /**
     * Listeners.
     *
     * @var \Hoa\Core\Event\Listener object
     */
    protected $_on       = null;

    /**
     * Worker's password (needed to stop the worker).
     *
     * @var \Hoa\Worker\Backend\Shared string
     */
    protected $_password = null;



    /**
     * Construct a worker.
     *
     * @access  public
     * @param   mixed   $workerId    Worker ID or a socketable object (instance
     *                               of \Hoa\Socket\Socketable).
     * @param   string  $password    Worker's password.
     * @return  void
     * @throw   \Hoa\Worker\Exception
     * @throw   \Hoa\Worker\Backend\Exception
     */
    public function __construct ( $workerId, $password ) {

        if(   !is_string($workerId)
           && !($workerId instanceof \Hoa\Socket\Socketable))
            throw new Exception(
                'Either you give a worker ID or you give an object of type ' .
                '\Hoa\Socket\Connection\Client, but not anything else; given %s',
                0, is_object($workerId) ? get_class($workerId) : $workerId);

        if(is_string($workerId)) {

            $this->_wid = $workerId;
            $handle     = \Hoa\Worker\Run::get($workerId);
            $workerId   = $handle['socket'];
        }

        set_time_limit(0);

        $this->_socket   = $workerId;
        $this->_on       = new \Hoa\Core\Event\Listener($this, array('message'));
        $this->_password = sha1($password);

        return;
    }

    /**
     * Attach a callable to this listenable object.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $call          First callable part.
     * @param   mixed   $able          Second callable part (if needed).
     * @return  \Hoa\Worker\Backend\Shared
     * @throw   \Hoa\Core\Exception
     */
    public function on ( $listenerId, $call, $able = '' ) {

        return $this->_on->attach($listenerId, $call, $able);
    }

    /**
     * Run the shared worker.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Worker\Backend\Exception
     */
    public function run ( ) {

        $server = new \Hoa\Socket\Connection\Server($this->_socket);
        $server->connectAndWait();

        if(false === function_exists('fastcgi_finish_request'))
            throw new Exception(
                'This program (%s) must run behind PHP-FPM.', 1, __FILE__);

        fastcgi_finish_request();
        $_eom   = pack('c', 0);

        while(true) foreach($server->select() as $node) {

            $request = unpack('Cr', $server->read(1));
            $length  = unpack('Nl', $server->read(4));
            $message = $server->read($length['l']);
            $eom     = unpack('Ce', $server->read(1));

            if($eom['e'] != $_eom) {

                $server->disconnect();

                continue;
            }

            $server->disconnect();

            switch($request['r']) {

                case self::TYPE_MESSAGE:
                    $this->_on->fire('message', new \Hoa\Core\Event\Bucket(
                        unserialize($message)
                    ));
                  break;

                case self::TYPE_STOP:
                    if($this->_password === $message)
                        break 3;
                  break;
            }
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
     * @param   \Hoa\Socket\Socketable  $socket        Socketable object to
     *                                                 PHP-FPM server.
     * @param   string                  $workerPath    Path to the shared worker
     *                                                 program.
     * @return  bool
     */
    public static function start ( \Hoa\Socket\Socketable $socket,
                                   $workerPath ) {

        $server = new \Hoa\FastCgi\Responder(
            new \Hoa\Socket\Connection\Client($socket)
        );

        return $server->send(array(
            'GATEWAY_INTERFACE' => 'FastCGI/1.0',
            'SERVER_PROTOCOL'   => 'HTTP/1.1',
            'REQUEST_METHOD'    => 'GET',
            'REQUEST_URI'       => $workerPath,
            'SCRIPT_FILENAME'   => $workerPath,
            'SCRIPT_NAME'       => DS . dirname($workerPath)
        ));
    }

    /**
     * Stop the shared worker.
     *
     * @access  public
     * @return  bool
     */
    public function stop ( ) {

        $client = new \Hoa\Socket\Connection\Client($this->_socket);
        $client->connect();
        $client->writeAll(
            pack('C', self::TYPE_STOP) .
            pack('N', strlen($this->_password)) .
            $this->_password .
            pack('C', 0)
        );
        $client->disconnect();

        return true;
    }
}

}
