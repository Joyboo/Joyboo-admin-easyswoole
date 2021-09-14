<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class IsIp extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'IsIp';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return filter_var($itemData, FILTER_VALIDATE_IP) ? true : false;
    }
}
