<?php


namespace App\CustomProcess;

use EasySwoole\Component\Process\AbstractProcess;
use Swoole\Process;

class Test extends AbstractProcess
{
    protected function run($arg)
    {
        // TODO: Implement run() method.
        $processName = $this->getProcessName(); // 获取 注册进程名称
        $swooleProcess = $this->getProcess(); // 获取 注册进程的实例 \Swoole\Process
        $processPid = $this->getPid(); // 获取 当前进程 Pid
        $args = $this->getArg(); // 获取 注册进程时传递的参数

        var_dump('### 开始运行自定义进程 start ###');
        var_dump($processName, $swooleProcess, $processPid, $args);
        var_dump('### 运行自定义进程结束 end ###');
    }

    protected function onPipeReadable(Process $process)
    {
        // 该回调可选
        // 当主进程对子进程发送消息的时候 会触发
        $recvMsgFromMain = $process->read(); // 用于获取主进程给当前进程发送的消息
        var_dump('收到主进程发送的消息: ');
        var_dump($recvMsgFromMain);
    }

    protected function onException(\Throwable $throwable, ...$args)
    {
        // 该回调可选
        // 捕获 run 方法内抛出的异常
        // 这里可以通过记录异常信息来帮助更加方便地知道出现问题的代码
    }

    protected function onShutDown()
    {
        // 该回调可选
        // 进程意外退出 触发此回调
        // 大部分用于清理工作
    }

    protected function onSigTerm()
    {
        // 当进程接收到 SIGTERM 信号触发该回调
    }
}
