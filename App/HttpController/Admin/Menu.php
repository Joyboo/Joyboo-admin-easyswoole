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
    protected array $_authOmit = ['getMenuList'];

    public function index()
    {
        $input = $this->get;

        $where = [];
        if (!empty($input['title']))
        {
            $where['title'] = ["%{$input['title']}%", 'like'];
        }
        if (isset($input['status']) && $input['status'] !== '')
        {
            $where['status'] = $input['status'];
        }

        $result = $this->Model->menuAll($where);
        $this->success($result);
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
        $userMenus = $this->getUserMenus();
        if (!is_null($userMenus) && empty($userMenus)) {
            return $this->error(Code::CODE_FORBIDDEN);
        }
        $menu = $this->Model->getRouter($userMenus);
        $this->success($menu);
    }
}
