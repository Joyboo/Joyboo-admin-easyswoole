<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/7/29
 * Time: 下午4:00
 */

namespace EasySwoole\Http\AbstractInterface;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;


abstract class AbstractRouter
{
    const PARSE_PARAMS_NONE = 0;
    const PARSE_PARAMS_IN_GET = 1;
    const PARSE_PARAMS_IN_POST = 2;
    const PARSE_PARAMS_IN_CONTEXT = 3;

    const PARSE_PARAMS_CONTEXT_KEY = 'PARSE_PARAMS_CONTEXT_KEY';

    private $routeCollector;
    private $methodNotAllowCallBack = null;
    private $routerNotFoundCallBack = null;
    private $globalMode = false;
    private $pathInfoMode = true;
    private $injectParams = AbstractRouter::PARSE_PARAMS_IN_CONTEXT;

    final function __construct()
    {
        $this->routeCollector = new RouteCollector(new Std(),new GroupCountBased());
        $this->initialize($this->routeCollector);
    }

    abstract function initialize(RouteCollector $routeCollector);

    /**
     * @return bool
     */
    public function isPathInfoMode(): bool
    {
        return $this->pathInfoMode;
    }

    /**
     * @param bool $pathInfoMode
     */
    public function setPathInfoMode(bool $pathInfoMode): void
    {
        $this->pathInfoMode = $pathInfoMode;
    }

    function getRouteCollector():RouteCollector
    {
        return $this->routeCollector;
    }


    function setMethodNotAllowCallBack(callable $call)
    {
        $this->methodNotAllowCallBack = $call;
    }

    function getMethodNotAllowCallBack()
    {
        return $this->methodNotAllowCallBack;
    }

    /**
     * @return null
     */
    public function getRouterNotFoundCallBack()
    {
        return $this->routerNotFoundCallBack;
    }

    /**
     * @param null $routerNotFoundCallBack
     */
    public function setRouterNotFoundCallBack($routerNotFoundCallBack): void
    {
        $this->routerNotFoundCallBack = $routerNotFoundCallBack;
    }

    /**
     * @return bool
     */
    public function isGlobalMode(): bool
    {
        return $this->globalMode;
    }

    public function setGlobalMode(bool $globalMode): AbstractRouter
    {
        $this->globalMode = $globalMode;
        return $this;
    }

    public function parseParams(?int $injectWay = null)
    {
        if($injectWay === null){
            return $this->injectParams;
        }else{
            $this->injectParams = $injectWay;
            return $this;
        }
    }
}