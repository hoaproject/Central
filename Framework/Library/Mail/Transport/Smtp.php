<?php

/**
 * Hoa Framework
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
