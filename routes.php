<?php
$routes = [
    [
        'path' => '#kinopoisk-parse$#i',
        'methods' => ['COMMAND'],
        'controller' => 'KinopoiskParseController',
        'action' => 'parseTop'
    ],
    [
        'path' => '#\/top[\/\?]?$#i',
        'methods' => ['GET', 'POST'],
        'controller' => 'MovieController',
        'action' => 'showTop'
    ],
];