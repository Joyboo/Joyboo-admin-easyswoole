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

        $this->delTableRows('http_tracker', $days);
    }

    // 删除N天前的服务进程监控日志
    public function delProcessInfo()
    {
        $days = intval($args['days'] ?? 3);
        if ($days < 1) {
            $days = 1;
        }

        $this->delTableRows('process_info', $days);
    }

    protected function delTableRows($table, $days, $field = 'instime')
    {
        $begintime = strtotime("-{$days} days");

        $Builder = new QueryBuilder();
        $Builder->where($field, $begintime, '<=')->delete($table);

        $Mysqli = new Mysqli('default', ['timeout' => -1]);
        $Res = $Mysqli->query($Builder);

        $nums = $Res->getAffectedRows();
        trace_immediate("{$table}已删除 $nums 行，SQL=" . $Builder->getLastQuery());
        $Mysqli->close();
    }
}
