<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Common\Http\Code;
use WonderGame\EsUtility\HttpController\BaseControllerTrait;

abstract class BaseController extends Controller
{
    use BaseControllerTrait;

    protected function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if ( ! $this->response()->isEndResponse()) {

            if (is_null($msg)) {
                $msg = Code::getReasonPhrase($statusCode);
            } elseif ($msg && in_array($msg, $this->langsConstants)) {
                $msg = lang($msg);
            }

            $data = [
                'code' => $statusCode,
                'result' => $result,
                'message' => $msg ?? ''
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            // 浏览器对axios隐藏了http错误码和异常信息，如果程序出错，通过业务状态码告诉客户端
            $this->response()->withStatus(Code::CODE_OK);
            return true;
        } else {
            return false;
        }
    }
}
