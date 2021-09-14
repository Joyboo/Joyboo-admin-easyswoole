<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Regex extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Regex';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!is_numeric($itemData) && !is_string($itemData)) {
            return false;
        }

        return preg_match($arg, (string)$itemData) ? true : false;
    }
}
