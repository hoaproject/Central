<?php

use Hoa\Router;

$router = new Router\Http();
$router
    ->get(
        'c',
        '(?<vendor>)/(?<chapter>)\.html'
    )
    ->get(
        'hack',
        '(?<chapter>)\.html'
    )
    ->get(
        'full',
        '/(?<vendor>)/(?<chapter>)\.html'
    )

    ->_get(
        'literature',
        'http://hoa-project.net/Literature\.html'
    )
    ->_get(
        'learn',
        'http://hoa-project.net/Literature/Learn/(?<chapter>)\.html'
    )
    ->get(
        '_resource',
        'http://static.hoa-project.net/(?<resource>)'
    )
    ->_get(
        'central_resource',
        'http://central.hoa-project.net/Resource/(?<path>)'
    )
    ->_get(
        'board',
        'https://waffle.io/hoaproject/(?<repository>)'
    )
    ->_get(
        'git',
        'http://git.hoa-project.net/(?<repository>).git/'
    )
    ->_get(
        'github',
        'https://github.com/hoaproject/(?<repository>)'
    );
