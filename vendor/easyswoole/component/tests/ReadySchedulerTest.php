<?php


namespace EasySwoole\Component\Tests;


use EasySwoole\Component\ReadyScheduler;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

class ReadySchedulerTest extends TestCase
{
    function testNormal()
    {
        ReadyScheduler::getInstance()->addItem('worker');
        ReadyScheduler::getInstance()->addItem('rpc');
        ReadyScheduler::getInstance()->addItem('fastCache');

        go(function (){
            Coroutine::sleep(1);
            ReadyScheduler::getInstance()->ready('worker');
            ReadyScheduler::getInstance()->ready('rpc');
        });
        $this->assertEquals(false,ReadyScheduler::getInstance()->waitReady(['rpc','worker'],0.1));
        $this->assertEquals(true,ReadyScheduler::getInstance()->waitReady('rpc'));
        $this->assertEquals(true,ReadyScheduler::getInstance()->waitReady(['rpc','worker']));
        $this->assertEquals(false,ReadyScheduler::getInstance()->waitReady(['rpc','worker','fastCache'],1.1));
    }
}