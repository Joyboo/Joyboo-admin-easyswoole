<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Url extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Url';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return filter_var($itemData, FILTER_VALIDATE_URL) ? true : false;
    }
}
