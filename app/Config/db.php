<?php
return [
    'demo' => [
        'driver'    => 'mysql',
        'read'      => [
            'host'     => env('DEMO_DB_READ_HOST', 'localhost'),
            'database' => env('DEMO_DB_READ_DATABASE', 'db_demo'),
            'username' => env('DEMO_DB_READ_USERNAME', 'root'),
            'password' => env('DEMO_DB_READ_PASSWORD', '88888888'),
            'port'     => env('DEMO_DB_READ_PORT', 3306),
        ],
        'write'     => [
            'host'     => env('DEMO_DB_WRITE_HOST', 'localhost'),
            'database' => env('DEMO_DB_WRITE_DATABASE', 'db_demo'),
            'username' => env('DEMO_DB_WRITE_USERNAME', 'root'),
            'password' => env('DEMO_DB_WRITE_PASSWORD', '88888888'),
            'port'     => env('DEMO_DB_WRITE_PORT', 3306),
        ],
        'sticky'    => true,//必须为true
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix'    => 't_'
    ],

];
