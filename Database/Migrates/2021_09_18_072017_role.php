<?php

use EasySwoole\DDL\DDLBuilder;

/**
 * migrate create
 * Class Role
 */
class Role
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `role` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色名称',
  `value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色值',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '9' COMMENT '排序(越小越前)',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '0-禁用，1-启用',
  `remark` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '简介',
  `menu` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限菜单ID列表',
  `instime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加的时间戳',
  `itime` timestamp GENERATED ALWAYS AS (from_unixtime(`instime`)) VIRTUAL NULL COMMENT 'instime的时间格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色表'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('role');
    }
}
