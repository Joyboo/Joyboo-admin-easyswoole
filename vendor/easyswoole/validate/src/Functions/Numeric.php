<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Numeric extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Numeric';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return is_numeric($itemData);
    }
}
