<?php

namespace EasySwoole\Utility;

class Random
{
    static function character($length = 6, $alphabet = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789')
    {
        mt_srand();
        // 重复字母表以防止生成长度溢出字母表长度
        if ($length >= strlen($alphabet)) {
            $rate = intval($length / strlen($alphabet)) + 1;
            $alphabet = str_repeat($alphabet, $rate);
        }
        // 打乱顺序返回
        return substr(str_shuffle($alphabet), 0, $length);
    }

    static function number($length = 6)
    {
        return static::character($length, '0123456789');
    }

    static function arrayRandOne(array $data)
    {
        if (empty($data)) {
            return null;
        }
        mt_srand();
        return $data[array_rand($data)];
    }

    /**
     * 生产一个UUID4
     * 有概率重复|短时间内可以认为唯一
     * @return string
     */
    static function makeUUIDV4()
    {
        mt_srand();
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = '-';
        $uuidV4 =
            substr($charid, 0, 8) . $hyphen .
            substr($charid, 8, 4) . $hyphen .
            substr($charid, 12, 4) . $hyphen .
            substr($charid, 16, 4) . $hyphen .
            substr($charid, 20, 12);
        return $uuidV4;
    }
}