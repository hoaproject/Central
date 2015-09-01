<?php

if (isset($_SERVER['HOA_PREVIOUS_CWD'])) {
    chdir($_SERVER['HOA_PREVIOUS_CWD']);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . '.autoload.atoum.php';

Hoa\Core::enableErrorHandler();
Hoa\Core::enableExceptionHandler();
