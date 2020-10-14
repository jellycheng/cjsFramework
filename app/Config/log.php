<?php
return [
    'prefix' => sprintf("%s/%s/%s.log", env('LOG_DIR', App::storagePath('logs')), env('APP_NAME', 'app'), env('APP_NAME', 'app')),
    'level'  =>  env('LOG_LEVEL', 'debug'),
    'name'   => env('APP_ENV', 'prod'),
    'channel'=>env('APP_NAME', 'app'),
];
