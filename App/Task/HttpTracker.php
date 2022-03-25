<?php

namespace App\Task;

use \App\Model\HttpTracker as HttpTrackerModel;

class HttpTracker extends Base
{
    public function run(int $taskId, int $workerIndex)
    {
        trace('HttpTracker 开始 ');
//        $count = $this->data['count'];
        $where = $this->data['where'];
        $chunk = $this->data['chunk'] ?? 300;

        /** @var HttpTrackerModel $model */
        $model = model('HttpTracker');

        $model->where($where)->chunk(
            function ($item) {
                /** @var HttpTrackerModel $item */
                $item->repeatOne();
            },
            $chunk
        );
        trace('HttpTracker 结束 ');
    }
}
