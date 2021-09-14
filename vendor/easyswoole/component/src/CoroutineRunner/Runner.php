<?php


namespace EasySwoole\Component\CoroutineRunner;


use Swoole\Coroutine\Channel;
use Swoole\Coroutine;

class Runner
{
    protected $concurrency;
    protected $taskChannel;
    protected $isRunning = false;
    protected $runningNum = 0;
    protected $onException;
    /** @var callable|null */
    protected $onLoop;

    function __construct($concurrency = 64,$taskChannelSize = 1024)
    {
        $this->concurrency = $concurrency;
        $this->taskChannel = new Channel($taskChannelSize);
    }

    function setOnException(callable $call):Runner
    {
        $this->onException = $call;
        return $this;
    }

    function setOnLoop(callable $call):Runner
    {
        $this->onLoop = $call;
        return $this;
    }

    function status():array
    {
        return [
            'queueSize'=>$this->taskChannel->length(),
            'concurrency'=>$this->concurrency,
            'runningNum'=>$this->runningNum,
            'isRunning'=>$this->isRunning
        ];
    }

    function addTask(Task $task):Runner
    {
        $this->taskChannel->push($task);
        return $this;
    }

    function queueSize():int
    {
        return $this->taskChannel->length();
    }

    function start(float $waitTime = 30)
    {
        if(!$this->isRunning){
            $this->isRunning = true;
            $this->runningNum = 0;
        }
        if($waitTime <=0){
            $waitTime = PHP_INT_MAX;
        }
        $start = time();
        while ($waitTime > 0){
            if(is_callable($this->onLoop)){
                call_user_func($this->onLoop,$this);
            }
            if($this->runningNum <= $this->concurrency && !$this->taskChannel->isEmpty()){
                $task = $this->taskChannel->pop(0.01);
                if($task instanceof Task){
                    Coroutine::create(function ()use($task){
                        $this->runningNum++;
                        $ret = null;
                        $task->setStartTime(microtime(true));
                        try{
                            $ret = call_user_func($task->getCall());
                            $task->setResult($ret);
                            if($ret !== false && is_callable($task->getOnSuccess())){
                                call_user_func($task->getOnSuccess(),$task);
                            }else if(is_callable($task->getOnFail())){
                                call_user_func($task->getOnFail(),$task);
                            }
                        }catch (\Throwable $throwable){
                            if(is_callable($this->onException)){
                                call_user_func($this->onException,$throwable,$task);
                            }else{
                                throw $throwable;
                            }
                        }finally{
                            $this->runningNum--;
                        }
                    });
                }
            }else{
                if(time() - $start > $waitTime){
                    break;
                }else if($this->taskChannel->isEmpty() && $this->runningNum <= 0){
                    break;
                }else{
                    /*
                     * 最小调度粒度为0.01
                     */
                    Coroutine::sleep(0.01);
                }
            }
        }
        $this->isRunning = false;
        $this->runningNum = 0;
    }
}