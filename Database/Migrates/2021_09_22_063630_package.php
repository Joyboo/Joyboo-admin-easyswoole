<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class Package
 */
class Package
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `package` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `gameid` smallint(5) unsigned NOT NULL COMMENT '所属游戏id',
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `url` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '下载地址',
  `pkgbnd` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'pkg或bundle_id',
  `os` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '包系统类型,0-安卓,1-ios',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '9' COMMENT '排序(越小越前)',
  `extension` json DEFAULT NULL COMMENT '扩展信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pkgbnd` (`pkgbnd`),
  KEY `gameid` (`gameid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='包数据表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('package');
    }
}
