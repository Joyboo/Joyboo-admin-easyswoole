<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Admin;
use App\Common\Exception\HttpParamException;

class Pub extends Base
{
    public function index()
    {
        return $this->login();
    }

    public function login()
    {
        /** @var Admin $Admin */
        $Admin = model('Admin');

        $request = $this->request();

        $data = $this->getPostParams();
        try {
            $result = $Admin->login($data, $request);
        }
        catch (HttpParamException $e)
        {
            return $this->error(Code::ERROR_3, $e->getMessage());
        }

        $this->success($result, Dictionary::ADMIN_3);
    }
}
