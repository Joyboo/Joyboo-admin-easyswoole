<?php

use EasySwoole\Command\CommandManager;
use EasySwoole\DatabaseMigrate\MigrateCommand;
use EasySwoole\DatabaseMigrate\MigrateManager;
use EasySwoole\DatabaseMigrate\Config\Config as MrConfig;

//全局bootstrap事件
//date_default_timezone_set('Asia/Shanghai');


if (is_file($funs = EASYSWOOLE_ROOT . '/App/Common/funcitons.php')) {
    require_once $funs;
}

$config = [
    'host'          => '127.0.0.1',
    'port'          => 3306,
    'user'          => 'root',
    'password'      => '0987abc123',
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

unset($config, $MrConfig, $funs);
