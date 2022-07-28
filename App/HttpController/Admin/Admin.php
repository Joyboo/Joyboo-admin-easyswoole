<?php


namespace App\HttpController\Admin;

use App\Common\Languages\Dictionary;
use WonderGame\EsUtility\Common\Classes\FdManager;
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

    protected function _search($where = [])
    {
        if (is_numeric($online = $this->get['online']))
        {
            $FdManager = FdManager::getInstance();
            if ($uids = $FdManager->onlineUids()) {
                $where['id'] = [$uids, $online ? 'in' : 'not in'];
            }
        }
        return $where;
    }

    protected function __after_index($items, $total)
    {
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
        return parent::__after_index($items, $total);
    }

    public function getUserInfo()
    {
        // 客户端进入页,应存id
        if (!empty($this->operinfo['extension']['homePath']))
        {
            /** @var \App\Model\Admin\Menu $Menu */
            $Menu = model_admin('Menu');
            $homePage = $Menu->getHomePage($this->operinfo['extension']['homePath']);
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
