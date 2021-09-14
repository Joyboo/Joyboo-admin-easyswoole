<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Optional extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Optional';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return true;
    }
}
