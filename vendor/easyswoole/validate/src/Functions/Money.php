<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Money extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Money';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (is_null($arg)) {
            $arg = '';
        }
//        $regex = '/^(0|[1-9]+[0-9]*)(.[0-9]{1,' . $arg . '})?$/';

        $regex = "/^-?(([1-9]\d*)|0)\.\d{1,$arg}$/";

        return (new Regex())->validate($itemData, $regex, $column, $validate);
    }
}
