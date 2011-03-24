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
 * @subpackage  Hoa_Mail_Transport_Abstract
 *
 */

/**
 * Hoa_Mail_Transport_Exception
 */
import('Mail.Transport.Exception');

/**
 * Class Hoa_Mail_Transport_Abstract.
 *
 * Abstract layer, common to all Transport.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Transport_Abstract
 */

abstract class Hoa_Mail_Transport_Abstract {

    /**
     * All datas.
     *
     * @var Hoa_Mail_Transport_Abstract array
     */
    protected $data = array();

    /**
     * All arguments.
     *
     * @var Hoa_Mail_Transport_Abstract array
     */
    protected $arg = array();

    /**
     * Headers.
     *
     * @var Hoa_Mail_Transport_Abstract array
     */
    protected $header = array();

    /**
     * To.
     *
     * @var Hoa_Mail_Transport_Abstract string
     */
    protected $to = '';

    /**
     * From.
     *
     * @var Hoa_Mail_Transport_Abstract string
     */
    protected $from = '';

    /**
     * Subject.
     *
     * @var Hoa_Mail_Transport_Abstract string
     */
    protected $subject = '';

    /**
     * Body.
     *
     * @var Hoa_Mail_Transport_Abstract string
     */
    protected $body = '';



    /**
     * sendMail
     * Send an email independent from the used Transport.
     */
    abstract protected function _sendMail ( );


    /**
     * sendMail
     * Prepare all variables and send an email.
     *
     * @access  public
     * @param   data    array    Methode data.
     * @param   arg     array    Methode argument.
     * @return  bool
     */
    public function sendMail ( Array $data, Array $arg ) {

        $this->data = $data;
        $this->arg  = $arg;

        $this->_prepareHeaders();
        $this->_prepareBody();

        return $this->_sendMail();
    }


    /**
     * _prepareHeaders
     * Prepare headers.
     *
     * @access  protected
     * @return  void
     */
    protected function _prepareHeaders ( ) {

        $this->header = $this->data['header'];
    }


    /**
     * _prepareBody
     * Prepare body variables.
     *
     * @access  protected
     * @return  void
     */
    protected function _prepareBody ( ) {

        $this->body    = $this->data['body'];
        $this->to      = $this->data['to'];
        $this->from    = $this->data['from'];
        $this->subject = $this->data['subject'];
    }
}
