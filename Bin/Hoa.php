<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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

/**
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 */

if(!defined('HOA'))
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Core.php';

Hoa\Core::enableErrorHandler();
Hoa\Core::enableExceptionHandler();

from('Hoa')
-> import('Router.Cli')
-> import('Dispatcher.Basic')
-> import('Console.Dispatcher.Kit')
-> import('Console.Cursor');

require __DIR__ . DS . 'Style' . DS . 'Basic.php';
$style = new \Hoa\Core\Bin\Style\Basic();
$style->import();

/**
 * Here we go…
 */
try {

    $router = new \Hoa\Router\Cli();
    $router->get(
        'g',
        '(?:(?<vendor>\w+)\s+)?(?<library>\w+)?(?::(?<command>\w+))?(?<_tail>.*?)',
        'main',
        'main',
        array(
            'vendor'  => 'hoa',
            'library' => 'core',
            'command' => 'welcome'
        )
    );

    $dispatcher = new \Hoa\Dispatcher\Basic(array(
        'synchronous.controller'
            => '(:%variables.vendor:lU:)\(:%variables.library:lU:)\Bin\(:%variables.command:lU:)',
        'synchronous.action'
            => 'main'
    ));
    $dispatcher->setKitName('Hoa\Console\Dispatcher\Kit');
    exit($dispatcher->dispatch($router));
}
catch ( \Hoa\Core\Exception $e ) {

    $message = $e->raise(true);
    $code    = 1;
}
catch ( \Exception $e ) {

    $message = $e->getMessage();
    $code    = 2;
}

\Hoa\Console\Cursor::colorize('foreground(white) background(red)');
echo $message, "\n";
\Hoa\Console\Cursor::colorize('normal');
exit($code);

}
