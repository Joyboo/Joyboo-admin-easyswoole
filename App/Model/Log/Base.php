<?php

namespace App\Model\Log;

use EasySwoole\ORM\AbstractModel;
use WonderGame\EsUtility\Model\BaseModelTrait;

abstract class Base extends AbstractModel
{
    use BaseModelTrait;

    protected $connectionName = 'log';
}
