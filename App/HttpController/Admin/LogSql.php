<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\LogSqlTrait;

/**
 * 操作日志
 * @property \App\Model\Admin\LogSql $Model
 */
class LogSql extends Auth
{
    use LogSqlTrait;
}
