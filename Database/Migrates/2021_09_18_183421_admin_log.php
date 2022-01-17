<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class AdminLog
 */
class AdminLog
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `log_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '管理员',
  `ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '登录IP',
  `instime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `updtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `itime` timestamp GENERATED ALWAYS AS (from_unixtime(`instime`)) VIRTUAL NOT NULL COMMENT 'instime的时间格式',
  `utime` timestamp GENERATED ALWAYS AS (from_unixtime(`updtime`)) VIRTUAL NOT NULL COMMENT 'updtime的时间格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('log_login');
    }
}
