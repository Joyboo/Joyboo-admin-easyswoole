<?php


namespace App\Model;


use WonderGame\EsUtility\Common\Classes\CtxRequest;
use WonderGame\EsUtility\Model\LogSqlTrait;

class LogSql extends Base
{
    use LogSqlTrait;

    public function sqlWriteLog($sql = '')
    {
        $Ctx = CtxRequest::getInstance();

        if ($operinfo = $Ctx->getOperinfo())
        {
            $data = [
                'admid' => $operinfo['id'] ?? 0,
                'content' => $sql,
                'ip' => ip($Ctx->request)
            ];

            $this->data($data)->save();
        }
    }
}
