<?php
return array(
    'common'         => array(
        'host'     => env('COMMON_REDIS_HOST','127.0.0.1'),
        'port'     => env('COMMON_REDIS_PORT', 6379),
        'database' => env('COMMON_REDIS_DATABASE', 0),
        'password' => env('COMMON_REDIS_PASSWORD', ''),
        'prefix'   => 'mobile_api:common:',
        'desc'     => '无法归类的业务模块'
    ),
    'user'         => array(
        'host'     => env('USER_REDIS_HOST','127.0.0.1'),
        'port'     => env('USER_REDIS_PORT', 6379),
        'database' => env('USER_REDIS_DATABASE', 9),
        'password' => env('USER_REDIS_PASSWORD', ''),
        'prefix'   => 'mobile_api:user:',
        'desc'     => '用户业务相关模块'
    ),
    'sms_code'         => array(
        'host'     => env('SMS_REDIS_HOST','127.0.0.1'),
        'port'     => env('SMS_REDIS_PORT', 6379),
        'database' => env('SMS_REDIS_DATABASE', 0),
        'password' => env('SMS_REDIS_PASSWORD', ''),
        'prefix'   => 'mobile_api:smscode:',
        'desc'     => '缓存短信验证码'
    ),
    'user_token'=>array(
        'host'     => env('USERTOKEN_REDIS_HOST','127.0.0.1'),
        'port'     => env('USERTOKEN_REDIS_PORT', 6379),
        'database' => env('USERTOKEN_REDIS_DATABASE', 9),
        'password' => env('USERTOKEN_REDIS_PASSWORD', ''),
        'prefix'   => 'user_token:user:',
        'desc'     => '登录态专用配置'
    )
);
