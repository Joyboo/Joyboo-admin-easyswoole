<?php


namespace EasySwoole\Task;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Socket\UnixProcessConfig;
use EasySwoole\Task\Exception\Exception;
use Swoole\Atomic\Long;
use Swoole\Server;
use Swoole\Table;

class Task
{
    private $taskIdAtomic;
    private $config;
    private $attachServer = false;
    private $table;

    const PUSH_IN_QUEUE = 0;
    const PUSH_QUEUE_FAIL = -1;
    const ERROR_PROCESS_BUSY = -2;
    const ERROR_PROTOCOL_ERROR = -3;
    const ERROR_ILLEGAL_PACKAGE = -4;
    const ERROR_TASK_ERROR = -5;
    const ERROR_PACKAGE_EXPIRE = -6;
    const ERROR_SOCK_TIMEOUT = -7;

    static function errCode2Msg(int $code):string
    {
        switch ($code){
            case self::PUSH_IN_QUEUE:{
                return 'push task in queue';
            }
            case self::PUSH_QUEUE_FAIL:{
                return 'push task to queue fail';
            }
            case self::ERROR_PROCESS_BUSY:{
                return 'task process busy';
            }
            case self::ERROR_PROTOCOL_ERROR:{
                return 'task package protocol error';
            }
            case self::ERROR_ILLEGAL_PACKAGE:{
                return 'task package illegal';
            }
            case self::ERROR_TASK_ERROR:{
                return "task run error";
            }
            case self::ERROR_PACKAGE_EXPIRE:{
                return "task package expire";
            }
            case self::ERROR_SOCK_TIMEOUT:{
                return "task sock timeout";
            }
            default:{
                return 'unknown error';
            }
        }
    }

    function __construct(Config $config = null)
    {
        $this->taskIdAtomic = new Long(0);
        $this->table = new Table(512);
        $this->table->column('running',Table::TYPE_INT,8);
        $this->table->column('success',Table::TYPE_INT,8);
        $this->table->column('fail',Table::TYPE_INT,8);
        $this->table->column('pid',Table::TYPE_INT,8);
        $this->table->column('startUpTime',Table::TYPE_INT,8);
        $this->table->create();
        if($config){
            $this->config = $config;
        }else{
            $this->config = new Config();
        }
    }

    function getConfig():Config
    {
        return $this->config;
    }

    function status():array
    {
        $ret = [];
        foreach ($this->table as $key => $value){
            $ret[$key] = $value;
        }
        return $ret;
    }



    public function attachToServer(Server $server)
    {
        if(!$this->attachServer){
            $list = $this->__initProcess();
            /** @var AbstractProcess $item */
            foreach ($list as $item){
                $server->addProcess($item->getProcess());
            }
            $this->attachServer = true;
            return true;
        }else{
            throw new Exception("Task instance has been attach to server");
        }

    }

    public function __initProcess():array
    {
        $ret = [];
        $serverName = $this->config->getServerName();
        for($i = 0;$i < $this->config->getWorkerNum();$i++){
            $config = new UnixProcessConfig();
            $config->setProcessName("{$serverName}.TaskWorker.{$i}");
            $config->setSocketFile($this->idToUnixName($i));
            $config->setProcessGroup("{$serverName}.TaskWorker");
            $config->setArg([
                'workerIndex'=>$i,
                'taskIdAtomic'=>$this->taskIdAtomic,
                'taskConfig'=>$this->config,
                'infoTable'=>$this->table
            ]);
            $ret[$i] = new Worker($config);
        }
        return  $ret;
    }

    public function async($task,callable $finishCallback = null,$taskWorkerId = null,float $timeout = null):?int
    {
        if($taskWorkerId === null){
            $taskWorkerId = $this->randomWorkerId();
        }
        $package = new Package();
        $package->setType($package::ASYNC);
        $package->setTask($task);
        $package->setOnFinish($finishCallback);
        return $this->sendAndRecv($package,$taskWorkerId,$timeout);
    }

    /*
     * 同步返回执行结果
     */
    public function sync($task,float $timeout = 3.0,$taskWorkerId = null)
    {
        if($taskWorkerId === null){
            $taskWorkerId = $this->randomWorkerId();
        }
        $package = new Package();
        $package->setType($package::SYNC);
        $package->setTask($task);
        return $this->sendAndRecv($package,$taskWorkerId,$timeout);
    }

    private function idToUnixName(int $id):string
    {
        return $this->config->getTempDir()."/{$this->config->getServerName()}.TaskWorker.{$id}.sock";
    }

    private function randomWorkerId()
    {
        mt_srand();
        return rand(0,$this->config->getWorkerNum() - 1);
    }

    private function sendAndRecv(Package $package,int $id,float $timeout = null)
    {
        if($timeout === null){
            $timeout = $this->config->getTimeout();
        }
        if($timeout > 0){
            $package->setExpire(microtime(true) + $timeout);
        }else{
            $package->setExpire(-1);
        }
        $client = new UnixClient($this->idToUnixName($id),$this->getConfig()->getMaxPackageSize());
        $client->send(Protocol::pack(\Opis\Closure\serialize($package)));
        $ret = $client->recv($timeout);
        $client->close();
        if (!empty($ret)) {
            return \Opis\Closure\unserialize(Protocol::unpack($ret));
        }else{
            return self::ERROR_SOCK_TIMEOUT;
        }
    }
}