<?php


namespace App\HttpController\Admin;

use App\Languages\Dictionary;
use App\Model\Admin;

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
        var_dump('controller data ', $data);
        $result = $Admin->login($data, $request);

        if ($result === false) {
            $err = $Admin->getError();
            return $this->error($err['code'] ?? 500, $err['msg'] ?? 'error' );
        }
        $this->success($result, lang(Dictionary::ADMIN_3));
    }
}
