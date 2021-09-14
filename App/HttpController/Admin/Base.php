<?php


namespace App\HttpController\Admin;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class Base extends Controller
{
    protected function success($result = null, $msg = null)
    {
        $this->writeJson(Status::CODE_OK, $result, $msg);
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
            $data = Array(
                "code" => $statusCode,
                "result" => $result,
                "message" => $msg
            );
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
        $request = $this->request();
        $data = $request->getParsedBody();
        if (empty($data)) {
            $json = $request->getBody()->__toString();
            $data = json_decode($json, true);
        }
        return $data;
    }
}
