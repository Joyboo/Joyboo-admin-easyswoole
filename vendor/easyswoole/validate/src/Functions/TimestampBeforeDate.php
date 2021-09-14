<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class TimestampBeforeDate extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'TimestampBeforeDate';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!is_numeric($itemData)) {
            return false;
        }

        $time = strtotime($arg);
        if ($time !== false && $time > 0 && $time > $itemData) {
            return true;
        }

        return false;
    }
}
