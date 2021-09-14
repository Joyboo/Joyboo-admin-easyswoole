<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class NotEmpty extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'NotEmpty';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if ($itemData === 0 || $itemData === '0') {
            return true;
        }

        return !empty($itemData);
    }
}
