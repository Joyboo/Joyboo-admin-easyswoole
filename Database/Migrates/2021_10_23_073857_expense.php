<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class Expense
 */
class Expense
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE `expense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pkgbnd` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '包标识',
  `gameid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '所属游戏id',
  `exptime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消耗时间',
  `cost` decimal(12,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '消耗金额',
  `ymd` mediumint(9) GENERATED ALWAYS AS (date_format(from_unixtime(`exptime`),'%y%m%d')) VIRTUAL COMMENT 'exptime的时间格式',
  `etime` char(10) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (date_format(from_unixtime(`exptime`),'%Y-%m-%d')) VIRTUAL NOT NULL COMMENT 'exptime的时间格式',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pkgbnd` (`ymd`,`pkgbnd`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告消耗表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('expense');
    }
}
