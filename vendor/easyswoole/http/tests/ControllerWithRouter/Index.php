<?php


namespace EasySwoole\Http\Tests\ControllerWithRouter;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    protected $handler = false;

    public function index()
    {
        $this->response()->write('index');
    }

    public function user()
    {
        $this->response()->write(json_encode([
            'get' => $this->request()->getQueryParams(),
            'post' => $this->request()->getParsedBody(),
            'context' => ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY)
        ]));
        ContextManager::getInstance()->destroyAll();
    }

    public function exception()
    {
        throw new \Exception('the error');
    }

    public function httpExceptionHandler()
    {
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
