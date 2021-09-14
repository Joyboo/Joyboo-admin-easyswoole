<?php


namespace EasySwoole\Component;


use Swoole\Coroutine;
use Swoole\Table;

class ReadyScheduler
{
    use Singleton;

    const STATUS_UNREADY = 0;
    const STATUS_READY = 1;

    private $table;

    function __construct()
    {
        $this->table = new Table(2048);
        $this->table->column('status',Table::TYPE_INT,1);
        $this->table->create();
    }

    function addItem(string $key,int $status = self::STATUS_UNREADY):ReadyScheduler
    {
        $this->table->set($key,[
            'status'=>$status
        ]);
        return $this;
    }

    function status(string $key):?int
    {
        $ret = $this->table->get($key);
        if($ret){
            return $ret['status'];
        }else{
            return null;
        }
    }

    function ready(string $key,bool $force = false):ReadyScheduler
    {
        if($force){
            $this->table->set($key,[
                'status'=>self::STATUS_READY
            ]);
        }else{
            $this->table->incr($key,'status',1);
        }
        return $this;
    }

    function unready(string $key,bool $force = false):ReadyScheduler
    {
        if($force){
            $this->table->set($key,[
                'status'=>self::STATUS_UNREADY
            ]);
        }else{
            $this->table->decr($key,'status',1);
        }
        return $this;
    }

    function restore(string $key,?int $status):ReadyScheduler
    {
        $this->table->set($key,[
            'status'=>$status
        ]);
        return $this;
    }

    function waitReady($keys,float $time = 3.0):bool
    {
        if(!is_array($keys)){
            $keys = [$keys];
        }else if(empty($keys)){
            return true;
        }
        while (1){
            foreach ($keys as $key => $item){
                if($this->status($item) >= self::STATUS_READY){
                    unset($keys[$key]);
                }
                if(count($keys) == 0){
                    return true;
                }
                if($time > 0){
                    $time = $time - 0.01;
                    Coroutine::sleep(0.01);
                }else{
                    return false;
                }
            }
        }
    }

    function waitAnyReady(array $keys,float $timeout = 3.0):bool
    {
        if(empty($keys)){
            return true;
        }
        while (1){
            foreach ($keys as $key){
                if($this->status($key) >= self::STATUS_READY){
                    return true;
                }
            }
            if($timeout > 0){
                $timeout = $timeout - 0.01;
                Coroutine::sleep(0.01);
            }else{
                return false;
            }
        }
    }

}