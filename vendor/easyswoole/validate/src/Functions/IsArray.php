<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class IsArray extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'IsArray';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return is_array($itemData);
    }
}
