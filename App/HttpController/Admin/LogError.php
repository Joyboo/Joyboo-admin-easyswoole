<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\LogErrorTrait;

/**
 * 错误日志
 * Class LogError
 * @property \App\Model\LogError $Model
 * @package App\HttpController\Admin
 */
class LogError extends Auth
{
    use LogErrorTrait;

    protected array $_authOmit = ['multiple'];
}
