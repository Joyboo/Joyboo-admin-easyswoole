<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Integer extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Integer';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return filter_var($itemData, FILTER_VALIDATE_INT) !== false;
    }
}
