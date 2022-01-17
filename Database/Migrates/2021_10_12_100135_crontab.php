<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class Crontab
 */
class Crontab
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `crontab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务名',
  `rule` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '执行规则',
  `rclass` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '异步任务模板类',
  `eclass` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '运行类',
  `method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '运行方法',
  `args` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '参数',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '备注',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-启用,1-禁用，2-运行一次',
  `instime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='定时任务表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('crontab');
    }
}
