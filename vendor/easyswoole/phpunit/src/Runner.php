<?php


namespace EasySwoole\Phpunit;


use PHPUnit\TextUI\Command;
use Swoole\ExitException;
use Swoole\Timer;
use Swoole\Coroutine\Scheduler;

class Runner
{
    public static function run($noCoroutine = true)
    {
        if ($noCoroutine) {
            return Command::main(false);
        }


        $exitCode = null;
        $scheduler = new Scheduler();
        $scheduler->add(function () use (&$exitCode) {
            try {
                $exitCode = Command::main(false);
            } catch (\Throwable $throwable) {
                /**
                 * 屏蔽 swoole exit错误
                 */
                if (strpos($throwable->getMessage(),'swoole exit') === false) {
                    throw $throwable;
                }
            } finally {
                Timer::clearAll();
            }
        });
        $scheduler->start();

        return $exitCode;
    }
}
