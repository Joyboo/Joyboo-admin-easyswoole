<?php


namespace EasySwoole\Bridge;


use Swoole\Coroutine\Socket;

interface CommandInterface
{
    public function commandName():string;
    public function exec(Package $package,Package $responsePackage,Socket $socket);
}
