<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Utility\Scanner;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        $scanner = new Scanner();
        $scanner->mappingRouter($routeCollector, __DIR__ . '/RouterPath.php', __NAMESPACE__);
        $this->setGlobalMode(true);
        $this->setRouterNotFoundCallBack(function (Request $request, Response $response) {
            $response->write('not found!');
            return false;
        });
    }
}
