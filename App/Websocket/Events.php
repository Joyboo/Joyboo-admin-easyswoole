<?php

namespace App\Websocket;

use EasySwoole\FastCache\Cache;
use Swoole\WebSocket\Server;

/**
 * Class WebsocketEvents
 * @package App\WebSocket
 */
class Events
{

    /**
     * @param Server $server
     * @param \Swoole\Http\Request $request
     */
    public static function onOpen(Server $server, \Swoole\Http\Request $request)
    {
        //绑定fd变更状态
//        Cache::getInstance()->enQueue('client_list', $request->fd);
//        Cache::getInstance()->set('fd' . $request->fd, ["value" => $user['id']], 3600);
        var_dump($request->fd, $request->server);
//        $server->push($request->fd, '返回onopen数据:' . $request->fd);
    }

    /**
     * 链接被关闭时
     * @param \Swoole\Server $server
     * @param int $fd
     * @param int $reactorId
     * @throws \Exception
     */
    public static function onClose(\Swoole\Server $server, int $fd, int $reactorId)
    {
//        $queue = Cache::getInstance()->deQueue('client_list');
        echo "client-{$fd} is closed\n";
    }

    /**
     * 程序发生错误时
     * @param \Swoole\Server $serv
     * @param int $worker_id 异常进程的编号
     * @param int $worker_pid 异常进程的ID
     * @param int $exit_code 退出的状态码，范围是 0～255
     * @param int $signal 进程退出的信号
     * @doc https://wiki.swoole.com/wiki/page/166.html
     */
    public static function onError(\Swoole\Server $serv, int $worker_id, int $worker_pid, int $exit_code, int $signal)
    {
        logger()->error("WebSocket onError: worker_id={$worker_id} worker_pid={$worker_pid} exit_code={$exit_code} signal={$signal}", 'error');
    }
}
