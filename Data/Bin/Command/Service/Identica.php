<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 * Class IdenticaCommand.
 *
 * Send a short tweet.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class IdenticaCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var IdenticaCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var IdenticaCommand string
     */
    protected $programName = 'Identica';

    /**
     * Options description.
     *
     * @var IdenticaCommand array
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

        \Hoa\Stream\Context::getInstance('identica', 'http')->addOptions(array(
            'method'  => 'POST',
            'header'  => 'Authorization: Basic ' .
                         base64_encode($username . ':' . $password) . "\r\n" .
                         'Content-type: application/x-www-form-urlencoded' . "\r\n",
            'content' => 'status=' . urlencode($message),
            'timeout' => 30
        ));

        try {

            new \Hoa\File\Read(
                'http://identi.ca/api/statuses/update.xml',
                \Hoa\File::MODE_READ,
                'identica'
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

        cout('Usage   : service:identica <options> message');
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
