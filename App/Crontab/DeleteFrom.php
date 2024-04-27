<?php

namespace App\Crontab;

use EasySwoole\Mysqli\QueryBuilder;
use WonderGame\EsUtility\Common\Classes\Mysqli;

class DeleteFrom
{
// 删除N天前的链路追踪日志
    public function delHttpTracker($args = [])
    {
        $days = intval($args['days'] ?? 30);
        if ($days < 10) {
            $days = 10;
        }

        $begintime = strtotime("-{$days} days");

        $where = ['instime' => [$begintime, '<=']];

        $this->delTableRows('default', 'http_tracker', $where, $args['limit'] ?? null);
    }

    // 删除N天前的服务进程监控日志
    public function delProcessInfo()
    {
        $days = intval($args['days'] ?? 3);
        if ($days < 1) {
            $days = 1;
        }

        $begintime = strtotime("-{$days} days");

        $where = ['instime' => [$begintime, '<=']];
        $this->delTableRows('default', 'process_info', $where, $args['limit'] ?? null);
    }

    /**
     * @param $db 选择数据库
     * @param $table 表名
     * @param $where 条件
     * @return void
     */
    protected function delTableRows($db, $table, $where, $limit = null)
    {
        if (empty($where)){
            return;
        }

        $Builder = new QueryBuilder();
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $value = is_array($value) ? $value : [$value];
                $Builder->where($key, ...$value);
            }
        } else {
            $Builder->where($where);
        }

        $Builder->delete($table, $limit);

        $Mysqli = new Mysqli($db, ['timeout' => -1]);

        $totalCount = 0;

        try {
            do {
                $Res = $Mysqli->query($Builder);

                $nums = $Res->getAffectedRows();
                $totalCount += $nums;
                trace("$db.{$table}已删除 $nums 行，SQL=" . $Builder->getLastQuery());

                // delete会锁表，让出队列时间
                \Swoole\Coroutine::sleep(1);
            } while ($nums > 0);
        } catch (\Exception $e) {}

        trace("$db.{$table}删除完成，累计删除 $totalCount 行");
        $Mysqli->close();
    }
}
