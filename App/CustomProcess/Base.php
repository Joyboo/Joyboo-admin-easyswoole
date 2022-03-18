<?php


namespace App\CustomProcess;

use EasySwoole\Redis\Redis;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\RedisPool\RedisPool;

abstract class Base extends AbstractProcess
{
    /**
     * 传递的参数
     * @var array
     */
    protected $args = [];

    /**
     * redis配置
     * @var array
     */
    protected $rcfg = [];

    /**
     * 消费单条数据，由子类继承实现
     * @param string $data 每一条队列数据
     * @return mixed
     */
    abstract protected function consume($data = '');

    /**
     * EasySwoole自定义进程入口
     * @param $arg
     */
    public function run($arg)
    {
        $this->args = $this->getArg();

        $this->rcfg = config('REDIS.default');

        $this->addTick($this->args['tick'] ?? 1000, function () {

            RedisPool::invoke(function (Redis $Redis) {

                $Redis->select($this->rcfg['db']);

                for ($i = 0; $i < $this->args['limit'] ?? 200; ++$i)
                {
                    $data = $Redis->lPop($this->args['queue']);
                    if (!$data)
                    {
                        break;
                    }

                    if ($this->args['coroutine'] ?? false)
                    {
                        go(function () use ($data) { $this->consume($data); });
                    } else {
                        $this->consume($data);
                    }
                }
            });
        });
    }
}
