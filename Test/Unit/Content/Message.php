<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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
 * @copyright  Copyright © 2007-2015 Hoa community
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
                $this->function->date = function() use (&$date) {
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
}
