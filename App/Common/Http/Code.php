<?php


namespace App\Common\Http;

use WonderGame\EsUtility\Common\Http\Code as BaseCode;

class Code extends BaseCode
{
    const SUCCESS = 200;
    const ERROR = 1001;
    // 无authorization
    const ERROR_1 = 401;
    // authorization过期
    const ERROR_2 = 401;
    // HttpParamException
    const ERROR_3 = 1004;
    // uid错误
    const ERROR_4 = 1005;

    const ERROR_5 = 1006;
}
