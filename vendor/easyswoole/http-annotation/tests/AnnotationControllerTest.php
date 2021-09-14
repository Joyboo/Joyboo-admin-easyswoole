<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Http\Dispatcher;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Exception\Annotation\MethodNotAllow;
use EasySwoole\HttpAnnotation\Tests\TestController\Annotation;
use EasySwoole\HttpAnnotation\Tests\TestController\RouterPath;
use PHPUnit\Framework\TestCase;

class AnnotationControllerTest extends TestCase
{
    use ControllerBase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new Annotation();
        ContextManager::getInstance()->set("context", 'context data');
        Di::getInstance()->set('di', 'di data');
    }

    function testDi()
    {

        $this->controller->__hook('index', $this->fakeRequest(), $this->fakeResponse());
        $this->assertEquals('di data', $this->controller->di);
        $this->controller->di = null;
    }

    function testContext()
    {
        $this->controller->__hook('index', $this->fakeRequest(), $this->fakeResponse());
        $this->assertEquals('context data', $this->controller->context);
        $this->controller->context = null;
    }

    function testGroupAuth()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('index', $this->fakeRequest('/', []), $response);
        $this->assertEquals('PE-groupParamA', $response->getBody()->__tostring());

        $response = $this->fakeResponse();
        $this->controller->__hook('index', $this->fakeRequest('/', null), $response);
        $this->assertEquals('index', $response->getBody()->__tostring());
    }


    function testParam1()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('param1', $this->fakeRequest('/', null), $response);
        $this->assertEquals('PE-param1', $response->getBody()->__tostring());

        $response = $this->fakeResponse();
        $this->controller->__hook('param1', $this->fakeRequest('/', null, ['param1' => 520]), $response);
        $this->assertEquals(520, $response->getBody()->__tostring());
    }

    function testParam2()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('param2', $this->fakeRequest('/', null), $response);
        $this->assertEquals('PE-param1', $response->getBody()->__tostring());

        $response = $this->fakeResponse();
        $this->controller->__hook('param2', $this->fakeRequest('/', null, ['param1' => 520, 'param2' => 520]), $response);
        $this->assertEquals(1040, $response->getBody()->__tostring());
    }

    function testParam3()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('param3', $this->fakeRequest('/', null), $response);
        $this->assertEquals('PE-groupParamA', $response->getBody()->__tostring());

        $response = $this->fakeResponse();
        $this->controller->__hook('param3', $this->fakeRequest('/', null, ['param1' => 520, 'groupParamA' => 520]), $response);
        $this->assertEquals(1040, $response->getBody()->__tostring());
    }

    function testParamExport1()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('paramExport1', $this->fakeRequest('/', null), $response);
        $this->assertEquals('groupParamA', $response->getBody()->__tostring());
    }

    function testParamExport2()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('paramExport2', $this->fakeRequest('/', null, ['exp' => "exp"]), $response);
        $this->assertEquals('exp', $response->getBody()->__tostring());
    }

    function testInjectParam1()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('injectParam1', $this->fakeRequest('/', null, ['param1' => "param1"]), $response);
        $this->assertEquals('groupParamA|groupParamB|param1', $response->getBody()->__tostring());
    }

    function testInjectParam2()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('injectParam2', $this->fakeRequest('/', null, ['param1' => "param1"]), $response);
        $this->assertEquals('param1', $response->getBody()->__tostring());
    }

    function testAllowPostMethod()
    {
        try {
            $this->controller->__hook('allowPostMethod', $this->fakeRequest(), $this->fakeResponse());
        } catch (\Throwable $throwable) {
            $this->assertInstanceOf(MethodNotAllow::class, $throwable);
        }

        $response = $this->fakeResponse();
        $request = $this->fakeRequest('/allowPostMethod', null, ['data' => 1]);
        $this->controller->__hook('allowPostMethod', $request, $response);
        $this->assertEquals('allowPostMethod', $response->getBody()->__tostring());
    }

    function testInject()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('inject', $this->fakeRequest(), $response);
        $this->assertEquals('inject test class -> index', $response->getBody()->__toString());

        $response = $this->fakeResponse();
        $this->controller->__hook('injectGetString', $this->fakeRequest(), $response);
        $this->assertEquals(1, $response->getBody()->__tostring());


        $response = $this->fakeResponse();
        $this->controller->__hook('injectGetArray', $this->fakeRequest(), $response);
        $this->assertEquals('[1,2]', $response->getBody()->__tostring());
    }

    function testRoutePath()
    {
        $response = $this->fakeResponse();
        $dispatcher = new Dispatcher('EasySwoole\HttpAnnotation\Tests\TestController');
        $dispatcher->dispatch($this->fakeRequest('/Router/test'), $response);
        $this->assertEquals('EasySwoole\HttpAnnotation\Tests\TestController\RouterPath::test', $response->getBody()->__toString());

        $response = $this->fakeResponse();
        $dispatcher = new Dispatcher('EasySwoole\HttpAnnotation\Tests\TestController');
        $dispatcher->dispatch($this->fakeRequest('/Router'), $response);
        $this->assertEquals('none', $response->getBody()->__toString());

        $response = $this->fakeResponse();
        $dispatcher = new Dispatcher('EasySwoole\HttpAnnotation\Tests\TestController');
        $dispatcher->dispatch($this->fakeRequest('/Router/aa'), $response);
        $this->assertEquals('not found!', $response->getBody()->__toString());

        $response = $this->fakeResponse();
        $dispatcher = new Dispatcher('EasySwoole\HttpAnnotation\Tests\TestController');
        $dispatcher->dispatch($this->fakeRequest('/ignore'), $response);
        $this->assertEquals('ignorePrefix', $response->getBody()->__toString());
    }

}
