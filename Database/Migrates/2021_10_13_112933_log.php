<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class Log
 */
class Log
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return " CREATE TABLE  IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admid` int(10) unsigned NOT NULL COMMENT '操作人id(关联admin表的主键)',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '日志内容',
  `instime` int(10) unsigned NOT NULL COMMENT '添加的时间戳',
  `ip` bigint(20) NOT NULL DEFAULT '0' COMMENT 'ip地址',
  `itime` timestamp GENERATED ALWAYS AS (from_unixtime(`instime`)) VIRTUAL NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('log');
    }
}
