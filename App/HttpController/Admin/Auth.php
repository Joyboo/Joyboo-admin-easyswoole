<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use Linkunyuan\EsUtility\Classes\LamJwt;

abstract class Auth extends Base
{

    protected function onRequest(?string $action): ?bool
    {
        return $this->checkAuthorization();
    }

    protected function checkAuthorization()
    {
        if (! $this->request()->hasHeader('authorization'))
        {
            $this->error(Code::ERROR_1, Dictionary::HTTP_1);
            return false;
        }

        $authorization = $this->request()->getHeader('authorization');
        if (is_array($authorization))
        {
            $authorization = current($authorization);
        }

        $jwt = LamJwt::verifyToken($authorization, config('auth.jwtkey'), false);

        $id = $jwt['data']['data']['id'] ?? '';
        if ($jwt['status'] != 1 || empty($id))
        {
            $this->error(Code::ERROR_2, Dictionary::HTTP_2);
            return false;
        }
        return true;
    }
}
