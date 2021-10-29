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
        'ignoreCategory' => [],
        // 单独记录的日志级别 level
        'apart_level' => ['error'],
        // 单独记录的日志类型 category
        'apart_category' => ['sql', 'worker', 'lowlevel'],
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
            'timeout'       => 30,
            'charset'       => 'utf8mb4',
        ],
        'log' => [
            'host'          => '',
            'port'          => 3305,
            'user'          => 'root',
            'password'      => '',
            'database'      => '',
            'timeout'       => 30,
            'charset'       => 'utf8mb4',
        ],
        'sdk' => [
            'host'          => '',
            'port'          => 3305,
            'user'          => '',
            'password'      => '',
            'database'      => '',
            'timeout'       => 30,
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

    'SERVER_NAME' => '4-1',

    // 超级管理员id
    'SUPER_ROLE' => [1],

    'SERVER_EXTRA' => [
        'operinfo' => 'auth_operinfo',
    ],
    // 不写日志的SQL，正则匹配
    'NOT_WRITE_SQL' => ['/set\s+time_zone/i'],

    'UPLOAD' => [
        'dir' => EASYSWOOLE_ROOT . '/Public/',
        'domain' => 'http://image-admin-easyswoole.develop',
    ],

    // 与客户端交互的字段名
    'fetchSetting' => [
        // 当前第几页
        'pageField' => 'page',
        // 每页大小
        'sizeField' => 'pageSize',
        // 列表dataSource的Key
        'listField' => 'items',
        // 合计页的Key
        'footerField' => 'summer',
        // 总条数
        'totalField' => 'total',
        // 导出表头
        'exportThField' => '_th',
        // 导出全部时发送的文件名
        'exprotFilename'=> '_fname',
    ],

    'export_dir' => EASYSWOOLE_ROOT . '/Public/excel/',
];
