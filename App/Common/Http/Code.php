<?php


namespace App\Common\Http;


class Code
{
    const SUCCESS = 200;
    // 无authorization
    const ERROR_1 = 1000;
    // authorization过期
    const ERROR_2 = 1001;
    // HttpParamException
    const ERROR_3 = 1003;
    // uid错误
    const ERROR_4 = 1004;
}
