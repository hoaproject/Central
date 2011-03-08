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
 * \Hoa\Socket\Connection\Server
 */
-> import('Socket.Connection.Server')

/**
 * \Hoa\Socket\Internet\DomainName
 */
-> import('Socket.Internet.DomainName')

/**
 * \Hoa\File\Read
 */
-> import('File.Read')

/**
 * \Hoa\Http\Request
 */
-> import('Http.Request');

/**
 * Class StartCommand.
 *
 * A damn stupid and very very simple HTTP server (just for fun).
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class StartCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var BhoaCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var BhoaCommand string
     */
    protected $programName = 'Start';

    /**
     * Options description.
     *
     * @var BhoaCommand array
     */
    protected $options     = array(
        array('domain', parent::REQUIRED_ARGUMENT, 'd'),
        array('port',   parent::REQUIRED_ARGUMENT, 'p'),
        array('root',   parent::REQUIRED_ARGUMENT, 'r'),
        array('help',   parent::NO_ARGUMENT,       'h'),
        array('help',   parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $domain = 'localhost';
        $port   = 8888;
        $root   = 'hoa://Application/Public/';
        $php    = $this->getParameter('command.php');

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'd':
                    $domain = $v;
                  break;

                case 'p':
                    $port = (int) $v;
                  break;

                case 'r':
                    $root = $v;
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        $ip      = new \Hoa\Socket\Internet\DomainName($domain, $port, 'tcp');
        $server  = new \Hoa\Socket\Connection\Server($ip);
        $server->connectAndWait();
        $request = new \Hoa\Http\Request();
        $_root   = $root;

        if('hoa://' == substr($_root, 0, 6))
            $_root = resolve($_root);

        cout('Server is up, on ' . $ip . '!');
        cout('Root: ' . $root);
        cout();

        $this->log("\n" . 'Waiting for connection…');

        while(true)
            foreach($server->select() as $node) {

                $buffer = $server->read(2048);

                if(empty($buffer)) {

                    $server->disconnect();

                    continue;
                }

                $request->parse($buffer);
                $method         = $request->getMethod();
                $methodAsString = strtoupper($request->getMethodAsString());
                $url            = $request->getURL();

                $this->log(
                    "\r" . '↺ '. $methodAsString . ' ' . $url .
                    ' (waiting…)'
                );

                switch($method) {

                    case \Hoa\Http\Request::METHOD_GET:
                        $path = $_root . DS . $url;

                        if(file_exists($path)) {

                            // I know, it's deprecated, but it's temporary.
                            $type    = mime_content_type($path);
                            $content = file_get_contents($path);
                            $server->writeAll(
                                'HTTP/1.1 200 OK' . "\r\n" .
                                'Date: ' . date('r') . "\r\n" .
                                'Server: Hoa+Bhoa/0.1' . "\r\n" .
                                'Content-Type: ' . $type . "\r\n" .
                                'Content-Length: ' . mb_strlen($content) . "\r\n\r\n" .
                                $content
                            );

                            break;
                        }

                        $_url = '"' . str_replace('"', '\"', $url) . '"';
                        $process = proc_open(
                            $php . ' index.php ' . $_url,
                            array(
                                0 => array('pipe', 'r'),
                                1 => array('pipe', 'w'),
                                2 => array('pipe', 'w')
                            ),
                            $pipes,
                            $_root,
                            $env
                        );
                        $content = stream_get_contents($pipes[1]);
                        fclose($pipes[1]);
                        proc_close($process);

                        $server->writeAll(
                            'HTTP/1.1 200 OK' . "\r\n" .
                            'Date: ' . date('r') . "\r\n" .
                            'Server: Hoa+Bhoa/0.1' . "\r\n" .
                            'Content-Type: text/html' . "\r\n" .
                            'Content-Length: ' . strlen($content) . "\r\n\r\n" .
                            $content
                        );
                      break;

                    default:
                        $content = 'This server is stupid and does not ' .
                                   'support ' . $methodAsString . '! ' .
                                   'Yup, damn stupid…';
                        $server->writeAll(
                            'HTTP/1.1 200 OK' . "\r\n" .
                            'Date: ' . date('r') . "\r\n" .
                            'Server: Hoa+Bhoa/0.1' . "\r\n" .
                            'Content-Type: text/html' . "\r\n" .
                            'Content-Length: ' . strlen($content) . "\r\n\r\n" .
                            $content
                        );
                }

                $this->log("\r" . '✓ '. $methodAsString . ' ' . $url);

                $this->log(null);
                $this->log("\n" . 'Waiting for new connection…');
            }

        return HC_SUCCESS;
    }

    /**
     * Just a server log :-). For fun only.
     *
     * @access  protected
     * @param   string     $message    Message.
     * @return  void
     */
    protected function log ( $message ) {

        static $l = 0;

        if(null === $message) {

            $l = 0;

            return;
        }

        $l = max($l, mb_strlen($message));

        cout(
            str_pad($message, $l, ' ', STR_PAD_RIGHT),
            \Hoa\Console\Core\Io::NO_NEW_LINE
        );

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : bhoa:start <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'd'    => 'Domain name.',
            'p'    => 'Port number.',
            'r'    => 'Public/document root.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
