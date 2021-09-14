<?php

namespace EasySwoole\Utility;


use Swoole\Coroutine;

class SnowFlake
{
    private static $lastTimestamp = 0;
    private static $lastSequence  = 0;
    private static $sequenceMask  = 2047;
    private static $twepoch       = 1508945092000;

    /**
     * 生成基于雪花算法的随机编号
     * @param int $dataCenterID 数据中心ID 0-31
     * @param int $workerID     任务进程ID 0-127
     * @return int 分布式ID
     */
    static function make($dataCenterID = 0, $workerID = 0)
    {
        if($dataCenterID > 31){
            throw new \InvalidArgumentException('dataCenterId must between 0-31');
        }
        if($workerID > 127){
            throw new \InvalidArgumentException('dataCenterId must between 0-127');
        }
        // 41bit timestamp + 5bit dataCenterId + 7bit workerId + 11bit lastSequence
        $timestamp = self::timeGen();
        if (self::$lastTimestamp == $timestamp) {
            self::$lastSequence = (self::$lastSequence + 1) & self::$sequenceMask;
            if (self::$lastSequence == 0){
                $timestamp = self::tilNextMillis(self::$lastTimestamp);
            }
        } else {
            self::$lastSequence = 0;
        }
        self::$lastTimestamp = $timestamp;
        return (($timestamp - self::$twepoch) << 22) | ($dataCenterID << 17) | ($workerID << 10) | self::$lastSequence;
    }

    /**
     * 反向解析雪花算法生成的编号
     * @param int|float $snowFlakeId
     * @return \stdClass
     */
    static function unmake($snowFlakeId)
    {
        $Binary = str_pad(decbin($snowFlakeId), 64, '0', STR_PAD_LEFT);
        $Object = new \stdClass;
        $Object->timestamp = bindec(substr($Binary, 0, 42)) + self::$twepoch;
        $Object->timestamp = round($Object->timestamp/1000,3);
        $Object->dataCenterID = bindec(substr($Binary, 42, 5));
        $Object->workerID = bindec(substr($Binary, 47, 7));
        $Object->sequence = bindec(substr($Binary, -11));
        return $Object;
    }

    private static function tilNextMillis($lastTimestamp)
    {
        $timestamp = self::timeGen();
        while ($timestamp <= $lastTimestamp) {
            $cid = Coroutine::getCid();
            if($cid >0 ){
                Coroutine::sleep(0.001);
            }else{
                usleep(1);
            }
            $timestamp = self::timeGen();
        }
        return $timestamp;
    }

    private static function timeGen()
    {
        return (float)sprintf('%.0f', microtime(true) * 1000);
    }
}
