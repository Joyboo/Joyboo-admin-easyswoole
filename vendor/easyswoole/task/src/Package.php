<?php


namespace EasySwoole\Task;


class Package
{
    const ASYNC = 1;
    const SYNC = 2;
    protected $type;
    protected $task;
    protected $onFinish;
    /**
     * @var float
     */
    protected $expire;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task): void
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getOnFinish()
    {
        return $this->onFinish;
    }

    /**
     * @param mixed $onFinish
     */
    public function setOnFinish($onFinish): void
    {
        $this->onFinish = $onFinish;
    }

    /**
     * @return float
     */
    public function getExpire(): float
    {
        return $this->expire;
    }

    /**
     * @param float $expire
     */
    public function setExpire(float $expire): void
    {
        $this->expire = $expire;
    }
}