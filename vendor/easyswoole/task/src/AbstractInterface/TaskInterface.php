<?php


namespace EasySwoole\Task\AbstractInterface;


interface TaskInterface
{
    function run(int $taskId,int $workerIndex);
    function onException(\Throwable $throwable,int $taskId,int $workerIndex);
}