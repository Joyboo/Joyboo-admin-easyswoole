<?php


namespace App\HttpController\Admin;


class Admin extends Auth
{
    public function getUserInfo()
    {
        $this->success(['ok'], 'success');
    }
}
