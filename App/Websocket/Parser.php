<?php

namespace App\Websocket;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Client\WebSocket;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

/**
 * Websocket解析器
 * Class WebSocketParser
 * @package App\WebSocket
 */
class Parser implements ParserInterface
{
    /**
     * decode
     * @param  string         $raw    客户端原始消息
     * @param  WebSocket      $client WebSocket Client 对象
     * @return Caller         Socket  调用对象
     */
    public function decode($raw, $client) : ? Caller
    {
        var_dump($raw, '----raw clientId: ' . $client->getFd());
        // 心跳
        if ($raw === config('websocket.heartbeat.request_message'))
        {
            $this->respClient($client, config('websocket.heartbeat.response_message'));
            return null;
        }

        // 解析 客户端原始消息
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            // 是否需要关闭连接???
            $this->respClient($client, "WebSocket decode message error: " . var_export($raw, true));
            return null;
        }

        $class = '\\App\\Websocket\\Controller\\'. (ucfirst($data['class'] ?? 'Index'));
        if (!class_exists($class)) {
            $this->respClient($client, "WebSocket Controller not fount: {$class}");
            return null;
        }

        $action = $data['action'] ?? 'index';
        if (!method_exists($class, $action)) {
            $this->respClient($client, "WebSocket Action not fount: {$class}.{$action}");
            return null;
        }

        // new 调用者对象
        $caller = new Caller();
        $caller->setControllerClass($class);
        $caller->setAction($action);
        unset($data['class'], $data['action']);
        $caller->setArgs($data);
        return $caller;
    }
    /**
     * encode
     * @param  Response $response Socket Response 对象
     * @param  WebSocket $client WebSocket Client 对象
     * @return string 发送给客户端的消息
     */
    public function encode(Response $response, $client) : ? string
    {
        /**
         * 这里返回响应给客户端的信息
         * 这里应当只做统一的encode操作 具体的状态等应当由 Controller处理
         */
        $message = $response->getMessage();
        // 默认是 WEBSOCKET_OPCODE_TEXT 类型，转文本
        if (is_array($message))
        {
            $message = json_encode($message);
        }
        return $message;
    }


    /**
     * 响应客户端
     * @param WebSocket $client
     * @param string $message
     * @param string $status
     */
    protected function respClient($client, $message, $status = '')
    {
        // 默认正常响应
        if (empty($status)) {
            $status = Response::STATUS_OK;
        }
        $server = ServerManager::getInstance()->getSwooleServer();
        $response = new Response([
            'status' => $status,
            'message' => $message
        ]);
        $data = $this->encode($response, $client);
        if (is_null($data)) {
            return;
        }
//        trace($data, 'error');
        $fd = $client->getFd();
        if ($server->isEstablished($fd)) {
            $server->push($fd, $data, $response->getOpCode(), $response->isFinish());
        } else {
//            \Swoole\Coroutine::sleep(0.1);
//            $server->close($fd);
            // 暂时记录日志，看有无此场景，视情况决定处理
            trace("isEstablished为false， fd={$fd}, data={$data}", 'error');
        }
    }
}
