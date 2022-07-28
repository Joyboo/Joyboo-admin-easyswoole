<?php

namespace EasySwoole\EasySwoole;

use App\Websocket\Events;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use WonderGame\EsUtility\Common\Classes\FdManager;
use WonderGame\EsUtility\EventInitialize;
use WonderGame\EsUtility\EventMainServerCreate;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        $EventInitialize = new EventInitialize([
            'httpTracker' => 'Joyboo-admin',
            'httpTrackerConfig' => ['redis-name' => 'default']
        ]);
        $EventInitialize->run();

        // 注册SwooleTable(WebSocket连接符管理)
        FdManager::getInstance()->register();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $consumers = [];
        if (is_file($file = EASYSWOOLE_ROOT . '/App/CustomProcess/config.php')) {
            $consumers = include $file;
        }
        $EventMainServerCreate = new EventMainServerCreate([
            'EventRegister' => $register,
            'webSocketEvents' => Events::class,
            'consumerJobs' => is_array($consumers) ? $consumers : false,
        ]);
        $EventMainServerCreate->run();
    }
}
