<?php

use EasySwoole\DDL\DDLBuilder;

/**
 * migrate create
 * Class ProcessInfo
 */
class ProcessInfo
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE `process_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `servname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '服务器标识',
  `servername` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'EasySwoole服务标识',
  `pid` int(10) unsigned NOT NULL COMMENT 'worker进程id',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '进程名',
  `process` json NOT NULL COMMENT '进程信息',
  `coroutine` json NOT NULL COMMENT '协程信息',
  `coroutine_list` json NOT NULL COMMENT '协程列表',
  `mysql_pool` json NOT NULL COMMENT 'MySQL连接池信息',
  `redis_pool` json NOT NULL COMMENT 'Redis连接池信息',
  `instime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `servname` (`servname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='worker进程监控表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('process_info');
    }
}
