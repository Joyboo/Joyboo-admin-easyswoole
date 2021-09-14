<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class AlphaDash extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'AlphaDash';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return (new Regex())->validate($itemData, '/^[a-zA-Z0-9\-\_]+$/', $column, $validate);
    }
}
