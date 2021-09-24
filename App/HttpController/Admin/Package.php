<?php


namespace App\HttpController\Admin;


use App\Common\Http\Code;

class Package extends Auth
{
    public function gkey()
    {
        if (!isset($this->get['column']))
        {
            return $this->error(Code::ERROR);
        }
        $action = 'get' . ucfirst($this->get['column']);
        $key = $this->Model->$action();
        $this->success($key);
    }
}
