<?php

namespace App\Task;

use App\Model\Admin\HttpTracker as HttpTrackerModel;

class HttpTracker extends Base
{
    public function run(int $taskId, int $workerIndex)
    {
        trace('HttpTracker 开始 ');
//        $count = $this->data['count'];
        $where = $this->data['where'];
        $chunk = $this->data['chunk'] ?? 300;

        /** @var HttpTrackerModel $model */
        $model = model_admin('HttpTracker');

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
