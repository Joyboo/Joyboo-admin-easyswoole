<?php


namespace App\Websocket\Controller;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket;
use WonderGame\EsUtility\Common\Classes\CtxRequest;

class BaseController extends Controller
{
    protected function onRequest(?string $actionName):bool
    {
        CtxRequest::getInstance()->caller = $this->caller();
        return parent::onRequest($actionName);
    }

    protected function onException(\Throwable $throwable):void
    {
        \EasySwoole\EasySwoole\Trigger::getInstance()->throwable($throwable);
    }

    protected function fmtMessage($event, $data = [], $message = '')
    {
        return json_encode(['event' => $event, 'data' => $data, 'message' => $message]);
    }

    protected function responseMessage($event, $data = [], $message = '')
    {
        return $this->response()->setMessage($this->fmtMessage($event, $data, $message));
    }

    protected function halfResponseMessage($event, $data = [], $message = '')
    {
        $messageContent = $this->fmtMessage($event, $data, $message);
        $Server = ServerManager::getInstance()->getSwooleServer();

        /** @var WebSocket $client */
        $client = $this->caller()->getClient();
        $fd = $client->getFd();
        if ($Server->isEstablished($fd)) {
            $Server->push($fd, $messageContent);
        } else {
            trace(" halfResponseMessage isEstablished为false， fd={$fd}, data={$messageContent}", 'error');
        }
    }
}
