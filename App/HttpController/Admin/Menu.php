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

    public function add()
    {
        // 如果name不为空，检查唯一性
        $name = $this->post['name'] ?? '';
        if (!empty($name))
        {
            /** @var \App\Model\Menu $model */
            $model = model('Menu');
            if ($model->where('name', $name)->count())
            {
                return $this->error(Code::ERROR, Dictionary::ADMIN_9);
            }
        }
        parent::add();
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
