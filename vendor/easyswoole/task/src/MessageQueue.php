<?php


namespace EasySwoole\Task;


use EasySwoole\Task\AbstractInterface\TaskQueueInterface;

class MessageQueue implements TaskQueueInterface
{
    private $queue;
    private $key;

    function __construct(string $key = null)
    {
        if($key === null){
            $key = ftok(__FILE__, 'a');
        }
        $this->key = $key;
        $this->queue = msg_get_queue($key, 0666);
    }

    function getQueueKey()
    {
        return $this->key;
    }

    /*
     * 清空一个queue,并不是删除
     */
    function clearQueue()
    {

        msg_remove_queue($this->queue);
        $this->queue = msg_get_queue($this->key , 0666);
    }

    function pop(): ?Package
    {
        msg_receive($this->queue, 1, $message_type, 1024, $package,false,MSG_IPC_NOWAIT);
        $package = \Opis\Closure\unserialize($package);
        if($package instanceof Package){
            return $package;
        }
        return null;
    }

    function push(Package $package): bool
    {
        return msg_send($this->queue,1,\Opis\Closure\serialize($package),false);
    }
}