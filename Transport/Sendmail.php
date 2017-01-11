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

namespace Hoa\Mail\Transport;

use Hoa\Mail;

/**
 * Class \Hoa\Mail\Transport\Sendmail.
 *
 * This class allows to send an email by using sendmail (through the PHP mail()
 * function).
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Sendmail implements ITransport\Out
{
    /**
     * Additional parameters for the mail() function.
     *
     * @var array
     */
    protected $_parameters = null;



    /**
     * Constructor.
     *
     * @param   array  $parameters    Additional parameters for the mail()
     *                                function.
     */
    public function __construct(array $parameters = [])
    {
        $this->_parameters = $parameters;

        return;
    }

    /**
     * Set additional parameters.
     *
     * @param   array  $parameters    Additional parameters.
     * @return  array
     */
    protected function setParameters(array $parameters)
    {
        $old               = $this->_parameters;
        $this->_parameters = $parameters;

        return $old;
    }

    /**
     * Get additional parameters.
     *
     * @return  array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Send a message.
     *
     * @param   \Hoa\Mail\Message  $message    Message.
     * @return  bool
     */
    public function send(Mail\Message $message)
    {
        $content  = $message->getFormattedContent();
        $headers  = $message->getHeaders();
        $pos      = strpos($content, CRLF . CRLF);
        $_headers = substr($content, 0, $pos);
        $_body    = substr($content, $pos + 4);

        return mail(
            $headers['to'],
            $headers['subject'],
            $_body,
            $_headers,
            $message->formatHeaders($this->getParameters())
        );
    }
}
