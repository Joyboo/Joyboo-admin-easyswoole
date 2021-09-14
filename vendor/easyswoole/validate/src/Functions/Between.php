<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Between extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Between';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $min = array_shift($arg);
        $max = array_shift($arg);

        if (!is_numeric($itemData) && !is_string($itemData)) {
            return false;
        }

        if ($itemData <= $max && $itemData >= $min) {
            return true;
        }

        return false;
    }
}
