<?php


namespace App\HttpController\Admin;

use App\Common\Languages\Dictionary;
use App\Common\Http\Code;
use EasySwoole\EasySwoole\Core;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class Base extends Controller
{
    /** @var \App\Model\Base $Model */
    protected $Model;

    /**
     * 实例化模型类
     *   1.为空字符串自动实例化
     *   2.为null不实例化
     *   3.不为空字符串，实例化指定模型
     * @var string
     */
    protected $modelName = '';

    protected $get = [];

    protected $post = [];

    protected function onRequest(?string $action): bool
    {
        $this->_initialize();
        return true;
    }

    protected function _initialize()
    {
        // 实例化模型
        $this->instanceModel();
        // 请求参数
        $this->requestParams();
    }

    protected function onException(\Throwable $throwable): void
    {
        trace($throwable->getMessage(), 'error', 'error');
        $message = Core::getInstance()->runMode() !== 'produce'
            ? $throwable->getMessage()
            : '网络异常，请稍后再试~';
        $this->error(\EasySwoole\Http\Message\Status::CODE_INTERNAL_SERVER_ERROR, $message);
    }

    protected function success($result = null, $msg = null)
    {
        is_null($msg) && $msg = Dictionary::SUCCESS;
        $this->writeJson(Code::SUCCESS, $result, $msg);
    }

    protected function error(int $code, $msg = null)
    {
        if (is_null($msg))
        {
            $msg = Status::getReasonPhrase($code);
        }
        $this->writeJson($code, [], $msg);
    }

    protected function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if (!$this->response()->isEndResponse()) {

            // 允许直传i18n的key
            $objClass = new \ReflectionClass(Dictionary::class);
            $const = $objClass->getConstants();
            if (in_array($msg, $const))
            {
                $msg = lang($msg);
            }

            $data = [
                'code' => $statusCode,
                'result' => $result,
                'message' => $msg
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            // 浏览器对axios隐藏了http错误码和异常信息，如果程序出错，通过业务状态码告诉客户端
            $this->response()->withStatus(Status::CODE_OK);
            return true;
        } else {
            return false;
        }
    }

    protected function writeUpload($url, $code = 200, $msg = '')
    {
        if (!$this->response()->isEndResponse()) {

            $data = [
                'code' => $code,
                'url' => $url,
                'message' => $msg
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(Status::CODE_OK);
            return true;
        } else {
            return false;
        }
    }

    protected function instanceModel()
    {
        if (!is_null($this->modelName))
        {
            if ($this->modelName === '')
            {
                $arr = explode('\\', static::class);
                $this->Model = model(ucfirst(end($arr)));
            } else {
                $this->Model = model($this->modelName);
            }
        }
    }

    protected function requestParams()
    {
        $this->get = $this->request()->getQueryParams();

        $post = $this->request()->getParsedBody();
        if (empty($post))
        {
            $post = $this->json();
        }
        $this->post = $post ?: [];
    }

    protected function isMethod($method)
    {
        return strtoupper($this->request()->getMethod()) === strtoupper($method);
    }

    /**
     * [1 => 'a', 2 => 'b', 4 => 'c']
     * 这种数组传给前端会被识别为object
     * 强转为typescript数组
     * @param array $array
     * @return array
     */
    protected function toArray($array = [])
    {
        $result = [];
        foreach ($array as $value)
        {
            $result[] = $value;
        }
        return $result;
    }
}
