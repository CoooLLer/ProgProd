<?php
define('PROJECT_ROOT', __DIR__);

$config = [
    'database' => [
        'modules_path' => '/App/Core/Modules/Database',
        'type' => 'mysqli',
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'password' => DB_PASSWORD,
        'charset' => 'utf8',
    ],
    'controllers_namespace' => 'App\Controller',

];