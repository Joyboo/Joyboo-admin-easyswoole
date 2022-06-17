<?php


namespace App\HttpController\Admin;

use App\Common\Classes\FdManager;
use App\Common\Languages\Dictionary;
use WonderGame\EsUtility\HttpController\Admin\AdminTrait;

/**
 * Class Admin
 * @property \App\Model\Admin\Admin $Model
 * @package App\HttpController\Admin
 */
class Admin extends Auth
{
    protected array $_authOmit = ['getUserInfo', 'getPermCode'];

    use AdminTrait;

    protected function __search()
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

    protected function __after_index($items, $total)
    {
        /** @var \App\Model\Admin\Role $Role */
        $Role = model_admin('Role');
        $roleList = $Role->getRoleListAll();
        $FdManager = FdManager::getInstance();
        foreach ($items as &$value)
        {
            unset($value['password']);
            $value->relation;
            if (!$this->isExport)
            {
                // 管理员是否在线
                $value->online = $FdManager->onlineNum($value['id']);
            }
        }
        return parent::__after_index(['items' => $items, 'roleList' => $roleList], $total);
    }

    public function getUserInfo()
    {
        // 客户端进入页,应存id
        if (!empty($this->operinfo['extension']['homePath']))
        {
            $Tree = new \App\Common\Classes\Tree();
            $homePage = $Tree->originData(['type' => [[0, 1], 'in']])->getHomePath($this->operinfo['extension']['homePath']);
        }
        $avatar = $this->operinfo['avatar'] ?? '';

        $result = [
            'id' => $this->operinfo['id'],
            'username' => $this->operinfo['username'],
            'realname' => $this->operinfo['realname'],
            'avatar' => $avatar,
            'desc' => $this->operinfo['desc'] ?? '',
            'homePath' => $homePage ?? '',
            'roles' => [
                [
                    'roleName' => $this->operinfo['role']['name'] ?? '',
                    'value' => $this->operinfo['role']['value'] ?? ''
                ]
            ]
        ];

        $this->success($result, Dictionary::SUCCESS);
    }

    public function edit()
    {
        if ($this->isHttpGet()) {
            $data = $this->_edit(true);
            unset($data['password'], $data['instime'], $data['itime']);
            $this->success($data);
        } else {
            // 留空，不修改密码
            if (empty($this->post['password'])) {
                unset($this->post['password']);
            }
            $this->_edit();
        }
    }
}
