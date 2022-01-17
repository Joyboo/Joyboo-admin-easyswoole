<?php

use EasySwoole\Log\LoggerInterface;

return [
    'SERVER_NAME' => "ES-Joyboo",
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
        'dir' => EASYSWOOLE_ROOT . '/../logs',
        'level' => LoggerInterface::LOG_LEVEL_DEBUG,
        'handler' => new \App\Common\Handler\Log(),
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
        // ... other
    ],
    'REDIS' => [
        'default' => [
            'host'          => get_cfg_var('env.hk_redishost'),
            'port'          => get_cfg_var('env.hk_redisport'),
            'auth'          => get_cfg_var('env.hk_redispwd'),
            'db'            => get_cfg_var('env.hk_redisdb')
        ]
        // ... other
    ],

    /* jwt */
    'auth' => [
        'jwtkey' => get_cfg_var('env.ES_hk-api_encrypt'),
        'expire' => 86400 * 2, // token有效期
        'refresh' => 86400  // token有效期小于此值会自动续期
    ],

    // 超级管理员id
    'SUPER_ROLE' => [1],

    'SERVER_EXTRA' => [
        'operinfo' => 'auth_operinfo',
    ],
    // 不记录SQL的日志
    'NOT_WRITE_SQL' => [
        // 正则匹配规则
        'pattern' => ['/set\s+time_zone/i', '/^SELECT/i'],
        // 表名
        'table' => ['log_login', 'log_error', 'log_sql'],
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
];
