<?php

namespace App\Websocket\Controller;

use App\Common\Classes\FdManager;
use App\Common\Languages\Dictionary;
use App\Websocket\Events;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\Client\WebSocket;
use WonderGame\EsUtility\Common\Classes\LamJwt;

class Index extends BaseController
{
    public function index()
    {
        $this->response()->setMessage('time: ' . time());
    }

    public function test()
    {
        $this->response()->setMessage('success');
    }

    protected function checkToken()
    {
        $FdManager = FdManager::getInstance();
        /** @var WebSocket $client */
        $client = $this->caller()->getClient();
        $fd = $client->getFd();

        if ($token = $FdManager->getUidByFd($fd, 'token')) {
            $jwtCfg = config('auth');
            $jwt = LamJwt::verifyToken($token, $jwtCfg['jwtkey'], false);
            $id = $jwt['data']['data']['id'] ?? '';
            if ($jwt['status'] != 1 || empty($id))
            {
                $this->responseMessage(Events::EVENT_4, ['fd' => $fd], lang(Dictionary::ADMIN_AUTHTRAIT_2));
                return false;
            }

            // 续期
            $exp = $jwt['data']['exp'];
//        var_dump('心跳  token到期时间为 ' . date('Y-m-d H:i:s', $exp) . "，当前时间" . date('Y-m-d H:i:s') . ", 有效期{$jwtCfg['refresh_time']}秒");
            if (is_numeric($jwtCfg['refresh_time'])
                && is_numeric($exp)
                && ($exp - time() <= $jwtCfg['refresh_time'])
                && class_exists($jwtCfg['refresh_task'])
            ) {
                $newToken = get_login_token($id);
                $taskData = ['uid' => $id, 'token' => $newToken];
                $status = TaskManager::getInstance()->async(new $jwtCfg['refresh_task']($taskData));
                if ($status > 0) {
                    $FdManager->setRowUid($fd, $id, $newToken);
                }
            }
        }

        return true;
    }

    /**
     * 心跳
     * 客户端连接成功后应发一条auth认证
     * @return void
     */
    public function heartbeat()
    {
        if ($this->checkToken()) {
            $this->responseMessage(Events::EVENT_0, [], config('ws.heartbeat.response_message'));
        }
    }

    /**
     * 客户端连接后，会主动发一条认证信息
     * 客户端有自动重连，认证失败断开连接的操作由客户端完成，否则会一直重连
     * @return void
     */
    public function auth()
    {
        $tokenKey = config('TOKEN_KEY');
        $args = $this->caller()->getArgs();
        /** @var WebSocket $client */
        $client = $this->caller()->getClient();
        $fd = $client->getFd();

        $sysinfo = sysinfo();
        $env = \EasySwoole\EasySwoole\Core::getInstance()->runMode();
        if (isset($args['versions'][$env]) && isset($sysinfo['versions'][$env]) && $args['versions'][$env] != $sysinfo['versions'][$env]) {
            $this->halfResponseMessage(Events::EVENT_2, ['force' => 1]);
        }
        unset($sysinfo, $env);

        $token = $args[$tokenKey] ?? '';
        if (empty($token)) {
            trace('没有token值哦 args=' . json_encode($args) . ',$tokenKey=' . $tokenKey . ', args[$tokenKey]=' . $token);
            return $this->responseMessage(Events::EVENT_4, ['fd' => $fd], lang(Dictionary::ADMIN_AUTHTRAIT_1));
        }

        $jwt = LamJwt::verifyToken($token, config('auth.jwtkey'));
        $id = $jwt['data']['id'] ?? '';
        if ($jwt['status'] != 1 || empty($id))
        {
            return $this->responseMessage(Events::EVENT_4, ['fd' => $fd], lang(Dictionary::ADMIN_AUTHTRAIT_2));
        }

        $FdManager = FdManager::getInstance();
        $FdManager->setRowFd($id, $fd);
        $FdManager->setRowUid($fd, $id, $token);
    }
}
