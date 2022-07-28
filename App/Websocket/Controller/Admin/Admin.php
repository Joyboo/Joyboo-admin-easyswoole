<?php

namespace App\Websocket\Controller\Admin;

use App\Websocket\Controller\BaseController;
use App\Websocket\Events;
use Swoole\WebSocket\Server as WebSocketServer;
use WonderGame\EsUtility\Common\Classes\FdManager;

class Admin extends BaseController
{
    // 消息
    const MESSAGE = 'message';
    // 刷新
    const REFRESH = 'refresh';
    // 重登
    const RELOGIN = 'relogin';

    public function message()
    {
        $args = $this->caller()->getArgs();

        switch ($args['type']) {
            case self::MESSAGE:
                $this->send(Events::EVENT_6, $args);
                break;
            case self::REFRESH:
                $this->send(Events::EVENT_2, $args);
                break;
            case self::RELOGIN:
                $this->send(Events::EVENT_8, $args);
                break;
            default: break;
        }
    }

    protected function send($event, $args = [])
    {
        $FdManager = FdManager::getInstance();
        if ($args['toId'] === 'all') {
            $FdManager->allForeach(function ($fd, WebSocketServer $Server) use ($event, $args) {
                $Server->push($fd, $this->fmtMessage($event, $args));
            });
        } else {
            $FdManager->uidForeach($args['toId'], function ($fd, WebSocketServer $Server) use ($event, $args) {
                $Server->push($fd, $this->fmtMessage($event, $args));
            });
        }
    }
}
