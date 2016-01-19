<?php

date_default_timezone_set('Europe/Paris');

$composer =
    dirname(__DIR__) . DIRECTORY_SEPARATOR .
    '..' . DIRECTORY_SEPARATOR .
    'autoload.php';

if (file_exists($composer)) {
    require_once $composer;
}
else {
    require_once
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'Consistency' . DIRECTORY_SEPARATOR .
        'Prelude.php';

    require_once
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'Protocol' . DIRECTORY_SEPARATOR .
        'Wrapper.php';
}
