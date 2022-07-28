<?php


namespace App\Model\Admin;

use App\Task\Message;
use App\Websocket\Events;
use EasySwoole\EasySwoole\Task\TaskManager;
use WonderGame\EsUtility\Common\Classes\CtxRequest;
use WonderGame\EsUtility\Model\Admin\AdminModelTrait;

class Admin extends Base
{
    use AdminModelTrait;

    public function signInLog($data = [])
    {
        go(function () use ($data) {
            /** @var \App\Model\Admin\LogLogin $model */
            $model = model_admin('LogLogin');
            $model->data($data)->save();
        });
    }

    // 强制用户退出
    protected function logout($data, $text = '')
    {
        $operinfo = CtxRequest::getInstance()->getOperinfo();
        $array = [
            'formId' => $operinfo['id'],
            'formName' => $operinfo['realname'],
            'formAvatar' => $operinfo['avatar'],
            'relogin' => [
                'force' => 1,
                'title' => "账号已被{$text}",
                'content' => "您的账号已被{$text}，请退出登录"
            ]
        ];
        $task = TaskManager::getInstance();
        $task->async(new Message([
            'adminid' => $data['id'],
            'event' => Events::EVENT_8,
            'data' => $array
        ]));
    }
}
