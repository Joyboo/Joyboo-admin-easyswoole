<?php

namespace App\Model;

use App\Task\VersionUpdate;
use EasySwoole\EasySwoole\Task\TaskManager;
use WonderGame\EsUtility\Model\SysinfoModelTrait;


class Sysinfo extends Base
{
    use SysinfoModelTrait;

    public function getCacheKey(): string
    {
        return 'JoybooSysinfo';
    }
}
