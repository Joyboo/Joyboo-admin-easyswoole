<?php


namespace EasySwoole\Http\Tests;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Dispatcher;
use EasySwoole\Http\Message\Uri;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use PHPUnit\Framework\TestCase;

class DispatchTest extends TestCase
{
    private $dispatcherWithRouter;
    private $dispatcher;

    function setUp(): void
    {
        $this->dispatcher = new Dispatcher('EasySwoole\Http\Tests\Controller');
        $this->dispatcherWithRouter = new Dispatcher('EasySwoole\Http\Tests\ControllerWithRouter');
    }


    function testIndex()
    {
        $response = new Response();
        $this->dispatcher->dispatch($this->getRequest('/'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('index', $response->getBody()->__toString());

        $response = new Response();
        $this->dispatcherWithRouter->dispatch($this->getRequest('/', 'GET'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('the router get /', $response->getBody()->__toString());

        $response = new Response();
        $this->dispatcherWithRouter->dispatch($this->getRequest('/', 'POST'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('index', $response->getBody()->__toString());

        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setGlobalMode(true);
            $router->setMethodNotAllowCallBack(function (Request $request, Response $response) {
                $response->withStatus(405)->write('not allow');
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/', 'POST'), $response);
        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('not allow', $response->getBody()->__toString());
    }


    function testNotFound()
    {
        $response = new Response();
        $this->dispatcher->dispatch($this->getRequest('/test/test'), $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('test-404', $response->getBody()->__toString());

        $response = new Response();
        $this->dispatcher->dispatch($this->getRequest('/index/test'), $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('test-404', $response->getBody()->__toString());


        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setPathInfoMode(false);
            $router->setRouterNotFoundCallBack(function (Request $request, Response $response) {
                $response->withStatus(404)->write('router not found');
                return false;
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index', 'GET'), $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('router not found', $response->getBody()->__toString());
    }

    function testException()
    {
        $response = new Response();
        $this->dispatcher->dispatch($this->getRequest('/index/exception'), $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('error-the error', $response->getBody()->__toString());

        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index/exception'), $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('error-the error', $response->getBody()->__toString());
    }

    function testHttpExceptionHandler()
    {
        $this->dispatcher->setHttpExceptionHandler(function (\Throwable $throwable, Request $request, Response $response) {
            $response->withStatus(500)->write('the exception handler');
        });
        $response = new Response();
        $this->dispatcher->dispatch($this->getRequest('/index/httpExceptionHandler'), $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('the exception handler', $response->getBody()->__toString());

        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setHttpExceptionHandler(function (\Throwable $throwable, Request $request, Response $response) {
            $response->withStatus(500)->write('the exception handler');
            return false;
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index/httpExceptionHandler'), $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('the exception handler', $response->getBody()->__toString());
    }

    public function testAddRouter()
    {
        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setGlobalMode(true);
            $router->setPathInfoMode(false);
            $router->setRouterNotFoundCallBack(function (Request $request, Response $response) {
                $response->withStatus(404)->write('router not found');
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index'), $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('router not found', $response->getBody()->__toString());

        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setGlobalMode(true);
            $router->setPathInfoMode(false);
            $router->getRouteCollector()->get('/index', function (Request $request, Response $response) {
                $response->write('the route is add index');
            });
            $router->setRouterNotFoundCallBack(function (Request $request, Response $response) {
                $response->withStatus(404)->write('router not found');
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('the route is add index', $response->getBody()->__toString());
    }

    public function testRedirect()
    {
        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setRouterNotFoundCallBack(function (Request $request, Response $response) {
                $response->withStatus(404)->write('the 404-');
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index/test'), $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('the 404-test-404', $response->getBody()->__toString());

        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setRouterNotFoundCallBack(function (Request $request, Response $response) {
                $response->withStatus(404)->write('the 404-');
                return false;
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index/test'), $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('the 404-', $response->getBody()->__toString());

        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->setRouterNotFoundCallBack(function (Request $request, Response $response) {
                return '/index/index';
            });
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/index/test'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('index', $response->getBody()->__toString());
    }

    public function testParseParams()
    {
        // get
        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->parseParams($router::PARSE_PARAMS_IN_GET);
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/user/gaobinzhan'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"get":{"name":"gaobinzhan"},"post":[],"context":[]}', $response->getBody()->__toString());

        // post
        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->parseParams($router::PARSE_PARAMS_IN_POST);
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/user/gaobinzhan'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"get":[],"post":{"name":"gaobinzhan"},"context":null}', $response->getBody()->__toString());

        // context
        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->parseParams($router::PARSE_PARAMS_IN_CONTEXT);
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/user/gaobinzhan'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"get":[],"post":[],"context":{"name":"gaobinzhan"}}', $response->getBody()->__toString());

        // none
        $this->reset();
        $response = new Response();
        $this->dispatcherWithRouter->setOnRouterCreate(function (AbstractRouter $router) {
            $router->parseParams($router::PARSE_PARAMS_NONE);
        });
        $this->dispatcherWithRouter->dispatch($this->getRequest('/user/gaobinzhan'), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"get":[],"post":[],"context":null}', $response->getBody()->__toString());
    }

    private function reset()
    {
        $this->dispatcher = new Dispatcher('EasySwoole\Http\Tests\Controller');
        $this->dispatcherWithRouter = new Dispatcher('EasySwoole\Http\Tests\ControllerWithRouter');
    }

    private function getRequest($url, $method = 'GET', array $postData = null)
    {
        $request = new Request();
        $request->withMethod($method);
        $request->withUri(new Uri($url));
        return $request;
    }
}
