<?php


namespace EasySwoole\Component\Process\Socket;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Exception;
use Swoole\Coroutine\Socket;
use Swoole\Coroutine;

abstract class AbstractUnixProcess extends AbstractProcess
{
    function __construct(UnixProcessConfig $config)
    {
        $config->setEnableCoroutine(true);
        if(empty($config->getSocketFile())){
            throw new Exception("socket file is empty at class ".static::class);
        }
        parent::__construct($config);
    }

    public function run($arg)
    {
        if (file_exists($this->getConfig()->getSocketFile()))
        {
            unlink($this->getConfig()->getSocketFile());
        }
        $socketServer = new Socket(AF_UNIX,SOCK_STREAM,0);
        $socketServer->setOption(SOL_SOCKET,SO_LINGER,$this->getConfig()->getLinger());
        if(!$socketServer->bind($this->getConfig()->getSocketFile())){
            throw new Exception(static::class.' bind '.$this->getConfig()->getSocketFile(). ' fail case '.$socketServer->errMsg);
        }
        if(!$socketServer->listen(2048)){
            throw new Exception(static::class.' listen '.$this->getConfig()->getSocketFile(). ' fail case '.$socketServer->errMsg);
        }
        while (1){
            $client = $socketServer->accept(-1);
            if(!$client){
                return;
            }
            if($this->getConfig()->isAsyncCallback()){
                Coroutine::create(function ()use($client){
                    try{
                        $this->onAccept($client);
                    }catch (\Throwable $throwable){
                        $this->onException($throwable,$client);
                    }
                });
            }else{
                try{
                    $this->onAccept($client);
                }catch (\Throwable $throwable){
                    $this->onException($throwable,$client);
                }
            }
        }
    }

    abstract function onAccept(Socket $socket);
}