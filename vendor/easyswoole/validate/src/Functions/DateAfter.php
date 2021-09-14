<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class DateAfter extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'DateAfter';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!is_string($itemData)) {
            return false;
        }

        if (empty($arg)) {
            $arg = date('ymd');
        }

        $afterUnixTime = strtotime($arg);
        $unixTime = strtotime($itemData);
        if (is_bool($afterUnixTime) || is_bool($unixTime)) {
            return false;
        }

        if ($unixTime > $afterUnixTime) {
            return true;
        }

        return false;
    }
}
