<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Different extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Different';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $value = array_shift($arg);
        $strict = array_shift($arg);

        return !($strict ? $itemData === $value : $itemData == $value);
    }
}
