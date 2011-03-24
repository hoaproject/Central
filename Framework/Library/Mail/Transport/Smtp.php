<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 *
 *
 * @category    Framework
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Transport_Smtp
 *
 */

/**
 * Hoa_Mail_Transport_Abstract
 */
import('Mail.Transport.Abstract');

/**
 * Hoa_Mail_Protocol_Smtp
 */
import('Mail.Protocol.Smtp');

/**
 * Class Hoa_Mail_Transport_Smtp.
 *
 * PHP Transport, with SMTP protocol.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Transport_Smtp
 */

class Hoa_Mail_Transport_Smtp extends Hoa_Mail_Transport_Abstract {

    /**
     * Connection to SMTP server.
     *
     * @var Hoa_Mail_Protocol_Smtp object
     */
    protected $connection = null;



    /**
     * __construct
     * Start a connection to SMTP server.
     *
     * @access  public
     * @param   host     string    Hostname or IP of remote server.
     * @param   port     int       The port to connection.
     * @param   timeout  int       Stream time out.
     * @return  void
     * @throw   Hoa_Mail_Protocol_Exception
     */
    public function __construct ( $host = '127.0.0.1', $port = null, $timeout = 30 ) {

        $this->connection = new Hoa_Mail_Protocol_Smtp($host, $port, $timeout);
    }

    /**
     * _sendMail
     * Send an email with SMTP protocol.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function _sendmail ( ) {

        $out  = $this->connection->ehlo('HOA Mail')                  . "\n";
        $out .= $this->connection->mail($this->from)                 . "\n";
        $out .= $this->connection->rcpt($this->to)                   . "\n";
        $out .= $this->connection->data($this->header . $this->body) . "\n";
        $out .= $this->connection->quit();

        $this->connection->disconnect();

        return $out;
    }
}
