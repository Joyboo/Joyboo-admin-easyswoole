<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\LogSqlTrait;

/**
 * 操作日志
 */
class LogSql extends Auth
{
    use LogSqlTrait;
}
