<?php

require_once __DIR__ . DIRECTORY_SEPARATOR .
             '..' . DIRECTORY_SEPARATOR .
             'Hoa' . DIRECTORY_SEPARATOR .
             'Core' . DIRECTORY_SEPARATOR .
             'Core.php';

$router = new Hoa\Router\Http();
$router
    ->get(
        'r',
        '/Resource/(?-i)(?<family>Library|Hoathis|Contributions)/(?<tail>[/\w \d_\-\.]+)',
        function ( $_this, $family, $tail, $_request ) {

            static $_formats = array('tree', 'raw');
            static $_remotes = array('github', 'hoa');

            $http   = new Hoa\Http\Response\Response();
            $format = $_formats[0];

            if(isset($_SERVER['HTTP_REFERER'])) {

                $referer = parse_url($_SERVER['HTTP_REFERER']);
                $host    = implode(
                    '.',
                    array_slice(
                        explode('.', $referer['host']),
                        -2,
                        2
                    )
                );

                switch($host) {

                    case 'github.com':
                    case 'github.io':
                    case 'githubusercontent.com':
                        $remote = $_remotes[0];
                      break;

                    case 'hoa-project.net':
                    case 'hoa.io':
                        $remote = $_remotes[1];
                      break;

                    default:
                        $remote = $_remotes[0];
                }
            }
            else
                $remote = $_remotes[0];

            if(isset($_request['remote'])) {

                if(false === in_array($_request['remote'], $_remotes)) {

                    $http->sendStatus($http::STATUS_NOT_ACCEPTABLE);

                    return;
                }

                $remote = $_request['remote'];
            }

            if(isset($_request['format'])) {

                if(false === in_array($_request['format'], $_formats)) {

                    $http->sendStatus($http::STATUS_NOT_ACCEPTABLE);

                    return;
                }

                $format = $_request['format'];
            }

            $uri = null;

            if('hoa' === $remote) {

                $uri   = 'http://git.hoa-project.net/' . $family . '/';
                $tails = explode('/', trim($tail, '/'));

                if('Contributions' === $family) {

                    if(2 > count($tails))
                        throw new Hoa\Router\Exception\NotFound(
                            'Contribution name is incomplete.', 0);

                    $library = array_shift($tails) . '/' .
                               array_shift($tails);
                }
                else
                    $library = array_shift($tails);

                $uri .= $library . '.git/';

                if(empty($tails))
                    $uri .= 'about';
                else {

                    switch($format) {

                        case 'tree':
                            $uri .= 'tree/';
                          break;

                        case 'raw':
                            $uri .= 'plain/';
                          break;
                    }

                    $uri .= implode('/', $tails);
                }
            }
            elseif('github' === $remote) {

                $uri   = 'https://github.com/hoaproject/';
                $tails = explode('/', trim($tail, '/'));

                if('Library' === $family)
                    $library = array_shift($tails);
                elseif('Hoathis' === $family)
                    $library = 'Hoathis-' . array_shift($tails);
                elseif('Contributions' === $family) {

                    if(2 > count($tails))
                        throw new Hoa\Router\Exception\NotFound(
                            'Contribution name is incomplete.', 0);

                    $library = 'Contributions-' .
                               array_shift($tails) . '-' .
                               array_shift($tails);
                }

                $uri .= $library . '/';

                if(!empty($tails)) {

                    switch($format) {

                        case 'tree':
                            $uri .= 'blob/master/';
                          break;

                        case 'raw':
                            $uri .= 'raw/master/';
                          break;
                    }

                    $uri .= implode('/', $tails);
                }
            }

            $http->sendStatus($http::STATUS_MOVED_PERMANENTLY);
            $http->sendHeader('Location', $uri);

            return;
        }
    )
    ->get(
        's',
        '/State/(?<library>[\w ]+)',
        function ( $library, $_request ) {

            $Library = ucfirst(strtolower($library));
            $http    = new Hoa\Http\Response\Response();

            if(false === file_exists('hoa://Library/' . $Library)) {

                $http->sendStatus($http::STATUS_NOT_FOUND);

                return;
            }

            $status = Hoa\Console\Processus::execute('hoa devtools:state ' . $library);

            if(empty($status)) {

                $http->sendStatus($http::STATUS_INTERNAL_SERVER_ERROR);

                return;
            }

            if(isset($_request['format'])) {

                if('raw' !== $_request['format']) {

                    $http->sendStatus($http::STATUS_NOT_ACCEPTABLE);

                    return;
                }

                $http->sendHeader('Content-Type', 'text/plain');
                echo $status;

                return;
            }

            $http->sendHeader('Content-Type', 'image/png');
            echo file_get_contents(
                __DIR__  . DS .
                'Badges' . DS .
                'Image'  . DS .
                ucfirst($status) . '.png'
            );

            return;
        }
    );

$dispatcher = new Hoa\Dispatcher\Basic();

try {

    $dispatcher->dispatch($router);
}
catch ( Hoa\Router\Exception\NotFound $e ) {

    $http = new Hoa\Http\Response\Response();
    $http->sendStatus($http::STATUS_NOT_FOUND);
    $http->sendHeader('Content-Type', 'text/plain');

    $rules = $router->getRules();

    echo 'GET ' . $rules['r'][$router::RULE_PATTERN] . "\n\n" .
         'Permanent link to a library or a resource inside a library.' . "\n\n" .
         'Usage:' . "\n" .
         '    * Link to the Hoa\Core library' . "\n" .
         '      GET /Resource/Library/Core' . "\n" .
         '    * Use ?remote to select a specific remote amongst hoa or github (default: auto-select):' . "\n" .
         '      GET /Resource/Library/Core?remote=github' . "\n" .
         '    * Link to the Hoa\Console\Cursor class' . "\n" .
         '      GET /Resource/Library/Console/Cursor.php' . "\n" .
         '    * Link to the atoum/praspel-extension contribution:' . "\n" .
         '      GET /Resource/Contributions/Atoum/PraspelExtension' . "\n" .
         '    * Use ?format=raw to get only the file, not the tree (default: format=tree)' . "\n" .
         '      GET
         /Resource/Library/Console/Documentation/Image/Readline_autocompleters.gif?format=raw' . "\n\n\n" .

         'GET ' . $rules['s'][$router::RULE_PATTERN] . "\n\n" .
         'Get the state of a library.' . "\n\n" .
         'Usage:' . "\n" .
         '    * State of Hoa\Core as an image'. "\n" .
         '      GET /State/Core' . "\n" .
         '    * Use ?format to turn the image into text' . "\n" .
         '      GET /State/Core?format=raw' . "\n";
}
catch ( Exception $e ) {

    $http = new Hoa\Http\Response\Response();
    $http->sendStatus($http::STATUS_INTERNAL_SERVER_ERROR);
}
