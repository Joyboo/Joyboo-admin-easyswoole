<?php

namespace App\Task;

use App\Websocket\Events;

/**
 * 异步WebSocket消息
 * @property array $data
 */
class Message extends Base
{
    const SEND_ALL = 'all';

    public function run(int $taskId, int $workerIndex)
    {
        $adminid = $this->data['adminid'] ?? self::SEND_ALL;
        $event = $this->data['event'];
        $data = $this->data['data'];

        $const = (new \ReflectionClass(Events::class))->getConstants();
        if ( ! in_array($event, $const)) {
            throw new \Exception("Event Error: $event");
        }

        $push = ['event' => $event, 'data' => $data ];
        if ($adminid === self::SEND_ALL) {
            $this->pushAll($push);
        } else {
            // 支持多个
            if ( ! is_array($adminid)) {
                $adminid = [$adminid];
            }
            foreach ($adminid as $admid)
            {
                $this->pushByUid($admid, $push);
            }
        }
    }
}
