<?php

namespace App\HttpController\Admin;

use App\Common\Http\Code;
use EasySwoole\Mysqli\QueryBuilder;
use WonderGame\EsUtility\HttpController\Admin\HttpTrackerTrait;

class HttpTracker extends Auth
{
    use HttpTrackerTrait;

    protected array $_authAlias = ['repeat' => 'index', 'count' => 'index', 'run' => 'index'];
}
