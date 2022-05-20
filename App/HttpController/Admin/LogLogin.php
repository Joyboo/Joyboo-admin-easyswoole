<?php


namespace App\HttpController\Admin;

use App\Model\LogLogin as AdminLogModel;
use WonderGame\EsUtility\HttpController\Admin\LogSqlTrait;

/**
 * 登录日志
 * Class LogLogin
 * @property AdminLogModel $Model
 * @package App\HttpController\Admin
 */
class LogLogin extends Auth
{
    use LogSqlTrait;
}
