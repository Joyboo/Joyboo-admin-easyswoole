<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Tests\TestController\Normal;
use EasySwoole\HttpAnnotation\Utility\Scanner;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use PHPUnit\Framework\TestCase;

class ScannerTest extends TestCase
{
    function testGetFileDeclaredClass()
    {
        $class = Scanner::getFileDeclaredClass(__DIR__ . '/TestController/Normal.php');
        $this->assertEquals(Normal::class, $class);
    }

    function testScanAnnotations()
    {
        $scan = new Scanner();
        $array = $scan->scanAnnotations(__DIR__ . '/TestController');
        $this->assertEquals(7, count($array));
    }

    function testRouter()
    {
        $scan = new Scanner();
        $col = new RouteCollector(new Std(), new GroupCountBased());
        $scan->mappingRouter($col, __DIR__ . '/TestController', 'EasySwoole\HttpAnnotation\Tests\TestController');
        $this->assertEquals('/NoneAnnotation/exception', $col->getData()[0]['GET']['/testR']);
    }

    function testDeprecated()
    {
        $scan = new Scanner();
        $col = new RouteCollector(new Std(), new GroupCountBased());
        $scan->mappingRouter($col, __DIR__ . '/TestController', 'EasySwoole\HttpAnnotation\Tests\TestController');
        $this->assertArrayNotHasKey('/deprecated', $col->getData()[0]['GET']);
    }
}
