<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\Tests\TestController\NoneAnnotation;
use PHPUnit\Framework\TestCase;

class NoneAnnotationControllerTest extends TestCase
{
    use ControllerBase;

    protected $controller;

    protected function setUp():void
    {
        parent::setUp();
        $this->controller = new NoneAnnotation();
    }

    function testIndex()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('index',$this->fakeRequest('/',null),$response);
        $this->assertEquals('index',$response->getBody()->__tostring());
    }

    function testOnRequest()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('testOnRequest',$this->fakeRequest('/',null),$response);
        $this->assertEquals('testOnRequest',$response->getBody()->__tostring());
    }

    function testProtectFunc()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('noneAction',$this->fakeRequest('/',null),$response);
        $this->assertEquals('404',$response->getBody()->__tostring());
    }

    function test404()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('xxxxxxx',$this->fakeRequest('/',null),$response);
        $this->assertEquals('404',$response->getBody()->__tostring());
    }

    function testException()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('exception',$this->fakeRequest('/',null),$response);
        $this->assertEquals('exception',$response->getBody()->__tostring());
    }

    function testGc()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('index',$this->fakeRequest('/',null),$response);
        $this->assertEquals('index',$response->getBody()->__tostring());
        $this->assertEquals(1,$this->controller->gc);


        $response = $this->fakeResponse();
        $this->controller->__hook('exception',$this->fakeRequest('/',null),$response);
        $this->assertEquals('exception',$response->getBody()->__tostring());
        $this->assertEquals(1,$this->controller->gc);
    }

    function testAfterAction()
    {
        \EasySwoole\Component\Di::getInstance()->set('afterAction',null);
        $response = $this->fakeResponse();
        $this->controller->__hook('index',$this->fakeRequest('/',null),$response);
        $this->assertEquals('afterAction',\EasySwoole\Component\Di::getInstance()->get('afterAction'));
        \EasySwoole\Component\Di::getInstance()->set('afterAction',null);


        \EasySwoole\Component\Di::getInstance()->set('afterAction',null);
        $response = $this->fakeResponse();
        $this->controller->__hook('exception',$this->fakeRequest('/',null),$response);
        $this->assertEquals('afterAction',\EasySwoole\Component\Di::getInstance()->get('afterAction'));
        \EasySwoole\Component\Di::getInstance()->set('afterAction',null);

    }

}