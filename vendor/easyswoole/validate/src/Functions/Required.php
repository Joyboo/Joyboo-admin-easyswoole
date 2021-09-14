<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Required extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Required';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if ($itemData === null) {
            return false;
        }

        return true;
    }
}
