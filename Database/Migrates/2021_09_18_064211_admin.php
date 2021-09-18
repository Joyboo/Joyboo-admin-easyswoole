<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class Admin
 */
class Admin
{
    /**
     * 创建： php easyswoole migrate create --create=Admin
     * 执行： php easyswoole migrate run
     * php easyswoole migrate run
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `admin` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机号',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `realname` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '真实姓名',
  `rid` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '角色id(关联role表的主键)',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '9' COMMENT '排序(越小越前)',
  `extension` json NOT NULL COMMENT '扩展信息',
  `instime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加的时间戳',
  `itime` timestamp GENERATED ALWAYS AS (from_unixtime(`instime`)) VIRTUAL NULL COMMENT 'instime的时间格式',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('admin');
    }
}
