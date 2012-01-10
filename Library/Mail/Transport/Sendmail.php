<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * @subpackage  Hoa_Mail_Transport_Sendmail
 *
 */

/**
 * Hoa_Mail_Transport_Abstract
 */
import('Mail.Transport.Abstract');

/**
 * Class Hoa_Mail_Transport_Sendmail.
 *
 * PHP Transport, with mail() function.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2012 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Mail
 * @subpackage  Hoa_Mail_Transport_Sendmail
 */

class Hoa_Mail_Transport_Sendmail extends Hoa_Mail_Transport_Abstract {

    /**
     * _sendMail
     * Send an email with Sendmail.
     *
     * @access  public
     * @return  bool
     * @throw   Hoa_Mail_Transport_Exception
     */
    public function _sendMail ( ) {

        if(!@mail($this->to,
                  $this->subject,
                  $this->body,
                  $this->header))
            throw new Hoa_Mail_Transport_Exception('Unable de send an email with Sendmail.', 0);

        return true;
    }

}
