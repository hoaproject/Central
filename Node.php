<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Socket\Node
 */
-> import('Socket.Node');

}

namespace Hoa\Irc {

/**
 * Class \Hoa\Irc\Node.
 *
 * Describe a IRC node.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Node extends \Hoa\Socket\Node {

    /**
     * Whether this is basically the first message.
     *
     * @var \Hoa\Irc\Node bool
     */
    protected $_joined   = false;

    /**
     * Nickname.
     *
     * @var \Hoa\Irc\Node string
     */
    protected $_nickname = null;

    /**
     * Channel.
     *
     * @var \Hoa\Irc\Node string
     */
    protected $_channel  = null;



    /**
     * Whether the client has already joined a channel or not.
     *
     * @access  public
     * @param   bool  $joined    Joined or not.
     * @return  bool
     */
    public function setJoined ( $joined ) {

        $old           = $this->_joined;
        $this->_joined = $joined;

        return $old;
    }

    /**
     * Whether the client has already joined a channel or not.
     *
     * @access  public
     * @return  bool
     */
    public function hasJoined ( ) {

        return $this->_joined;
    }

    /**
     * Set nickname.
     *
     * @access  public
     * @param   string  $nickname    Nickname.
     * @return  string
     */
    public function setNickname ( $nickname ) {

        $old             = $this->_nickname;
        $this->_nickname = $nickname;

        return $old;
    }

    /**
     * Get nickname.
     *
     * @access  public
     * @return  string
     */
    public function getNickname ( ) {

        return $this->_nickname;
    }

    /**
     * Set current channel.
     *
     * @access  public
     * @param   string  $channel    Channel.
     * @return  string
     */
    public function setChannel ( $channel ) {

        $old            = $this->_channel;
        $this->_channel = $channel;

        return $old;
    }

    /**
     * Get current channel.
     *
     * @access  public
     * @return  string
     */
    public function getChannel ( ) {

        return $this->_channel;
    }
}

}
