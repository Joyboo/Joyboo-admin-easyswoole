<?php


namespace EasySwoole\Phpunit\Tests;


use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

class ExampleTest extends TestCase
{
    function testNormal()
    {
        $this->assertEquals("easyswoole","easyswoole");
    }

    function testCoroutine()
    {
        Coroutine::sleep(0.001);
        $this->assertEquals("easyswoole","easyswoole");
    }
}