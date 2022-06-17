<?php

namespace App\HttpController\Admin;

use App\Common\Http\Code;
use EasySwoole\Mysqli\QueryBuilder;
use WonderGame\EsUtility\HttpController\Admin\HttpTrackerTrait;

class HttpTracker extends Auth
{
    use HttpTrackerTrait;

    protected function instanceModel()
    {
        // 此项目没有分库，都在admin
        $this->Model = model_admin($this->getStaticClassName());
        return true;
    }

    protected array $_authAlias = ['repeat' => 'index', 'count' => 'index', 'run' => 'index'];
}
