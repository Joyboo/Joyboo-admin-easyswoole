<?php


namespace App\HttpController\Admin;


use App\Common\Languages\Dictionary;

class Menu extends Auth
{
    /**
     * 客户端路由
     */
    public function getMenuList()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $menu = $model->menuList();
        $this->success($menu, Dictionary::SUCCESS);
    }
}
