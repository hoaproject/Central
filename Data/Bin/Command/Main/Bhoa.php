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
 * \Hoa\Socket\Connection\Server
 */
-> import('Socket.Connection.Server')

/**
 * \Hoa\Socket\Connection\Client
 */
-> import('Socket.Connection.Client')

/**
 * \Hoa\Socket\Internet\DomainName
 */
-> import('Socket.Internet.DomainName')

/**
 * \Hoa\FastCgi\Client
 */
-> import('FastCgi.Client')

/**
 * \Hoa\File\Read
 */
-> import('File.Read')

/**
 * \Hoa\Http\Request
 */
-> import('Http.Request')

/**
 * \Hoa\Mime
 */
-> import('Mime.~');

/**
 * Class StartCommand.
 *
 * A damn stupid and very very simple HTTP server (just for fun).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class BhoaCommand extends \Hoa\Console\Command\Generic {

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
    protected $programName = 'Bhoa';

    /**
     * Options description.
     *
     * @var BhoaCommand array
     */
    protected $options     = array(
        array('domain',         parent::REQUIRED_ARGUMENT, 'd'),
        array('port',           parent::REQUIRED_ARGUMENT, 'p'),
        array('root',           parent::REQUIRED_ARGUMENT, 'r'),
        array('fastcgi-domain', parent::REQUIRED_ARGUMENT, 'D'),
        array('fastcgi-port',   parent::REQUIRED_ARGUMENT, 'P'),
        array('help',           parent::NO_ARGUMENT,       'h'),
        array('help',           parent::NO_ARGUMENT,       '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $domain  = 'localhost';
        $port    = 8888;
        $fdomain = 'localhost';
        $fport   = 9000;
        $root    = 'hoa://Application/Public/';
        $php     = $this->getParameter('command.php');

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'd':
                    $domain = $v;
                  break;

                case 'p':
                    $port = (int) $v;
                  break;

                case 'D':
                    $fdomain = $v;
                  break;

                case 'P':
                    $fport = (int) $v;
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

        $server  = new \Hoa\Socket\Connection\Server(
                       new \Hoa\Socket\Internet\DomainName(
                           $domain,
                           $port,
                           'tcp'
                       )
                   );
        $client  = new \Hoa\FastCgi\Client(
                       new \Hoa\Socket\Connection\Client(
                           new \Hoa\Socket\Internet\DomainName(
                               $fdomain,
                               $fport,
                               'tcp'
                           )
                       )
                   );
        $server->connectAndWait();
        $request = new \Hoa\Http\Request();
        $_root   = $root;
        $time    = time();

        if('hoa://' == substr($_root, 0, 6))
            $_root = resolve($_root);
        else
            $_root = $root = realpath($root);

        cout('Server is up, on ' . $server->getSocket() . '!');
        cout('Root: ' . $root . '.');
        cout();

        $this->log('Waiting for connection…');

        while(true) foreach($server->select() as $node) {

            $buffer = $server->read(2048);

            if(empty($buffer)) {

                $server->disconnect();

                continue;
            }

            $request->parse($buffer);
            $method         = $request->getMethod();
            $methodAsString = strtoupper($request->getMethodAsString());
            $url            = $request->getURL();
            $ttime          = time();
            $smartPrint     = "\r";

            if($ttime - $time >= 2) {

                $this->log("\r");
                $smartPrint = "\n";
            }

            $this->log(
                $smartPrint . '↺ '. $methodAsString . ' /' . $url .
                ' (waiting…)'
            );

            $time = $ttime;

            switch($method) {

                case \Hoa\Http\Request::METHOD_GET:
                    try {

                        $file = new \Hoa\File\Read($_root . DS . $url);
                        $idx  = file_exists($_root . DS . 'index.php');

                        if(true === $file->isDirectory() && false === $idx) {

                            $server->writeAll(
                                'HTTP/1.1 200 OK' . "\r\n" .
                                'Date: ' . date('r') . "\r\n" .
                                'Server: Hoa+Bhoa/0.1' . "\r\n" .
                                'Content-Type: text/plain' . "\r\n" .
                                'Content-Length: 1' . "\r\n\r\n" .
                                'd'
                            );

                            break;
                        }

                        if(true === $file->isFile()) {

                            try {

                                $mime     = new \Hoa\Mime($file);
                                $mimeType = $mime->getMime();
                            }
                            catch ( \Hoa\Mime\Exception\MimeIsNotFound $e ) {

                                $mimeType = 'application/octet-stream';
                            }

                            $server->writeAll(
                                'HTTP/1.1 200 OK' . "\r\n" .
                                'Date: ' . date('r') . "\r\n" .
                                'Server: Hoa+Bhoa/0.1' . "\r\n" .
                                'Content-Type: ' . $mimeType . "\r\n" .
                                'Content-Length: ' . $file->getSize() . "\r\n\r\n" .
                                $file->readAll()
                            );

                            break;
                        }

                        if(false === $idx) {

                            $server->writeAll(
                                'HTTP/1.1 404 Not Found' . "\r\n" .
                                'Date: ' . date('r') . "\r\n" .
                                'Server: Hoa+Bhoa/0.1' . "\r\n" .
                                'Content-Type: text/plain' . "\r\n" .
                                'Content-Length: 3' . "\r\n\r\n" .
                                '404'
                            );

                            break;
                        }

                        throw new \Hoa\File\Exception\FileDoesNotExist(
                            'Yup', 42
                        );
                    }
                    catch ( \Hoa\File\Exception\FileDoesNotExist $e ) {

                        try {

                            $content = $client->send(array(
                                'GATEWAY_INTERFACE' => 'FastCGI/1.0',

                                'SERVER_SOFTWARE'   => 'Hoa+Bhoa/0.1',
                                'SERVER_PROTOCOL'   => 'HTTP/1.1',
                                'SERVER_NAME'       => 'localhost',
                                'SERVER_ADDR'       => '::1',
                                'SERVER_PORT'       => 8888,
                                'SERVER_SIGNATURE'  => 'Hoa Bhoa \o/',

                                'HTTP_HOST'         => 'localhost:8888',
                                'HTTP_USER_AGENT'   => 'Mozilla Firefox',

                                'REQUEST_METHOD'    => 'GET',
                                'REQUEST_URI'       => '/' . $url,

                                'SCRIPT_FILENAME'   => $_root . DS . 'index.php',
                                'SCRIPT_NAME'       => '/index.php',

                                'CONTENT_TYPE'      => 'text/html',
                                'CONTENT_LENGTH'    => 0
                            ));
                        }
                        catch ( \Hoa\Socket\Exception $ee ) {

                            $socket  = $client->getClient()->getSocket();
                            $listen  = $socket->getAddress() . ':' .
                                       $socket->getPort();
                            $this->log("\r" . '✖ ' . $methodAsString . ' /' .
                                       $url);
                            $this->log("\n" . '  ↳ PHP FastCGI seems to be ' .
                                       'disconnected (tried to reach ' .
                                       $socket . ').' . "\n" .
                                       '  ↳ Try $ php-cgi -b ' . $listen . "\n" .
                                       '     or $ php-fpm -d listen=' . $listen);
                            $this->log(null);

                            continue 2;
                        }

                        $headers = $client->getResponseHeaders();

                        $server->writeAll(
                            'HTTP/1.1 200 OK' . "\r\n" .
                            'Date: ' . date('r') . "\r\n" .
                            'Server: Hoa+Bhoa/0.1' . "\r\n" .
                            'Content-Type: ' . $headers['content-type'] . "\r\n" .
                            'Content-Length: ' . strlen($content) . "\r\n\r\n" .
                            $content
                        );
                    }
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

            $this->log("\r" . '✔ '. $methodAsString . ' /' . $url);

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

        $l = max($l, mb_strlen($message) + 1);

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

        cout('Usage   : main:bhoa <options>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'd'    => 'Domain name (default: localhost).',
            'p'    => 'Port number (default: 8888).',
            'D'    => 'PHP FastCGI or PHP-FPM domain name (default: localhost).',
            'P'    => 'PHP FastCGI or PHP-FPM port number (default: 9000).',
            'r'    => 'Public/document root.',
            'help' => 'This help.'
        )));
        cout('To start PHP FastCGI:' . "\n" .
             '    $ php-cgi -b localhost:9000' . "\n" .
             'or' . "\n" .
             '    $ php-fpm -d listen=localhost:9000');

        return HC_SUCCESS;
    }
}

}
