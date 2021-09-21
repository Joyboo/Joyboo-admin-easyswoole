<?php


namespace App\HttpController\Admin;


use App\Common\Http\Code;
use App\Common\Languages\Dictionary;

/**
 * Class Admin
 * @property \App\Model\Admin $Model
 * @package App\HttpController\Admin
 */
class Admin extends Auth
{
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

        // todo 返回角色列表、游戏列表、包列表、功能配置项

        $this->success($result, Dictionary::SUCCESS);
    }

    /**
     * 权限码
     */
    public function getPermCode()
    {
        $this->success([], Dictionary::SUCCESS);
    }

    /**
     * 账号名是否存在
     */
    public function accountExist()
    {
        $count = $this->Model->where('username', $this->get['username'])->count();
        $count > 0 ? $this->error(Code::ERROR, Dictionary::ADMIN_8) : $this->success();
    }
}
