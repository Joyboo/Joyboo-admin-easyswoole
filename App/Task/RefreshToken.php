<?php

namespace App\Task;

use App\Websocket\Events;

/**
 * @params uid
 * @params token
 */
class RefreshToken extends Base
{
    public function run(int $taskId, int $workerIndex)
    {
        $this->pushByUid($this->data['uid'], [
            'event' => Events::EVENT_5,
            'data' => ['token' => $this->data['token']]
        ]);
    }
}
