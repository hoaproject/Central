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
 * \Hoa\Worker\Exception
 */
-> import('Worker.Exception')

/**
 * \Hoa\Worker\Run
 */
-> import('Worker.Run')

/**
 * \Hoa\Socket\Connection\Client
 */
-> import('Socket.Connection.Client');

}

namespace Hoa\Worker {

/**
 * Class \Hoa\Worker\Shared.
 *
 * Worker frontend, user's API.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Shared {

    /**
     * Client to worker.
     *
     * @var \Hoa\Socket\Connection\Client object
     */
    protected $_client = null;



    /**
     * Build a worker pipe.
     *
     * @access  public
     * @param   mixed   $workerId    Worker ID or an object of type
     *                               \Hoa\Socket\Connection\Client.
     * @return  void
     * @throw   \Hoa\Worker\Exception
     */
    public function __construct ( $workerId ) {

        if(is_string($workerId)) {

            $this->_client = new \Hoa\Socket\Connection\Client(
                Run::get($workerId)
            );

            return;
        }
        elseif($workerId instanceof \Hoa\Socket\Connection\Client) {

            $this->_client = $workerId;

            return;
        }

        throw new Exception(
            'Either you give a worker ID or you give an object of type ' .
            '\Hoa\Socket\Connection\Client, but not anything else; given %s',
            0, is_object($workerId) ? get_class($workerId) : $workerId);

        return;
    }

    /**
     * Post a message to the worker.
     *
     * @access  public
     * @param   mixed   $message    Message (everything you want).
     * @return  void
     */
    public function postMessage ( $message ) {

        $message = serialize($message);
        $this->_client->connect();
        $this->_client->writeAll(
            pack('C', 1) .
            pack('N', strlen($message)) .
            $message .
            pack('C', 0)
        );
        $this->_client->disconnect();

        return;
    }
}

}
