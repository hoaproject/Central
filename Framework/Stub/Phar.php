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

$root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

require $root . 'Core/Core.php';

if('1' === ini_get('phar.readonly'))
    throw new \Hoa\Core\Exception(
        'The directive phar.readonly is set to 1; must be set to 0.' . "\n" .
        'Tips: php -d phar.readonly=0 %s.', 0, @$argv[0] ?: __FILE__);

$name = 'Hoa_v-' .
        HOA_VERSION_MAJOR   . '.' .
        HOA_VERSION_MINOR   . '.' .
        HOA_VERSION_RELEASE .
        HOA_VERSION_STATUS  . '.phar';

if(file_exists($name) && false === unlink($name))
    throw new \Hoa\Core\Exception(
        'Phar %s already exists and we cannot delete it.', 1, $name);

$phar = new \Phar($name);
$phar->setMetadata(array(
    'author'          => 'Ivan Enderlin',
    'license'         => 'New BSD License',
    'copyright'       => \Hoa\Core::©(),
    'version.name'    => $name,
    'version.major'   => HOA_VERSION_MAJOR,
    'version.minor'   => HOA_VERSION_MINOR,
    'version.release' => HOA_VERSION_RELEASE,
    'version.status'  => HOA_VERSION_STATUS,
    'datetime'        => date('c')
));
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->buildFromIterator(
    new \RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root)
    ),
    $root
);
$phar->setStub(<<<'STUB'
<?php

\Phar::mapPhar('Hoa.phar');
require 'phar://Hoa.phar/Core/Core.php';

$phar = new \Phar(__FILE__);

foreach(array_slice($_SERVER['argv'], 1) as $option)
    switch(strtolower($option)) {

        case '-m':
        case '--metadata':
            echo 'Metadata:' . "\n\n";
            $metadata = $phar->getMetadata();
            $max      = 0;

            foreach($metadata as $key => $value)
                $max < $l = strlen($key) and $max = $l;

            foreach($metadata as $key => $value)
                echo sprintf(
                    '%-' . $max . 's : %s',
                    $key,
                    str_replace("\n", ' ', $value)
                ) . "\n";
          break;

        case '-t':
        case '--test':
            echo (HOA ? 'true' : 'false') . "\n";
          break;

        case '-s':
        case '--signature':
            $signature = $phar->getSignature();
            echo $signature['hash_type'] . ': ' . $signature['hash'] . "\n";
          break;

        case '-p':
        case '--phar':
            echo 'Phar archive version: ' . $phar->getVersion() . "\n" .
                 'Phar API version    : ' . \Phar::apiVersion() . "\n";
          break;

        case '-e':
        case '--extract':
            $phar->extractTo(__DIR__);
            echo 'Extracted in ' . __DIR__ . "\n";
          break;

        case '-h':
        case '-?':
        case '--help':
        default:
            echo 'Usage   : ' . $_SERVER['argv'][0] . ' <options>' . "\n" .
                 'Options :' . "\n" .
                 '    -m, --metadata  : Print all metadata.' . "\n" .
                 '    -t, --test      : Test if Hoa is in this Phar.' . "\n" .
                 '    -s, --signature : Print signature.' . "\n" .
                 '    -p, --phar      : Phar informations.' . "\n" .
                 '    -e, --extract   : Extract Hoa in the current directory.' . "\n" .
                 '    -h, --help      : This help.' . "\n" .
                 '    -?, --help      : This help.' . "\n";
    }

__HALT_COMPILER();
STUB
);

echo __DIR__ . DS . $name . "\n";
