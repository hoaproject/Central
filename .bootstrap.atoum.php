<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
             'Core' . DIRECTORY_SEPARATOR .
             'Core.php';

if(isset($_SERVER['HOA_ATOUM_PRASPEL_EXTENSION']))
    Hoa\Core::getInstance()->getParameters()->setParameter(
        'namespace.prefix.Atoum',
        $_SERVER['HOA_ATOUM_PRASPEL_EXTENSION']
    );
