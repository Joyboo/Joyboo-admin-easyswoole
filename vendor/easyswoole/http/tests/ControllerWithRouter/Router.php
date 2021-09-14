<?php

namespace EasySwoole\Http\Tests\ControllerWithRouter;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/', function (Request $request, Response $response) {
            $response->write('the router get /');
            return false;
        });

        $routeCollector->get('/user/{name}','/Index/user');

//        $this->setGlobalMode(true);
    }
}
