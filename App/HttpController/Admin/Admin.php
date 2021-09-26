<?php


namespace App\HttpController\Admin;

use App\Common\Languages\Dictionary;

/**
 * Class Admin
 * @property \App\Model\Admin $Model
 * @package App\HttpController\Admin
 */
class Admin extends Auth
{
    protected function _search()
    {
        $where = [];
        if (!empty($this->get['rid']))
        {
            $where['rid'] = $this->get['rid'];
        }
        foreach (['username', 'realname'] as $val)
        {
            if (!empty($this->get[$val]))
            {
                $where[$val] = ["%{$this->get[$val]}%", 'like'];
            }
        }
        return $where;
    }

    protected function _afterIndex($items)
    {
        $items = parent::_afterIndex($items);

        /** @var \App\Model\Role $Role */
        $Role = model('Role');
        $roleList = $Role->getRoleListAll();
        return ['items' => $items, 'roleList' => $roleList];
    }

    public function getUserInfo()
    {
        // 客户端进入页,应存id
        if (!empty($this->operinfo['extension']['homePage']))
        {
            /** @var \App\Model\Menu $Menu */
            $Menu = model('Menu');
            $homePage = $Menu->where('id', $this->operinfo['extension']['homePage'])->val('path');
        }

        $result = [
            'id' => $this->operinfo['id'],
            'username' => $this->operinfo['username'],
            'realname' => $this->operinfo['realname'],
            'avatar' => $this->operinfo['avatar'] ?? '',
            'desc' => $this->operinfo['desc'] ?? '',
            'homePath' => $homePage ?? '',
            'roles' => [
                [
                    'roleName' => $this->operinfo['role']['name'] ?? '',
                    'value' => $this->operinfo['role']['value'] ?? ''
                ]
            ]
        ];

        $super = $this->isSuper();
        // 游戏和包
        /** @var \App\Model\Game $Game */
        $Game = model('Game');
        /** @var \App\Model\Package $Package */
        $Package = model('Package');
        if (! $super)
        {
            $gameids = explode(',', $this->operinfo['extension']['gameids'] ?? '');
            $Game->where(['id' => [$gameids, 'in']]);

            $pkg = explode(',', $this->operinfo['extension']['pkgbnd'] ?? '');
            $Package->where(['pkgbnd' => [$pkg, 'in']]);
        }
        $result['gameList'] = $Game->where('status', 1)->order(...$Game->sort)->field(['id', 'name'])->all();
        $result['pkgList'] = $Package->field(['gameid', 'pkgbnd', 'name', 'id'])->order(...$Game->sort)->all();

        $result['config'] = [
            // 图片上传路径
            'imageDomain' => config('UPLOAD.domain'),
        ];

        $this->success($result, Dictionary::SUCCESS);
    }

    /**
     * 用户权限码
     */
    public function getPermCode()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $code = $model->permCode($this->operinfo['rid']);
        $this->success($code, Dictionary::SUCCESS);
    }

    protected function addGet()
    {
        $result = $this->_view();
        $this->success($result);
    }

    protected function _afterEditGet($items)
    {
        unset($items['password']);
        $result = $this->_view();
        $result['result'] = $items;
        return $result;
    }

    protected function _view()
    {
        $result = [];
        // 角色组，菜单
        /** @var \App\Model\Role $Role */
        $Role = model('Role');
        /** @var \App\Model\Menu $Menu */
        $Menu = model('Menu');
        $roleAll = $Role->getRoleListAll();
        $result['roleList'] = array_map(function ($val) {
            return ['label' => $val['name'], 'value' => $val['id']];
        }, $roleAll);
        $result['menuList'] = $Menu->menuList();
        return $result;
    }

    protected function getSave($post = [], $origin = [], $split = '.')
    {
        $data = parent::getSave($post, $origin, $split);
        // 留空，不修改密码
        if (empty($post['password'])) {
            unset($data['password']);
        }
        return $data;
    }
}
