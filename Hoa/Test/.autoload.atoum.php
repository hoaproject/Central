<?php

$composer = dirname(__DIR__) . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'autoload.php';

if(file_exists($composer))
    require_once $composer;
else
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
                 'Core' . DIRECTORY_SEPARATOR .
                 'Core.php';

if(isset($_SERVER['HOA_ATOUM_PRASPEL_EXTENSION']))
    Hoa\Core::getInstance()->getParameters()->setParameter(
        'namespace.prefix.Atoum',
        $_SERVER['HOA_ATOUM_PRASPEL_EXTENSION']
    );
