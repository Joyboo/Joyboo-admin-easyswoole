<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\Component\Di;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;

class NoneAnnotation extends AnnotationController
{
    public $gc = 1;

    function index()
    {
        $this->gc = time();
        $this->response()->write('index');
    }


    protected function onRequest(?string $action): ?bool
    {
        if ($action == 'testOnRequest') {
            $this->response()->write('testOnRequest');
            return false;
        }
        return true;
    }


    function afterAction(?string $actionName): void
    {
        Di::getInstance()->set("afterAction", 'afterAction');
    }

    protected function noneAction()
    {
        $this->response()->write('noneAction');
    }

    function actionNotFound(?string $action)
    {
        $this->response()->write(404);
    }

    /**
     * @Api(path="/testR",name="exec")
     */
    function exception()
    {
        $this->gc = time();
        new AAAAAAAAAAA();
    }

    /**
     * @Api(name="deprecated",path="/testDeprecated",deprecated=true)
     */
    public function testDeprecated()
    {

    }


    protected function onException(\Throwable $throwable): void
    {
        $this->response()->write('exception');
    }
}
