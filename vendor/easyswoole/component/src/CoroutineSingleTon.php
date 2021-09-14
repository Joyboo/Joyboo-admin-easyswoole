<?php


namespace EasySwoole\Component;


use Swoole\Coroutine;

trait CoroutineSingleTon
{
    private static $instance = [];

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function getInstance(...$args)
    {
        $cid = Coroutine::getCid();
        if(!isset(static::$instance[$cid])){
            static::$instance[$cid] = new static(...$args);
            /*
             * 兼容非携程环境
             */
            if($cid > 0){
                Coroutine::defer(function ()use($cid){
                    unset(static::$instance[$cid]);
                });
            }
        }
        return static::$instance[$cid];
    }

    function destroy(int $cid = null)
    {
        if($cid === null){
            $cid = Coroutine::getCid();
        }
        unset(static::$instance[$cid]);
    }
}