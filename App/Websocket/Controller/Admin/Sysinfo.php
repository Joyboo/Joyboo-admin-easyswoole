<?php

namespace App\Websocket\Controller\Admin;

use App\Common\Classes\FdManager;
use App\Task\VersionUpdate;
use App\Websocket\Controller\BaseController;
use App\Websocket\Events;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;

class Sysinfo extends BaseController
{
    public function refresh()
    {
        $force = $this->caller()->getArgs()['force'] ?? 1;
        TaskManager::getInstance()->async(new VersionUpdate(['force' => $force]));
    }

    public function refreshUser()
    {
        $args = $this->caller()->getArgs();
        $adminId = $args['id'];
        if (empty($adminId)) {
            return;
        }

        $force = $args['force'] ?? 1;

        $Server = ServerManager::getInstance()->getSwooleServer();
        FdManager::getInstance()->uidForeach(
            $adminId,
            function ($fd) use ($Server, $force) {
                $Server->push($fd, $this->fmtMessage(Events::EVENT_2, ['force' => $force]));
            }
        );
    }

    // todo 后面得移动到相关控制器里
    public function sendUserMessage()
    {
        $args = $this->caller()->getArgs();
        if (empty($args['toId']) || empty($args['message'])) {
            return;
        }

        $Server = ServerManager::getInstance()->getSwooleServer();
        FdManager::getInstance()->uidForeach(
            $args['toId'],
            function ($fd) use ($Server, $args) {
                $Server->push($fd, $this->fmtMessage(Events::EVENT_6, $args));
            }
        );
    }
}
