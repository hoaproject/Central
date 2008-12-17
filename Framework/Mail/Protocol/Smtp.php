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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Mail_Protocol_Smtp
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Mail_Protocol_Exception
 */
import('Mail.Protocol.Exception');

/**
 * Hoa_Mail_Protocol_Abstract
 */
import('Mail.Protocol.Abstract');

/**
 * Class Hoa_Mail_Protocol_Smtp.
 *
 * SMTP protocol manager.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Protocol_Smtp
 */

class Hoa_Mail_Protocol_Smtp extends Hoa_Mail_Protocol_Abstract {

    /**
     * Hostame or IP of remote server.
     *
     * @var Hoa_Mail_Protocol_Smtp string
     */
    protected $host = '127.0.0.1';

    /**
     * Port number.
     *
     * @var Hoa_Mail_Protocol_Smtp string
     */
    protected $port = null;

    /**
     * Connection time out.
     *
     * @var Hoa_Mail_Protocol_Smtp int
     */
    protected $timeout = 30;

    /**
     * Indicates a MAIL FROM command has been issued.
     *
     * @var Hoa_Mail_Protocol_Smtp bool
     */
    private $_mail = false;

    /**
     * Indicates a RCPT TO command has been issued.
     *
     * @var Hoa_Mail_Protocol_Smtp bool
     */
    private $_rcpt = false;

    /**
     * Indicates a DATA command has been issued.
     *
     * @var Hoa_Mail_Protocol_Smtp bool
     */
    private $_data = false;

    /**
     * HELP command result.
     *
     * @var Hoa_Mail_Protocol_Smtp string
     */
    protected $cmdList = '';



    /**
     * __construct
     * Initialize connection to remote server.
     * See RFC 2821 for the basic specification of SMTP.
     * See also RFC 1123 for important additional informations.
     * And see RFC 1893 and 2034 for information about enhanced status codes.
     *
     * @access  public
     * @param   host     string    Hostname or IP of remote server.
     * @param   port     int       The port to connection.
     * @param   timeout  int       Stream time out.
     * @return  void
     * @throw   Hoa_Socket_Exception
     */
    public function __construct ( $host = '127.0.0.1', $port = null, $timeout = 30 ) {

        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;

        parent::connect($host, $port, $timeout); // place into $this->stream

        $this->help();
    }

    /**
     * _help
     * Send a HELP command.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Socket_Exception
     */
    public function help ( ) {

        try {
            $this->cmdList = $this->stream->send('HELP');

            $this->stream->except(211, 214);
        }
        catch ( Hoa_Socket_Exception $e ) {
            $this->cmdList = '';
        }

        return $this->cmdList;
    }

    /**
     * cmdExists
     * Check if the command is enabled on the remote server.
     *
     * @access  public
     * @param   cmd     string    Command to check.
     * @return  bool
     */
    public function cmdExists ( $cmd ) {

        if(empty($this->cmdList))
            return true;

        return !(false === strpos($this->cmdList, $cmd));
    }

    /**
     * ehlo
     * Send a EHLO command.
     *
     * @access  public
     * @param   who     string    Who says EHLO ?
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function ehlo ( $who = null ) {

        if(!$this->cmdExists('EHLO'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                0, array('EHLO', $this->host));

        if(empty($who))
            $who = $this->host;

        $out = $this->stream->send('EHLO ' . $who);
        $this->stream->except(250);

        $this->_mail = false;
        $this->_rcpt = false;
        $this->_data = false;

        return $out;
    }

    /**
     * mail
     * Send a MAIL FROM command.
     *
     * @access  public
     * @param   mail    string    Mail address from.
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function mail ( $mail ) {

        if(!$this->cmdExists('MAIL'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                1, array('MAIL', $this->host));

        if(preg_match('#(<.*?>)#', $mail, $matches))
            $mail = $matches[1];

        $out = $this->stream->send('MAIL FROM: ' . $mail);
        $this->stream->except(250);

        $this->_mail = true;
        $this->_rcpt = false;
        $this->_data = false;

        return $out;
    }

    /**
     * rcpt
     * Send a RCPT TO command.
     *
     * @access  public
     * @param   mail    string    Mail address to.
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function rcpt ( $mail ) {

        if(!$this->cmdExists('RCPT'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                2, array('RCPT', $this->host));

        if(preg_match('#(<.*?>)#', $mail, $matches))
            $mail = $matches[1];

        $out = $this->stream->send('RCPT TO: ' . $mail);
        $this->stream->except(250, 251);

        $this->_rcpt = true;
        $this->_data = false;

        return $out;
    }

    /**
     * data
     * Send a DATA command.
     *
     * @access  public
     * @param   data    string    Data to send.
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function data ( $data ) {

        if(!$this->cmdExists('DATA'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                3, array('DATA', $this->host));

        if($this->_rcpt !== true)
            throw new Hoa_Mail_Protocol_Exception(
                'No sender reverse path has been supplied', 4);

        $out  = $this->stream->send('DATA')."\n";
        $this->stream->except(354);

        $data = explode(CRLF, $data);
        foreach($data as $line) {
            if(strpos($line, '.') === 0)
                $line .= '.' . $line;
            $out .= $this->stream->send($line)."\n";
        }

        $out .= $this->stream->send(CRLF . '.');
        $this->stream->except(250);

        $this->_data = true;
    }

    /**
     * rset
     * Send a RESET command.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function rset ( ) {

        if(!$this->cmdExists('RSET'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                5, array('RSET', $this->host));

        $out = $this->stream->send('RSET');
        $this->stream->except(250, 251);

        $this->_mail = false;
        $this->_rcpt = false;
        $this->_data = false;

        return $out;
    }

    /**
     * vrfy
     * Send a VRFY command.
     *
     * @access  public
     * @param   user    string    User to verify.
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function vrfy ( $user ) {

        if(!$this->cmdExists('VRFY'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                6, array('VRFY', $this->host));

        $out = $this->stream->send('VRFY ' . $user);
        $this->stream->except(250, 252);

        return $out;
    }

    /**
     * noop
     * Send a NOOP command.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Mail_Protocol_Exception
     * @throw   Hoa_Socket_Exception
     */
    public function noop ( ) {

        if(!$this->cmdExists('NOOP'))
            throw new Hoa_Mail_Protocol_Exception(
                'Command %s is not enabled by the remote server %s',
                7, array('NOOP', $this->host));

        $out = $this->stream->send('NOOP');
        $this->stream->except(250);

        return $out;
    }

    /**
     * quit
     * Send a QUIT command.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Socket_Exception
     */
    public function quit ( ) {

        $out = $this->stream->send('QUIT');
        $this->stream->except(221);

        return $out;
    }

    /**
     * cmd
     * Send an other command.
     *
     * @access  public
     * @param   cmd       string    The command to send.
     * @param   codeLow   int       Range low except code.
     * @param   codeHigh  int       Range high except code.
     * @return  string
     * @throw   Hoa_Socket_Exception
     */
    public function cmd ( $cmd, $codeLow = 250, $codeHigh = null ) {

        $out = $this->stream->send($cmd);
        $this->stream->except($codeLow, $codeHigh);

        return $out;
    }
}
