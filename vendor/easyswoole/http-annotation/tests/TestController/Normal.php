<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;

class Normal extends Controller
{
    public $gc = 1;

    function index()
    {
        $this->gc = time();
        $this->response()->write('index');
    }


    protected function onRequest(?string $action): ?bool
    {
        if($action == 'testOnRequest'){
            $this->response()->write('testOnRequest');
            return false;
        }
        return true;
    }


    function afterAction(?string $actionName): void
    {
        Di::getInstance()->set("afterAction",'afterAction');
    }

    protected function noneAction()
    {
        $this->response()->write('noneAction');
    }

    function actionNotFound(?string $action)
    {
        $this->response()->write(404);
    }

    function exception()
    {
        $this->gc = time();
        new AAAAAAAAAAA();
    }

    protected function classParser()
    {
        $class = Annotation::class;
        if($class){
            $this->response()->write($class);
        }
        $this->response()->write('none');

    }


    protected function onException(\Throwable $throwable): void
    {
        $this->response()->write('exception');
    }
}