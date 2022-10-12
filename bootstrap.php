<?php

use EasySwoole\Command\CommandManager;
use EasySwoole\DatabaseMigrate\MigrateCommand;
use EasySwoole\DatabaseMigrate\MigrateManager;
use EasySwoole\DatabaseMigrate\Config\Config as MrConfig;

/******************************************************************************************
 * 关闭死锁检测相关堆栈信息输出到日志
 *    Swoole文档:     https://wiki.swoole.com/#/coroutine/coroutine?id=deadlock_check
 *    EasySwoole文档: http://www.easyswoole.com/QuickStart/install.html
 ******************************************************************************************/
\Swoole\Coroutine::set(['enable_deadlock_check' => false]);

//全局bootstrap事件
//date_default_timezone_set('Asia/Shanghai');

$config = [
    'host'          => '127.0.0.1',
    'port'          => 3306,
    'user'          => 'root',
    'password'      => '',
    'database'      => 'vben_admin',
    'timeout'       => 3,
    'charset'       => 'utf8mb4',
];

// 注册migrate自定义命令: php easyswoole migrate
CommandManager::getInstance()->addCommand(new MigrateCommand());
$MrConfig = new MrConfig($config);
// 表名
$MrConfig->setMigrateTable('migrations');
// 迁移文件目录的绝对路径
$MrConfig->setMigratePath(EASYSWOOLE_ROOT . '/Database/Migrates/');
// 数据填充目录绝对路径
$MrConfig->setSeederPath(EASYSWOOLE_ROOT . '/Database/Seeds/');

MigrateManager::getInstance($MrConfig);

unset($config, $MrConfig);
