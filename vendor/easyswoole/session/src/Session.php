<?php


namespace EasySwoole\Session;



class Session
{
    protected $context = [];
    protected $handler;
    protected $timeout;

    function __construct(SessionHandlerInterface $handler,float $timeout = 3.0)
    {
        $this->handler = $handler;
        $this->timeout = $timeout;
    }

    function create(string $sessionId,float $timeout = null):?Context
    {
        if($timeout === null){
            $timeout = $this->timeout;
        }
        if(!isset($this->context[$sessionId])){
            try{
                if($this->handler->open($sessionId,$timeout)){
                    $this->context[$sessionId] = new Context($this->handler->read($sessionId,$timeout));
                }else{
                    throw new Exception("fail to open sessionId {$sessionId}");
                }
            }catch (\Throwable $exception){
                unset($this->context[$sessionId]);
                $this->close($sessionId,$timeout);
                throw $exception;
            }
        }
        return $this->context[$sessionId];
    }

    function close(string $sessionId,float $timeout = null):?bool
    {
        if(isset($this->context[$sessionId])){
            if($timeout === null){
                $timeout = $this->timeout;
            }
            try{
                /** @var Context $context */
                $context = $this->context[$sessionId];
                $this->handler->write($sessionId,$context->allContext(),$timeout);
            }catch (\Throwable $exception){
                throw $exception;
            } finally {
                unset($this->context[$sessionId]);
                return $this->handler->close($sessionId,$timeout);
            }
        }
        return null;
    }

    function gc(int $expire,float $timeout = null):bool
    {
        if($timeout === null){
            $timeout = $this->timeout;
        }
        return $this->handler->gc($expire,$timeout);
    }
}
