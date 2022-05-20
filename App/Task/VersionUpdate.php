<?php


namespace App\Task;


use App\Websocket\Events;

class VersionUpdate extends Base
{
    public function run(int $taskId, int $workerIndex)
    {
        $this->toAllAdmin([
            'event' => Events::EVENT_2,
            'data' => ['force' => $this->data['force']]
        ]);
    }
}
