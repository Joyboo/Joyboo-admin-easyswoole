<?php

namespace EasySwoole\EasySwoole;

use App\Websocket\Events;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use WonderGame\EsUtility\EventInitialize;
use WonderGame\EsUtility\EventMainServerCreate;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        $EventInitialize = new EventInitialize([
            'mysqlOnQueryFunc' => [
                '_save_sql' => function ($sql) {
                    /** @var \App\Model\LogSql $Log */
                    $Log = model('LogSql');
                    $Log->sqlWriteLog($sql);
                },
            ]
        ]);
        $EventInitialize->run();

        // 注册SwooleTable(WebSocket连接符管理)
        \App\Common\Classes\FdManager::getInstance()->register();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $consumers = include EASYSWOOLE_ROOT . '/App/CustomProcess/config.php';
        $EventMainServerCreate = new EventMainServerCreate([
            'EventRegister' => $register,
            'webSocketEvents' => [
                EventRegister::onOpen => [Events::class, 'onOpen'],
                EventRegister::onClose => [Events::class, 'onClose'],
                EventRegister::onWorkerError => [Events::class, 'onError'],
                EventRegister::onShutdown => [Events::class, 'onShutdown']
            ],
            'consumerJobs' => is_array($consumers) ? $consumers : false,
        ]);
        $EventMainServerCreate->run();
    }
}
