<?php


namespace App\HttpController\Admin;


use App\Common\Http\Code;
use App\Common\Languages\Dictionary;

/**
 * Class Menu
 * @property \App\Model\Menu $Model
 * @package App\HttpController\Admin
 */
class Menu extends Auth
{
    public function index()
    {
        $input = $this->get;

        if (!empty($input['title']))
        {
            $this->Model->where(['title' => ["%{$input['title']}%", 'like']]);
        }
        if (isset($input['status']) && $input['status'] !== '')
        {
            $this->Model->where('status', $input['status']);
        }

        $all = $this->Model->order('sort', 'asc')->all();
        $Tree = new \App\Common\Classes\Tree($all);

        $this->success($Tree->getAll());
    }

    /**
     * 客户端路由
     */
    public function getMenuList()
    {
        $menu = $this->Model->menuList();
        $this->success($menu);
    }
}
