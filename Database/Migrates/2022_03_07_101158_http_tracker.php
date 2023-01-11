<?php

use EasySwoole\DDL\DDLBuilder;

/**
 * migrate create
 * Class HttpTracker
 */
class HttpTracker
{
    /**
     * migrate run
     * @return string
     */
    public function up()
    {
        // 此表会定期删除，无需分区
        return "CREATE TABLE IF NOT EXISTS `http_tracker` (
                  `point_id` varchar(50) NOT NULL,
                  `parent_id` varchar(50) DEFAULT NULL,
                  `point_name` varchar(255) DEFAULT NULL COMMENT '请求名称',
                  `is_next` int(11) NOT NULL DEFAULT '0',
                  `depth` int(11) NOT NULL DEFAULT '0',
                  `status` varchar(10) NOT NULL DEFAULT '' COMMENT '状态',
                  `repeated` tinyint(2) unsigned not null default '0' comment '是否复发请求',
                  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '请求ip',
                  `url` varchar(3000) NOT NULL DEFAULT '' COMMENT '请求地址',
                  `request` json NOT NULL COMMENT '请求参数',
                  `response` json NOT NULL COMMENT '响应参数',
                  `server_name` varchar(20) NOT NULL DEFAULT '' COMMENT '运行服务器',
                  `start_time` varchar(15) NOT NULL DEFAULT '0' COMMENT '请求开始时间',
                  `end_time` varchar(15) NOT NULL DEFAULT '0' COMMENT '请求结束时间',
                  `instime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加的时间戳',
                  `runtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运行时间',
                  PRIMARY KEY (`point_id`),
                  KEY `parent_id` (`parent_id`),
                  KEY `instime` (`instime`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='链路追踪日志';";
    }

    /**
     * migrate rollback
     * @return string
     */
    public function down()
    {
        return DDLBuilder::dropIfExists('http_tracker');
    }
}
