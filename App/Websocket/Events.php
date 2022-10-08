<?php

namespace App\Websocket;

use Swoole\WebSocket\Server;
use WonderGame\EsUtility\Common\Classes\FdManager;
use App\Common\Languages\Dictionary;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Socket\Client\WebSocket;
use WonderGame\EsUtility\Common\Classes\LamJwt;

/**
 * Class WebsocketEvents
 * @package App\WebSocket
 */
class Events
{
    // 心跳
    const EVENT_0 = 'EVENT_0';
    // 通知客户端关闭连接
    const EVENT_1 = 'EVENT_1';
    // 更新版本
    const EVENT_2 = 'EVENT_2';
    // 设备过多
//    const EVENT_3 = 'EVENT_3';
    // 认证失败
    const EVENT_4 = 'EVENT_4';
    // 续期token
    const EVENT_5 = 'EVENT_5';
    // 给用户推送消息
    const EVENT_6 = 'EVENT_6';
    // 更新某些数据
    const EVENT_7 = 'EVENT_7';
    // 重新登录
    const EVENT_8 = 'EVENT_8';

    /*
    public static function onHandShake(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $token = urldecode($request->header['sec-websocket-protocol']);
        $jwt = LamJwt::verifyToken($token, config('auth.jwtkey'));
        $id = $jwt['data']['id'] ?? '';
        if ($jwt['status'] !== 1 || empty($id)) {
            $response->end();
            return false;
        }

        $fd = $request->fd;

        $FdManager = FdManager::getInstance();
        $FdManager->setRowFd($id, $fd);
        $FdManager->setRowUid($fd, $id, $token);


        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        $key = base64_encode(
            sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            )
        );
        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }
        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();

        // 触发Open
        $Server = ServerManager::getInstance()->getSwooleServer();
        self::onOpen($Server, $request);
    }
    */

    /**
     * @param Server $server
     * @param \Swoole\Http\Request $request
     */
    public static function onOpen(Server $server, \Swoole\Http\Request $request)
    {
        echo '开始链接 Open fd=' . $request->fd . PHP_EOL;
        // fd真实的字段是在auth的时候才设置的
        // 从open到auth是有时间差的，无论该时间有多短，为避免期间检测到fd不存在被干掉，先占一个坑
        $FdManager = FdManager::getInstance();
        $FdManager->setRowUid($request->fd, -1, '');

//        Timer::getInstance()->after(60 * 1000, function () use ($FdManager, $request) {
//            $token = $FdManager->getUidByFd($request->fd, 'token');
//            // 校验token
//        });
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
//        var_dump($info, '===========debug onClose info');
        if ($info['websocket_status'] === 3)
        {
            echo "[websocket] client-{$fd} is closed " . PHP_EOL;
            $table = FdManager::getInstance();
            $uid = $table->getUidByFd($fd, 'uid');
            $table->delRowUid($fd);
            if ($uid) {
                $table->delRowFd($uid, $fd);
            } else {
                trace('没有找到关联fd的uid：fd=' . var_export($fd, true) . ',uid=' . var_export($uid, true));
            }
        } else {
            echo "[http] client-{$fd} is closed " . PHP_EOL;
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
    public static function onWorkerError(\Swoole\Server $serv, int $worker_id, int $worker_pid, int $exit_code, int $signal)
    {
        trace("WebSocket onError: worker_id={$worker_id} worker_pid={$worker_pid} exit_code={$exit_code} signal={$signal}", 'error');
    }

    public static function onShutdown(\Swoole\Server $serv)
    {
        trace('onShutdown---------------');
    }
}
