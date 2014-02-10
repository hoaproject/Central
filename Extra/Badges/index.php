<?php

require_once __DIR__ . DIRECTORY_SEPARATOR .
             '..' . DIRECTORY_SEPARATOR .
             '..' . DIRECTORY_SEPARATOR .
             'Hoa' . DIRECTORY_SEPARATOR .
             'Core' . DIRECTORY_SEPARATOR .
             'Core.php';

$router = new Hoa\Router\Http();
$router->get(
    's',
    '/State/(?<library>[\w ]+)',
    function ( $library ) {

        $Library = ucfirst(strtolower($library));
        $http = new Hoa\Http\Response\Response();

        if(false === file_exists('hoa://Library/' . $Library)) {

            $http->sendStatus($http::STATUS_NOT_FOUND);
            exit;
        }

        $status = Hoa\Console\Processus::execute('hoa core:state ' . $library);

        if(empty($status)) {

            $http->sendStatus($http::STATUS_INTERNAL_SERVER_ERROR);
            exit;
        }

        $http->sendHeader('Content-Type', 'image/png');
        echo file_get_contents(__DIR__ . DS . 'Image' . DS . ucfirst($status) . '.png');
    }
);
$dispatcher = new Hoa\Dispatcher\Basic();

try {

    $dispatcher->dispatch($router);
}
catch ( \Exception $e ) {

    $http = new Hoa\Http\Response\Response();
    $http->sendStatus($http::STATUS_INTERNAL_SERVER_ERROR);
}
