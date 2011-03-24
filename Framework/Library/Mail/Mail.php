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
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
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
