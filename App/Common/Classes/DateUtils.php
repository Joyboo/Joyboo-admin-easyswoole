<?php


namespace App\Common\Classes;


class DateUtils
{
    const _Ymd = 'Y-m-d';
    const _ymd = 'ymd';
    const Ymd = 'Ymd';
    const ymd = 'ymd';
    const FULL = 'Y-m-d H:i:s';
    const YmdHis = 'YmdHis';

    public static function format($time, $fmt = '')
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        if (empty($fmt)) {
            $fmt = self::FULL;
        }
        return date($fmt, $time);
    }

    /**
     * 当前系统时区与指定时区之间的差值,单位秒
     * @param string $tzs Asia/Shanghai
     */
    public static function timeZoneOffsetSec(string $tzs)
    {
        $date = date(self::FULL);
        // 当前系统运行的时区
        $currentRunTimeZone = date_default_timezone_get();
        $currTimeZone = new \DateTimeZone($currentRunTimeZone);
        $currentOffset = $currTimeZone->getOffset(new \DateTime($date));

        $toTimeZone = new \DateTimeZone($tzs);
        $toOffset = $toTimeZone->getOffset(new \DateTime($date));

        return $currentOffset - $toOffset;
    }

    public static function getTimeZoneStamp(int $time, $tzs): int
    {
        return $time + self::timeZoneOffsetSec($tzs);
    }

    /**
     * 将ymd转换为客户端展示的Y-m-d格式，如果在客户端转换，会误杀合计行
     */
    public static function ymdToClientFormat(string $ymd): string
    {
        $len = strlen($ymd);
        $array = str_split($ymd, 2);
        $join = implode('-', $array);
        return $len === 6 ? ('20' . $join) : $join;
    }
}
