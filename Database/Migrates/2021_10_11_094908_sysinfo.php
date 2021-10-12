<?php

use EasySwoole\DDL\DDLBuilder;

/**
 * migrate create
 * Class Sysinfo
 */
class Sysinfo
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        return "CREATE TABLE IF NOT EXISTS `sysinfo` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `varname` varchar(20) UNIQUE NOT NULL COMMENT '变量名',
  `type` tinyint(2) unsigned NOT NULL default '0' COMMENT '0-number,1-string,2-json',
  `value` text NOT NULL COMMENT '变量值',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '0-禁用，1-启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT '系统动态配置'";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('sysinfo');
    }
}
