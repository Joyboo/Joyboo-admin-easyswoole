<?php


namespace App\HttpController\Admin;


use App\Common\Http\Code;
use App\Common\Languages\Dictionary;

class Menu extends Auth
{
    public function index()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');

        $input = $this->request()->getQueryParams();

        if (!empty($input['title']))
        {
            $model->where(['title' => ["%{$input['title']}%", 'like']]);
        }
        if (isset($input['status']) && $input['status'] !== '')
        {
            $model->where('status', $input['status']);
        }

        $all = $model->order('sort', 'asc')->all();
        $Tree = new \App\Common\Classes\Tree($all);

        $this->success($Tree->getAll());
    }

    public function add()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $form = $this->getPostParams();
        $result = $model->data($form)->save();
        $result ? $this->success() : $this->error(Dictionary::ADMIN_6);
    }

    // todo 权限限制
    public function change()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $form = $this->getPostParams();
        foreach (['id', 'column', 'status'] as $col)
        {
            if (!isset($form[$col]))
            {
                return $this->error(Code::ERROR_5, Dictionary::ADMIN_7);
            }
        }
        $result = $model->update([$form['column'] => $form['status']], ['id' => $form['id']]);
        $result ? $this->success() : $this->error(Dictionary::ADMIN_6);
    }

    /**
     * 客户端路由
     */
    public function getMenuList()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $menu = $model->menuList();
        $this->success($menu);
    }
}
