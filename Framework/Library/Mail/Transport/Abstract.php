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
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
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
