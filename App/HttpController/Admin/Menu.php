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
        $result = $this->Model->data($this->post)->save();
        $result ? $this->success() : $this->error(Code::ERROR);
    }

    public function edit()
    {
        $this->success();
    }

    public function del()
    {
        $this->success();
    }

    // todo 权限限制
    public function change()
    {
        $form = $this->post;
        foreach (['id', 'column', 'status'] as $col)
        {
            if (!isset($form[$col]))
            {
                return $this->error(Code::ERROR_5, Dictionary::ADMIN_7);
            }
        }
        $result = $this->Model->update([$form['column'] => $form['status']], ['id' => $form['id']]);
        $result ? $this->success() : $this->error(Code::ERROR);
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
