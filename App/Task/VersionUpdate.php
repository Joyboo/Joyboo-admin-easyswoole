<?php


namespace App\Task;


use App\Websocket\Events;

class VersionUpdate extends Base
{
    public function run(int $taskId, int $workerIndex)
    {
        parent::run($taskId, $workerIndex);

        $this->toAllAdmin([
            'event' => Events::SYSTEM_VERSION_UPDATE,
            'data' => ['force' => $this->data['force']]
        ]);
    }
}
