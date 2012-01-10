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
 */

namespace {

/**
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 */

if(!defined('HOA_DATA') || !defined('HOA_APPLICATION')) {

    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Core' .
            DIRECTORY_SEPARATOR . 'Core.php';

    \Hoa\Core::getInstance()->initialize(array(
        'namespace.prefix.Bin' => 'hoa://Data/Bin;hoa://Bin'
    ));
}
else
    \Hoa\Core::getInstance()->initialize(array(
        'root.data'            => HOA_DATA,
        'root.application'     => HOA_APPLICATION,
        'namespace.prefix.Bin' => 'hoa://Data/Bin;hoa://Bin'
    ));

from('Hoa')
-> import('Router.Cli')
-> import('Dispatcher.Basic');

from('Bin')
-> import('Command.Style');

$style = new \Bin\Command\Style();
$style->import();


/**
 * Here we go…
 */
$router = new \Hoa\Router\Cli();
$router->get(
    'g',
    '(?:(?<_call>\w+):)?(?<command>\w+)?(?<_tail>.*?)',
    'main',
    'main',
    array(
        '_call'   => 'main',
        'command' => 'welcome'
    )
);

$dispatcher = new \Hoa\Dispatcher\Basic(array(
    'synchronous.controller'
        => 'Bin\Command\(:controller:lU:)\(:%variables.command:lU:)',
    'synchronous.action'
        => 'main'
));
$dispatcher->setKitName('Hoa\Console\Dispatcher\Kit');
$dispatcher->dispatch($router);

}
