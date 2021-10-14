<?php

use EasySwoole\Log\LoggerInterface;

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 7800,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SOCKET_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'reload_async' => true,
            'max_wait_time' => 3
        ],
        'TASK' => [
            'workerNum' => 4,
            'maxRunningNum' => 128,
            'timeout' => 15
        ]
    ],
    "LOG" => [
        'dir' => EASYSWOOLE_ROOT . '/Log',
        'level' => LoggerInterface::LOG_LEVEL_DEBUG,
        'handler' => new \App\Common\Handler\Log(EASYSWOOLE_ROOT . '/Log'),
        'logConsole' => true,
        'displayConsole' => true,
        'ignoreCategory' => []
    ],
    'TEMP_DIR' => null,
//    'TEMP_DIR' => '/tmp/Joyboo-admin-easyswoole',

    'MYSQL' => [
        'default' => [
            'host'          => '127.0.0.1',
            'port'          => 3306,
            'user'          => 'root',
            'password'      => '0987abc123',
            'database'      => 'vben_admin',
            'timeout'       => 3,
            'charset'       => 'utf8mb4',
        ],
        'sdk' => [
            'host'          => '',
            'port'          => 3305,
            'user'          => '',
            'password'      => '',
            'database'      => '',
            'timeout'       => 3,
            'charset'       => 'utf8mb4',
        ],
    ],
    'REDIS' => [
        'default' => [
            'host'          => get_cfg_var('env.hk_redishost'),
            'port'          => get_cfg_var('env.hk_redisport'),
            'auth'          => get_cfg_var('env.hk_redispwd'),
            'db'            => get_cfg_var('env.hk_redisdb')
        ]
    ],

    /* jwt */
    'auth' => [
        'jwtkey' => get_cfg_var('env.ES_hk-api_encrypt'),
        'expire' => 86400 * 2, // token有效期
        'refresh' => 86400  // token有效期小于此值会自动续期
    ],

    // 超级管理员id
    'SUPER_ROLE' => [1],

    'UPLOAD' => [
        'dir' => EASYSWOOLE_ROOT . '/Public/',
        'domain' => 'http://image-admin-easyswoole.develop',
    ],
];
