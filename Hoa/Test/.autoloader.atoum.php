<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Paris');

$composerAutoloaders = [
    dirname(__DIR__) . DIRECTORY_SEPARATOR .
        '..' . DIRECTORY_SEPARATOR .
        'autoload.php',
    __DIR__ . DIRECTORY_SEPARATOR .
        'vendor' . DIRECTORY_SEPARATOR .
        'autoload.php'
];

foreach ($composerAutoloaders as $autoloader) {
    if (true === file_exists($autoloader)) {
        require_once $autoloader;

        $autoloader = new Hoa\Consistency\Autoloader();
        $autoloader->addNamespace('Hoa', dirname(__DIR__));
        $autoloader->register(true);

        break;
    }
}

if (false === defined('HOA')) {
    require_once
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'Consistency' . DIRECTORY_SEPARATOR .
        'Source' . DIRECTORY_SEPARATOR .
        'Prelude.php';

    require_once
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'Protocol' . DIRECTORY_SEPARATOR .
        'Source' . DIRECTORY_SEPARATOR .
        'Wrapper.php';
}

if (isset($_SERVER['HOA_PREVIOUS_CWD'])) {
    chdir($_SERVER['HOA_PREVIOUS_CWD']);
}

if (isset($_SERVER['HOA_PRELUDE_FILES'])) {
    foreach (explode("\n", $_SERVER['HOA_PRELUDE_FILES']) as $file) {
        require_once $file;
    }
}
