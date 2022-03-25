<?php

namespace App\Websocket;

use App\Common\Classes\FdManager;
use App\Common\Classes\WsEvent;
use App\Model\ChatTopic;
use App\Model\Game;
use App\Model\Package;
use Linkunyuan\EsUtility\Classes\LamJwt;
use Swoole\WebSocket\Server;

/**
 * Class WebsocketEvents
 * @package App\WebSocket
 */
class Events
{
    // 关闭客户端
    const CLOSE = 'event_1';
    // 客户端版本更新
    const SYSTEM_VERSION_UPDATE = 'event_2';
    // 踢下线
    const KICK = 'event_3';

    /**
     * @param Server $server
     * @param \Swoole\Http\Request $request
     */
    public static function onOpen(Server $server, \Swoole\Http\Request $request)
    {
        $tokenKey = config('TOKEN_KEY');
        $table = FdManager::getInstance();

        $token = $request->get[$tokenKey];

        if (empty($tokenKey))
        {
            return $server->push($request->fd, json_encode(['event' => self::CLOSE, 'message' => 'Token Empty.']));
        }
        // jwt验证
        $jwt = LamJwt::verifyToken($token, config('auth.jwtkey'));
        $id = $jwt['data']['id'] ?? '';
        if ($jwt['status'] != 1 || empty($id))
        {
            return $server->push($request->fd, json_encode(['event' => self::CLOSE, 'message' => 'Auth Fail']));
        }

        // 单点登录
        if ($loginFd = $table->getFdByUid($id))
        {
            // 踢下线
            $table->delFdByUid($id);
            $server->push($loginFd, json_encode(['event' => self::KICK, 'message' => '您的账号已在其他地方登录!']));
            $table->delUidByFd($loginFd);
        }

        $table->setUidFd($id, $request->fd);
        $table->setFdUid($request->fd, $id);
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
        $info = $server->connection_info($fd);
        if ($info['websocket_status'] === 3)
        {
            echo "[websocket] client-{$fd} is closed \n";
            $table = FdManager::getInstance();
            $uid = $table->getUidByFd($fd);
            $table->delUidByFd($fd);
            $uid && $table->delFdByUid($uid);
        } else {
            echo "[http] client-{$fd} is closed \n";
        }
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
        trace("WebSocket onError: worker_id={$worker_id} worker_pid={$worker_pid} exit_code={$exit_code} signal={$signal}", 'error');
    }

    public static function onShutdown(\Swoole\Server $serv)
    {
        trace('onShutdown---------------');
    }
}
