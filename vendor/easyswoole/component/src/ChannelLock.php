<?php


namespace EasySwoole\Component;


use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class ChannelLock
{
    protected $list;
    protected $status = [];
    use Singleton;

    function lock(string $lockName,float $timeout = -1):bool
    {
        $cid = Coroutine::getCid();
        if(isset($this->status[$cid])){
            return true;
        }
        if(!isset($this->list[$lockName])){
            $this->list[$lockName] = new Channel(1);
        }
        /** @var Channel $channel */
        $channel = $this->list[$lockName];
        $ret = $channel->push(1,$timeout);
        if($ret){
            $this->status[$cid] = true;
        }
        return $ret;
    }

    function unlock(string $lockName,float $timeout = -1):bool
    {
        $cid = Coroutine::getCid();
        if(!isset($this->status[$cid])){
            return true;
        }
        if(!isset($this->list[$lockName])){
            $this->list[$lockName] = new Channel(1);
        }
        /** @var Channel $channel */
        $channel = $this->list[$lockName];
        if($channel->isEmpty()){
            unset($this->status[$cid]);
            return true;
        }else{
            $ret = $channel->pop($timeout);
            if($ret){
                unset($this->status[$cid]);
            }
            return $ret;
        }
    }

    function deferLock(string $lockName,float $timeout = -1):bool
    {
        $lock = $this->lock($lockName,$timeout);
        if($lock){
            Coroutine::defer(function ()use($lockName){
                $this->unlock($lockName);
            });
        }
        return $lock;
    }

}