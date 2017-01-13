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

namespace Hoa\Mail\Content;

use Hoa\Mail;

/**
 * Class \Hoa\Mail\Content\Message.
 *
 * This class represents a message, that can also be a content of another
 * message.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Message extends Content
{
    /**
     * Boundary hash prefix.
     *
     * @const string
     */
    const BOUNDARY = '@hoaproject-';

    /**
     * Content.
     *
     * @var array
     */
    protected $_content = [];



    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this['content-type'] = 'multipart/mixed';
        $this['date']         = date('r');

        return;
    }

    /**
     * Add a content part.
     *
     * @param   \Hoa\Mail\Content  $content    Content part.
     * @return  \Hoa\Mail\Content\Message
     */
    public function addContent(Content $content)
    {
        $this->_content[] = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return  array
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Get final “plain” content.
     *
     * @return  string
     * @throws  \Hoa\Mail\Exception
     */
    protected function _getContent()
    {
        $content = $this->getContent();

        if (empty($content)) {
            throw new Mail\Exception('The message does not have content.', 0);
        }

        if (1 < count($content)) {
            $boundary =
                '__bndry-' .
                md5(static::BOUNDARY . microtime(true));
            $frontier = '--' . $boundary;

            $this['content-type'] =
                $this['content-type'] . '; ' .
                'boundary="' . $boundary . '"';

            $message = static::formatHeaders($this->getHeaders()) . CRLF;

            foreach ($content as $c) {
                $message .= $frontier . CRLF . $c . CRLF;
            }

            $message .= $frontier . '--' . CRLF;
        } else {
            $oldContentType = $this['content-type'];
            unset($this['content-type']);

            $message =
                static::formatHeaders($this->getHeaders()) .
                current($content);

            $this['content-type'] = $oldContentType;
        }

        return $message;
    }

    /**
     * Get all recipients of the message.
     * The first recipient (index 0) is `$this['to']`.
     *
     * @return  array
     */
    public function getRecipients()
    {
        $out = [];

        $this->_getRecipients($this['to'], $out);

        if (isset($this['cc'])) {
            $this->_getRecipients($this['cc'], $out);
        }

        if (isset($this['bcc'])) {
            $this->_getRecipients($this['bcc'], $out);
        }

        return $out;
    }

    /**
     * Get recipients from a specific line (value of a header).
     *
     * @param   string  $line    Line.
     * @param   array   &$out    Out.
     * @return  void
     */
    protected function _getRecipients($line, array &$out)
    {
        $line = preg_replace_callback(
            '#("|\')[^\1]+\1#',
            function ($matches) {
                return '';
            },
            $line
        );

        foreach (explode(',', $line) as $contact) {
            $out[] = static::getAddress($contact);
        }

        return;
    }

    /**
     * Override the parent::getFormattedContent method.
     *
     * @param   bool  $headers    With headers or not (forced to false here).
     * @return  string
     */
    public function getFormattedContent($headers = false)
    {
        return parent::getFormattedContent(false);
    }
}
