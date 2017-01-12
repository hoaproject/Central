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

namespace Hoa\Worker;

use Hoa\Socket;

/**
 * Class \Hoa\Worker\Shared.
 *
 * Worker frontend, user's API.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Shared
{
    /**
     * Client.
     *
     * @var \Hoa\Socket\Client
     */
    protected $_client = null;



    /**
     * Build a worker pipe.
     *
     * @param   mixed   $workerId    Worker ID or a socket client (i.e. a
     *                               \Hoa\Socket\Client object).
     * @throws  \Hoa\Worker\Exception
     */
    public function __construct($workerId)
    {
        if (is_string($workerId)) {
            $wid           = Run::get($workerId);
            $this->_client = new Socket\Client($wid['socket']);

            return;
        } elseif ($workerId instanceof Socket\Client) {
            $this->_client = $workerId;

            return;
        }

        throw new Exception(
            'Either you give a worker ID or you give an object of type ' .
            '\Hoa\Socket\Client, but not anything else; given %s',
            0,
            is_object($workerId)
                ? get_class($workerId)
                : $workerId
        );

        return;
    }

    /**
     * Post a message to the shared worker.
     *
     * @param   mixed   $message    Message (everything you want).
     * @return  void
     */
    public function postMessage($message)
    {
        $this->_client->connect();
        $this->_client->writeAll(Backend\Shared::pack(
            Backend\Shared::TYPE_MESSAGE,
            $message
        ));
        $this->_client->disconnect();

        return;
    }

    /**
     * Get information about the shared worker.
     *
     * @return  array
     */
    public function getInformation()
    {
        $this->_client->connect();
        $this->_client->writeAll(
            Backend\Shared::pack(
                Backend\Shared::TYPE_INFORMATION,
                "\0"
            )
        );
        $this->_client->read(2); // skip type.
        $length  = unpack('Nl', $this->_client->read(4));
        $message = $this->_client->read($length['l']);
        $this->_client->read(1); // skip eom.
        $this->_client->disconnect();

        return unserialize($message);
    }
}
