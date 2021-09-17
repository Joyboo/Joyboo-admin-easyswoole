<?php


namespace App\HttpController\Admin;

use App\Common\Languages\Dictionary;
use App\Common\Http\Code;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class Base extends Controller
{
    protected function success($result = null, $msg = null)
    {
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

    protected function getPostParams()
    {
        $data = $this->request()->getParsedBody();
        return empty($data) ? $this->json() : $data;
    }
}
