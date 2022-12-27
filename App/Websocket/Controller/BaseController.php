<?php


namespace App\Websocket\Controller;

use EasySwoole\Socket\AbstractInterface\Controller;
use WonderGame\EsUtility\WebSocket\Controller\BaseControllerTrait;

class BaseController extends Controller
{
    use BaseControllerTrait;

    protected function fmtMessage($event, $data = [], $message = '')
    {
        // message <-> msg
        return json_encode(['event' => $event, 'data' => $data, 'message' => $message]);
    }
}
