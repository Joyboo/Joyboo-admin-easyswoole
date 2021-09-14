<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Func extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Func';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!is_callable($arg)) {
            return false;
        }

        return call_user_func($arg, $itemData, $column, $validate);
    }
}
