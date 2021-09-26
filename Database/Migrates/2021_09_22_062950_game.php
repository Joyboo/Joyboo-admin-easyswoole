<?php

use EasySwoole\DDL\Blueprint\Create\Table as CreateTable;
use EasySwoole\DDL\Blueprint\Alter\Table as AlterTable;
use EasySwoole\DDL\Blueprint\Drop\Table as DropTable;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

/**
 * migrate create
 * Class Game
 */
class Game
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `game` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态: 0-禁用，1-启用',
  `instime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `itime` timestamp GENERATED ALWAYS AS (from_unixtime(`instime`)) VIRTUAL NULL COMMENT 'instime的时间格式',
  `extension` json NOT NULL COMMENT '扩展信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='游戏表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('game');
    }
}
