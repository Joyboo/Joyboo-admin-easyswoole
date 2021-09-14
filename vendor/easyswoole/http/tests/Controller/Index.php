<?php


namespace EasySwoole\Http\Tests\Controller;


use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    protected $handler = false;

    public function index()
    {
        $this->response()->write('index');
    }

    public function exception()
    {
        throw new \Exception('the error');
    }

    public function httpExceptionHandler(){
        $this->handler = true;
        throw new \Exception('the handler');
    }

    public function actionNotFound(?string $action)
    {
        return $this->response()->withStatus(404)->write("{$action}-404");
    }

    protected function onException(\Throwable $throwable): void
    {
        if ($this->handler === true) throw $throwable;

        $this->response()->withStatus(500)->write("error-{$throwable->getMessage()}");
    }
}