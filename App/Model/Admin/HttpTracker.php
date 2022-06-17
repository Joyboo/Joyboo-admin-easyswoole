<?php

namespace App\Model\Admin;

use WonderGame\EsUtility\Model\Log\HttpTrackerTrait;

class HttpTracker extends Base
{
    use HttpTrackerTrait;

    protected function setBaseTraitProptected()
    {
        // 组件库的http_tracker在log库，此项目没有分库
        $this->connectionName = 'default';
        $this->sort = ['instime' => 'desc'];
        $this->autoTimeStamp = true;
    }
}
