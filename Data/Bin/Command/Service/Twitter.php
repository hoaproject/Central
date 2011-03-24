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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Stream\Context
 */
-> import('Stream.Context')

/**
 * \Hoa\File\Read
 */
-> import('File.Read');

/**
 * Class TwitterCommand.
 *
 * Send a short tweet.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.1
 */

class TwitterCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var TwitterCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var TwitterCommand string
     */
    protected $programName = 'Twitter';

    /**
     * Options description.
     *
     * @var TwitterCommand array
     */
    protected $options     = array(
        array('username', parent::REQUIRED_ARGUMENT, 'u'),
        array('password', parent::REQUIRED_ARGUMENT, 'p'),
        array('help',     parent::NO_ARGUMENT,       'h'),
        array('help',     parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $username = null;
        $password = null;

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'u':
                    $username = $v;
                  break;

                case 'p':
                    $password = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        parent::listInputs($message);

        if(null === $message)
            return $this->usage();

        if(strlen($message) > 140)
            throw new \Hoa\Console\Exception(
                'Message length must be lesser than 140 (given %d).',
                1, strlen($message));

        if(null === $username)
            return $this->usage();

        if(null === $password)
            $password = cin('Password:', \Hoa\Console\Core\Io::TYPE_PASSWORD);

        \Hoa\Stream\Context::getInstance('twitter', 'http')->addOptions(array(
            'method'  => 'POST',
            'header'  => 'Authorization: Basic ' .
                         base64_encode($username . ':' . $password) . "\r\n" .
                         'Content-type: application/x-www-form-urlencoded' . "\r\n",
            'content' => 'status=' . urlencode($message),
            'timeout' => 30
        ));

        try {

            new \Hoa\File\Read(
                'http://twitter.com/statuses/update.xml',
                \Hoa\File::MODE_READ,
                'twitter'
            );
        }
        catch ( \Hoa\File\Exception $e ) {

            throw new \Hoa\Console\Exception(
                $e->getFormattedMessage(),
                $e->getCode()
            );
        }

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : service:twitter <options> message');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'u'    => 'Username (required).',
            'p'    => 'Password.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
