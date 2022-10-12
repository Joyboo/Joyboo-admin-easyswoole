<?php


namespace App\Model\Admin;

use WonderGame\EsUtility\Model\Admin\LogLoginTrait;

class LogLogin extends Base
{
    use LogLoginTrait;

    /**
     * 更新最新一条登录记录的updtime为WebSocket上次连接时间
     * @param $uid
     * @return bool
     */
    public function updateConnectTime($uid)
    {
        $last = $this->where('uid', $uid)->order('instime', 'desc')->get();

        $time = time();
        $lastUpdtime = $last->getAttr('updtime');
        // 超过10s
        $last && $time - $lastUpdtime > 10 && $last->update(['updtime' => $time]);
    }
}
