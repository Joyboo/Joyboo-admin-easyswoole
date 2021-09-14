<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Alpha extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Alpha';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return (new Regex())->validate($itemData, '/^[a-zA-Z]+$/', $column, $validate);
    }
}
