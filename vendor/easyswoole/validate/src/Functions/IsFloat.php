<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class IsFloat extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Float';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return filter_var($itemData, FILTER_VALIDATE_FLOAT) !== false;
    }
}
