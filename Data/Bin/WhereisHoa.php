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

/**
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 */

function _define ( $name, $value, $case = false ) {

    if(!defined($name))
        return define($name, $value, $case);

    return false;
}

function cin ( $out = null ) {

    if(null !== $out)
        cout($out);

    return trim(fgets(STDIN));
}

function cinq ( $out = null ) {

    $in = strtolower(cin($out));

    switch($in) {

        case 'y':
        case 'ye':
        case 'yes':
        case 'yeah': // hihi
            return true;
          break;

        default:
            return false;
    }
}

function cout ( $out ) {

    return fwrite(STDOUT, $out);
}

function check ( $out, $test ) {

    if(false === $test) {

        cout('✖  ' . $out);
        exit;
    }

    cout('✔  ' . $out);

    return;
}

_define('STDIN',  fopen('php://stdin',  'rb'));
_define('STDOUT', fopen('php://stdout', 'wb'));
_define('STDERR', fopen('php://stderr', 'wb'));
_define('DS',     DIRECTORY_SEPARATOR);

cout('** Where is Hoa **' . "\n\n");
cout('Ready to redefine the path to the framework?' . "\n");
cout('We need to redefine it in:' . "\n" .
     '  • the “hoa” binary;' . "\n" .
     '  • the configuration file;' . "\n" .
     '  • the configuration cache file.' . "\n");

$go = cinq("\n" . 'There we go [y/n]? ');

if(false === $go) {

    cout('Ok, bye bye!' . "\n");

    exit;
}

$whereis = cin("\n" . 'A very simple question: where is Hoa so (Core.php)?' .
               "\n" . '> ');

cout("\n");

check(
    'Check if the given file exists' . "\n",
    !is_dir($whereis) && file_exists($whereis)
);

require $whereis;

check(
    'Check if it is Hoa\'s core' . "\n",
    defined('HOA')
);
check(
    'Check if “hoa” binary is accessible' . "\n",
    file_exists($bin = __DIR__ . DS . 'Hoa.php')
);
check(
    'Check if the configuration file is accessible' . "\n",
    file_exists($json = __DIR__ . DS . '..' . DS . 'Etc' . DS .
                       'Configuration' . DS . 'HoaCoreCore.json')
);
check(
    'Check if the configuration cache file is accessible' . "\n",
    file_exists($cache = __DIR__ . DS . '..' . DS . 'Etc' . DS .
                        'Configuration' . DS . '.Cache' . DS .
                        'HoaCoreCore.php')
);

$goo = cinq("\n" . 'Are you to continue [y/n]? ');

if(false === $goo) {

    cout('Ok, bye bye!' . "\n");

    exit;
}

cout("\n");

check(
    'Backup for “hoa” binary (Hoa.php.orig)' . "\n",
    copy($bin, $bin . '.orig')
);
check(
    'Backup for the configuration file (HoaCoreCore.json.orig)' . "\n",
    copy($json, $json . '.orig')
);
check(
    'Backup for the configuration cache file (HoaCoreCore.php.orig)' . "\n",
    copy($cache, $cache . '.orig')
);

cout("\n");

$bini = file_get_contents($bin);
$shoa = '#hoa' . "\n" . 'require_once ([^;]+);' . "\n" . '#!hoa';
check(
    'Check if “hoa” binary is not corrupted' . "\n",
    0 !== preg_match('`' . $shoa . '`s', $bini)
);
check(
    'Redefine “hoa” binary.' . "\n",
    ($bino = preg_replace(
        '`' . $shoa . '`s',
        '#hoa' . "\n" .
        'require_once \'' . str_replace('\'', '\\\'', $whereis) . '\';' . "\n" .
        '#!hoa',
        $bini,
        1
    )) &&
    file_put_contents($bin, $bino)
);

$jsoni = file_get_contents($json);
$jhoa  = '("root.framework"\s*:\s*)"(.*?)(?<!\\\)"';
check(
    'Check if the configuration file is not corrupted' . "\n",
    0 !== preg_match('`' . $jhoa . '`s', $jsoni)
);
check(
    'Redefine the configuration file' . "\n",
    ($jsono = preg_replace(
        '`' . $jhoa . '`s',
        '\1"' . str_replace('"', '\"', dirname(dirname($whereis))) . '"',
        $jsoni,
        1
    )) &&
    file_put_contents($json, $jsono)
);

$cachei = file_get_contents($cache);
$choa  = '(\'root.framework\'\s*=>\s*)\'(.*?)(?<!\\\)\'';

check(
    'Check if the configuration cache file is not corrupted' . "\n",
    0 !== preg_match('`' . $choa . '`s', $cachei)
);
check(
    'Redefine the configuration cache file' . "\n",
    ($cacheo = preg_replace(
        '`' . $choa . '`s',
        '\1\'' . str_replace('\'', '\\\'', dirname(dirname($whereis))) . '\'',
        $cachei,
        1
    )) &&
    file_put_contents($cache, $cacheo)
);

cout("\n");
cout('\o/' . "\n");
cout('Path to framework is refined!' . "\n");
cout('(You may delete backups (*.orig) after ' .
     'beeing sure that all works fine).' . "\n");

}
