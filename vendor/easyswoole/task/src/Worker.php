<?php


namespace EasySwoole\Task;


use EasySwoole\Component\Process\Socket\AbstractUnixProcess;
use EasySwoole\Task\AbstractInterface\TaskInterface;
use Swoole\Atomic\Long;
use Swoole\Coroutine;
use Swoole\Coroutine\Socket;
use Swoole\Table;

class Worker extends AbstractUnixProcess
{
    protected $workerIndex;
    /**
     * @var Table
     */
    protected $infoTable;
    /** @var Long */
    protected $taskIdAtomic;
    /**
     * @var Config
     */
    protected $taskConfig;

    public function run($arg)
    {
        $this->workerIndex = $arg['workerIndex'];
        $this->infoTable = $arg['infoTable'];
        $this->taskIdAtomic = $arg['taskIdAtomic'];
        $this->taskConfig = $arg['taskConfig'];
        $this->infoTable->set($this->workerIndex,[
            'running'=>0,
            'success'=>0,
            'fail'=>0,
            'pid'=>$this->getProcess()->pid,
            'startUpTime'=>time()
        ]);
        if($this->taskConfig->getTaskQueue()){
            Coroutine::create(function (){
                while (1){
                    try {
                        if ($this->infoTable->get($this->workerIndex, 'running') >= $this->taskConfig->getMaxRunningNum()) {
                            Coroutine::sleep(0.1);
                            continue;
                        }
                        $task = $this->taskConfig->getTaskQueue()->pop();
                        if($task){
                            $taskId = $this->taskIdAtomic->add(1);
                            Coroutine::create(function ()use($taskId,$task){
                                try{
                                    $this->infoTable->incr($this->workerIndex, 'running', 1);
                                    $this->runTask($task,$taskId);
                                }catch (\Throwable $throwable){
                                    $this->onException($throwable);
                                } finally {
                                    $this->infoTable->decr($this->workerIndex,'running',1);
                                }
                            });
                        }else{
                            Coroutine::sleep(0.1);
                        }
                    }catch (\Throwable $throwable){
                        $this->onException($throwable);
                    }
                }
            });
        }
        parent::run($arg);
    }

    function onAccept(Socket $socket)
    {
        // 收取包头4字节计算包长度 收不到4字节包头丢弃该包
        $header = $socket->recvAll(4, 1);
        if (strlen($header) != 4) {
            $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::ERROR_PROTOCOL_ERROR)));
            $socket->close();
            return;
        }
        $allLength = Protocol::packDataLength($header);
        $data = $socket->recvAll($allLength, 1);
        if (strlen($data) != $allLength) {
            $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::ERROR_PROTOCOL_ERROR)));
            $socket->close();
            return;
        }
        /** @var Package $package */
        $package = \Opis\Closure\unserialize($data);
        if(!$package instanceof Package){
            $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::ERROR_ILLEGAL_PACKAGE)));
            $socket->close();
            return;
        }
        /*
           * 在投递一些非协成任务的时候，例如客户端的等待时间是3s，阻塞任务也刚好是趋于2.99999~
           * 因此在进程accept该链接并读取完数据后，客户端刚好到达最大等待时间，客户端返回了null，
           * 因此业务逻辑可能就认定此次投递失败，重新投递，因此进程逻辑也要丢弃该任务。次处逻辑为尽可能避免该种情况发生
           * -1表示忽略此种情况
        */
        if($package->getExpire() > 0 && (microtime(true) - $package->getExpire() >= 0.001)){
            //本质是进程繁忙
            $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::ERROR_PACKAGE_EXPIRE)));
            $socket->close();
            return;
        }
        try{
            if($this->infoTable->incr($this->workerIndex,'running',1) <= $this->taskConfig->getMaxRunningNum()){
                $taskId = $this->taskIdAtomic->add(1);
                switch ($package->getType()){
                    case $package::ASYNC:{
                        $socket->sendAll(Protocol::pack(\Opis\Closure\serialize($taskId)));
                        $this->runTask($package,$taskId);
                        $socket->close();
                        break;
                    }
                    case $package::SYNC:{
                        $reply = $this->runTask($package,$taskId);
                        $socket->sendAll(Protocol::pack(\Opis\Closure\serialize($reply)));
                        $socket->close();
                        break;
                    }
                }
            }else{
                //异步任务才进队列，
                if(($package->getType() != $package::SYNC) && $this->taskConfig->getTaskQueue()){
                    $ret = $this->taskConfig->getTaskQueue()->push($package);
                    if($ret){
                        $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::PUSH_IN_QUEUE)));
                    }else{
                        $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::PUSH_QUEUE_FAIL)));
                    }
                }else{
                    $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::ERROR_PROCESS_BUSY)));
                }
                $socket->close();
            }
        }catch (\Throwable $exception){
            if($package->getType() == $package::SYNC){
                $socket->sendAll(Protocol::pack(\Opis\Closure\serialize(Task::ERROR_TASK_ERROR)));
                $socket->close();
            }
            throw $exception;
        }finally{
            $this->infoTable->decr($this->workerIndex,'running',1);
        }
    }

    protected function onException(\Throwable $throwable, ...$args)
    {
        if(is_callable($this->taskConfig->getOnException())){
            call_user_func($this->taskConfig->getOnException(),$throwable,$this->workerIndex);
        }else{
            throw $throwable;
        }
    }

    protected function runTask(Package $package,int $taskId)
    {
        try{
            $task = $package->getTask();
            $reply = null;
            if(is_string($task) && class_exists($task)){
                $ref = new \ReflectionClass($task);
                if($ref->implementsInterface(TaskInterface::class)){
                    /** @var TaskInterface $ins */
                    $task = $ref->newInstance();
                }
            }
            if($task instanceof TaskInterface){
                try{
                    $reply = $task->run($taskId,$this->workerIndex);
                }catch (\Throwable $throwable){
                    $reply = $task->onException($throwable,$taskId,$this->workerIndex);
                }
            }else if(is_callable($task)){
                $reply = call_user_func($task,$taskId,$this->workerIndex);
            }
            if(is_callable($package->getOnFinish())){
                $reply = call_user_func($package->getOnFinish(),$reply,$taskId,$this->workerIndex);
            }
            $this->infoTable->incr($this->workerIndex,'success',1);
            return $reply;
        }catch (\Throwable $throwable){
            $this->infoTable->incr($this->workerIndex,'fail',1);
            $this->onException($throwable);
        }
    }
}