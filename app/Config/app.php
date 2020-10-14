<?php
return [
    'sequence_tbl_prefix' => [//sequence业务对应的订单前缀
                              't_order'           => 'DJ',
                              't_order_item'      => 'OI',
                              't_order_server'    => 'RW',
                              't_order_discount'  => 'OD',
                              't_order_consignee' => 'OC',
                              't_order_pay_list'  => 'OPL',
                              't_return_order'    => 'SH',
                              't_return_item'     => 'RI',
                              't_return_discount' => 'RD',
                              't_refund_info'     => 'RFI',
                              't_order_invoice'   => 'OIV',
    ],
    'sequence_db_config' => [//sequence业务缓存down了之后使用的db配置
                             'host'     => env('DAOJIA_COMMON_DB_WRITE_HOST'),
                             'database' => env('DAOJIA_COMMON_DB_WRITE_DATABASE'),
                             'username' => env('DAOJIA_COMMON_DB_WRITE_USERNAME'),
                             'password' => env('DAOJIA_COMMON_DB_WRITE_PASSWORD'),
                             'port'     => env('DAOJIA_COMMON_DB_WRITE_PORT', 3306),
    ],
];
