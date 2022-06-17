<?php


namespace App\Model\Admin;

use WonderGame\EsUtility\Model\Admin\CrontabTrait;

class Crontab extends Base
{
    use CrontabTrait;

    public function getCrontab($svr = '')
    {
        // 0-启用,2-运行一次
        return $this->where(['status' => [[0, 2], 'in']])->all();
    }
}
