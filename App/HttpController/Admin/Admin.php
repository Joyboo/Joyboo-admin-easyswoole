<?php


namespace App\HttpController\Admin;


use App\Common\Languages\Dictionary;

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
            'avatar' => $this->operinfo['extension']['avatar'] ?? '',
            'desc' => $this->operinfo['extension']['desc'] ?? '',
            'homePath' => $homePage ?? '',
            'roles' => [
                ['roleName' => $this->operinfo['name'], 'value' => $this->operinfo['value']]
            ]
        ];

        $this->success($result, Dictionary::SUCCESS);
    }

    /**
     * 权限码
     */
    public function getPermCode()
    {
        $this->success([], Dictionary::SUCCESS);
    }
}
