<?php


namespace App\HttpController\Admin;


use App\Common\Languages\Dictionary;

class Admin extends Auth
{
    public function getUserInfo()
    {
        $result = [
            'id' => $this->operinfo['id'],
            'username' => $this->operinfo['username'],
            'realname' => $this->operinfo['realname'],
            'avatar' => $this->operinfo['extension']['avatar'] ?? '',
            'desc' => $this->operinfo['extension']['desc'] ?? '',
            'homePath' => '', //  todo 替换进入页
            'roles' => [['roleName' => $this->operinfo['summary'], 'value' => $this->operinfo['name']]]
        ];

        $this->success($result, Dictionary::SUCCESS);
    }
}
