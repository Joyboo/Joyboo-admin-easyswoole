<?php


namespace App\Model;


class ErrorLog extends Base
{
    protected function setTimeAttr($value, $alldata)
    {
        // 支持format格式
        if (!is_numeric($value))
        {
            $value = strtotime($value);
        }
        // 支持微妙级时间戳
        if (strval(strlen($value)) === 13)
        {
            $value /= 1000;
        }
        return $value;
    }
}
