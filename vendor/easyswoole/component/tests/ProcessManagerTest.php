<?php


use EasySwoole\Component\Process\Config;
use EasySwoole\Component\Tests\Lib\TestProcess;
use PHPUnit\Framework\TestCase;
use EasySwoole\Component\Process\Manager;

class ProcessManagerTest extends TestCase
{


    public function testGetProcessByPid()
    {
        $config = new Config();
        $process = new TestProcess($config);
        Manager::getInstance()->addProcess($process);

        $this->assertEquals(null, Manager::getInstance()->getProcessByPid(0));
    }

    public function testGetProcessByName()
    {
        $process = new TestProcess();
        Manager::getInstance()->addProcess($process);
        $this->assertEmpty(Manager::getInstance()->getProcessByName('test'));


        $config = new Config();
        $process = new TestProcess($config);
        $config->setProcessName('test');
        Manager::getInstance()->addProcess($process);
        $this->assertEquals(1, count(Manager::getInstance()->getProcessByName('test')));
    }

    public function testGetProcessByGroup()
    {
        $process = new TestProcess();
        Manager::getInstance()->addProcess($process);
        $this->assertEmpty(Manager::getInstance()->getProcessByGroup('test'));


        $config = new Config();
        $process = new TestProcess($config);
        $config->setProcessGroup('test');
        Manager::getInstance()->addProcess($process);
        $this->assertEquals(1, count(Manager::getInstance()->getProcessByGroup('test')));
    }
}