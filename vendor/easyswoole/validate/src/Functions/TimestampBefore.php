<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class TimestampBefore extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'TimestampBefore';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (is_numeric($itemData) && is_numeric($arg)) {
            return intval($itemData) < intval($arg);
        }

        return false;
    }
}
