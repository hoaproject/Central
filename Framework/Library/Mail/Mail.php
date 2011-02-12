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
 *
 */

/**
 * Hoa_Mail_Exception
 */
import('Mail.Exception');

/**
 * Hoa_Mail_Mime
 */
import('Mail.Mime');

/**
 * Class Hoa_Mail.
 *
 * Superclass :
 * Send an email with differents methods.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Mail
 */

class Hoa_Mail extends Hoa_Mail_Mime {

    /**
     * Type of sender.
     *
     * @var Hoa_Mail object
     */
    protected $defaultSender = null;

    /**
     * Built mail.
     *
     * @var Hoa_Mail_Mime array
     */
    protected $data = array();



    /**
     * __construct
     * Prepare Mime.
     *
     * @access  public
     * @param   charset   string    Charset.
     * @param   eol       string    End-Of-Line sequence.
     * @param   priority  int       X-Priority level.
     * @return  void
     * @throw   Hoa_Mail_Transport_Exception
     */
    public function __construct ( $charset = 'UTF-8', $eol = CRLF, $priority = 3 ) {

        parent::__construct($charset, $eol, $priority);
    }

    /**
     * setDefaultSender
     * Set default sender.
     *
     * @access  public
     * @param   sender  objet    The sender.
     * @return  object
     */
    public function setDefaultSender ( $sender ) {

        if($sender instanceof Hoa_Mail_Transport_Abstract)
            return $this->defaultSender = $sender;
        else
            throw new Hoa_Mail_Exception('Sender must be an instance of Hoa_Mail_Sender_Abstract', 0);
    }


    /**
     * send
	 * Send an email with different method.
	 * For more documentation, see RFC 2821.
     *
     * @access  public
     * @param   arg     array    Methode argument.
     * @return  bool
     */
    public function send ( Array $arg = array() ) {

        $this->data = $this->get();
        return $this->defaultSender->sendMail($this->data, $arg);
    }
}
