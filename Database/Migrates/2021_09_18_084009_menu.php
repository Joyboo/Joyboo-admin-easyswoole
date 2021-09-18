<?php

use EasySwoole\DDL\DDLBuilder;

/**
 * migrate basic
 * Class Menu
 */
class Menu
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE `menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上一级ID',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '菜单类型,0-目录,1-菜单,2-按钮',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '组件名',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '显示名称',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '9' COMMENT '排序(越小越前)',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '路由path',
  `component` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '组件路径',
  `redirect` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '重定向path',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态,0-禁用，1-启用',
  `permission` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限标识',
  `isext` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '外链,0-否，1-是',
  `isshow` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '显示,0-否，1-是',
  `keepalive` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '缓存,0-否，1-是',
  `affix` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '固钉,0-否，1-是',
  `ignore_auth` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '忽略权限,0-否，1-是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限认证菜单&路由&按钮'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('menu');
    }
}
