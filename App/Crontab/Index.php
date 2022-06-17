<?php


namespace App\Crontab;

use App\Model\Admin\HttpTracker;

/**
 * 这是一个简单示例，在后台管理中添加Crontab即可运行！
 * Class Index
 * @package App\Crontab
 */
class Index
{
    public function test()
    {
        var_dump(date('Y-m-d H:i:s') .' test ok');
    }

    /**
     * 删N天前的链路追踪日志, todo 删对应分区、增加对应分区
     * @param $args
     * @return void
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function delHttpTracker($args = [])
    {
        $days = intval($args['days'] ?? 90);
        // 最近10天不删除
        if ($days < 10)
        {
            return;
        }

        $begintime = strtotime("-{$days} days");

        /** @var HttpTracker $model */
        $model = model_admin('HttpTracker');
        $model->where('instime', $begintime, '<=')->destroy();
    }
}
