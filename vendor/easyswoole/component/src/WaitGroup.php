<?php


namespace EasySwoole\Component;


use Swoole\Coroutine\Channel;

class WaitGroup
{
    private $count = 0;
    /** @var Channel  */
    private $channel;
    private $success = 0;
    private $size;

    public function __construct(int $size = 128)
    {
        $this->size = $size;
        $this->reset();
    }

    public function add()
    {
        $this->count++;
    }

    function successNum():int
    {
        return $this->success;
    }

    public function done()
    {
        $this->channel->push(1);
    }

    public function wait(?float $timeout = 15)
    {
        if($timeout <= 0){
            $timeout = PHP_INT_MAX;
        }
        $this->success = 0;
        $left = $timeout;
        while(($this->count > 0) && ($left > 0))
        {
            $start = round(microtime(true),3);
            if($this->channel->pop($left) === 1)
            {
                $this->count--;
                $this->success++;
            }
            $left = $left - (round(microtime(true),3) - $start);
        }
    }


    function reset()
    {
        $this->close();
        $this->count = 0;
        $this->success = 0;
        $this->channel = new Channel($this->size);
    }

    function close()
    {
        if($this->channel){
            $this->channel->close();
            $this->channel = null;
        }
    }

    function __destruct()
    {
        $this->close();
    }
}