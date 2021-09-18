<?php


namespace App\HttpController\Admin;


use App\Common\Languages\Dictionary;

class Menu extends Auth
{
    public function getMenuList()
    {
        $this->success([], Dictionary::SUCCESS);
    }
}
