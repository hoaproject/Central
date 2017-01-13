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

namespace Hoa\Mail\Test\Unit\Content;

use Hoa\Mail\Content\Message as SUT;
use Hoa\Mail\Content\Text;
use Hoa\Test;

/**
 * Class \Hoa\Mail\Test\Unit\Content\Message.
 *
 * Test suite of the message content.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Message extends Test\Unit\Suite
{
    public function case_content_type()
    {
        $this
            ->when($result = new SUT())
            ->then
                ->string($result['content-type'])
                    ->isEqualTo('multipart/mixed');
    }

    public function case_date()
    {
        $this
            ->when(
                $this->function->date = function () use (&$date) {
                    return $date = date('r', 42);
                },
                $result = new SUT()
            )
            ->then
                ->string($result['date'])
                    ->isEqualTo($date);
    }

    public function case_add_content()
    {
        $this
            ->given(
                $message  = new SUT(),
                $content1 = new Text('foo'),
                $content2 = new Text('bar'),
                $message  = new SUT()
            )
            ->when(
                $result1 = $message->addContent($content1),
                $result2 = $message->addContent($content2)
            )
            ->then
                ->object($result1)
                    ->isIdenticalTo($result2)
                ->object($result2)
                    ->isIdenticalTo($message)
                ->integer(count($message->getContent()))
                    ->isEqualTo(2);
    }

    public function case_get_recipients_only_to()
    {
        $this
            ->given(
                $recipient     = 'gordon@hoa-project.net',
                $message       = new SUT(),
                $message['to'] = $recipient
            )
            ->when($result = $message->getRecipients())
            ->then
                ->array($result)
                    ->isEqualTo([
                        $recipient
                    ]);
    }

    public function case_get_recipients_to_and_cc()
    {
        $this
            ->given(
                $recipient1    = 'gordon@hoa-project.net',
                $recipient2    = 'alix@hoa-project.net',
                $recipient3    = 'g-man@hoa-project.net',
                $message       = new SUT(),
                $message['to'] = $recipient1,
                $message['cc'] = $recipient2 . ', ' . $recipient3
            )
            ->when($result = $message->getRecipients())
            ->then
                ->array($result)
                    ->isEqualTo([
                        $recipient1,
                        $recipient2,
                        $recipient3
                    ]);
    }

    public function case_get_recipients_to_and_bcc()
    {
        $this
            ->given(
                $recipient1     = 'gordon@hoa-project.net',
                $recipient2     = 'alix@hoa-project.net',
                $recipient3     = 'g-man@hoa-project.net',
                $message        = new SUT(),
                $message['to']  = $recipient1,
                $message['bcc'] = $recipient2 . ', ' . $recipient3
            )
            ->when($result = $message->getRecipients())
            ->then
                ->array($result)
                    ->isEqualTo([
                        $recipient1,
                        $recipient2,
                        $recipient3
                    ]);
    }

    public function case_get_recipients_to_cc_and_bcc()
    {
        $this
            ->given(
                $recipient1     = 'gordon@hoa-project.net',
                $recipient2     = 'alix@hoa-project.net',
                $recipient3     = 'g-man@hoa-project.net',
                $message        = new SUT(),
                $message['to']  = $recipient1,
                $message['cc']  = $recipient2,
                $message['bcc'] = $recipient3
            )
            ->when($result = $message->getRecipients())
            ->then
                ->array($result)
                    ->isEqualTo([
                        $recipient1,
                        $recipient2,
                        $recipient3
                    ]);
    }

    public function case_get_recipents_with_description()
    {
        $this
            ->given(
                $recipient     = '"Gordon Freeman" gordon@hoa-project.net',
                $message       = new SUT(),
                $message['to'] = $recipient
            )
            ->when($result = $message->getRecipients())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'gordon@hoa-project.net'
                    ]);
    }

    public function case_no_content()
    {
        $this
            ->given($message = new SUT())
            ->exception(function () use ($message) {
                $message->getFormattedContent();
            })
                ->isInstanceOf('Hoa\Mail\Exception');
    }

    public function case_one_content()
    {
        $this
            ->given(
                $this->function->date = function () use (&$date) {
                    return $date = date('r', 42);
                },
                $content = new Text('foo'),
                $message = new SUT(),
                $message->addContent($content)
            )
            ->when($result = $message->getFormattedContent())
            ->then
                ->string($result)
                    ->isEqualTo(
                        'date: ' . $date . CRLF .
                        'content-transfer-encoding: quoted-printable' . CRLF .
                        'content-disposition: inline' . CRLF .
                        'content-type: text/plain; charset=utf-8' . CRLF .
                        CRLF .
                        'foo'
                    );
    }

    public function case_many_contents()
    {
        $this
            ->given(
                $this->function->date = function () use (&$date) {
                    return $date = date('r', 42);
                },
                $this->function->microtime = function () use (&$microtime) {
                    return $microtime = 42;
                },
                $content1 = new Text('foo'),
                $content2 = new Text('bar'),
                $content3 = new Text('baz'),
                $content4 = new Text('qux'),
                $message  = new SUT(),
                $message->addContent($content1),
                $message->addContent($content2),
                $message->addContent($content3),
                $message->addContent($content4)
            )
            ->when($result = $message->getFormattedContent())
            ->then
                ->string($result)
                    ->isEqualTo(
                        'content-type: multipart/mixed; boundary="__bndry-889c9d2eee9fc547c03ab71ac5c93db3"' . CRLF .
                        'date: Thu, 01 Jan 1970 01:00:42 +0100' . CRLF .
                        CRLF .

                        // Content 1.
                        '--__bndry-889c9d2eee9fc547c03ab71ac5c93db3' . CRLF .
                        $content1->getFormattedContent() . CRLF .

                        // Content 2.
                        '--__bndry-889c9d2eee9fc547c03ab71ac5c93db3' . CRLF .
                        $content2->getFormattedContent() . CRLF .

                        // Content 3.
                        '--__bndry-889c9d2eee9fc547c03ab71ac5c93db3' . CRLF .
                        $content3->getFormattedContent() . CRLF .

                        // Content 4.
                        '--__bndry-889c9d2eee9fc547c03ab71ac5c93db3' . CRLF .
                        $content4->getFormattedContent() . CRLF .

                        '--__bndry-889c9d2eee9fc547c03ab71ac5c93db3' .
                        '--' . CRLF
                    );
    }
}
