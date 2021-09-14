<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Decimal extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Decimal';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (is_null($arg)) {
            return filter_var($itemData, FILTER_VALIDATE_FLOAT) !== false;
        }
        if (intval($arg) === 0) {
            // 容错处理 如果小数点后设置0位 则验整数
            return filter_var($itemData, FILTER_VALIDATE_INT) !== false;
        }

        // "/^(0|[1-9]+[0-9]*)(.[0-9]{1,' . {$arg} . '})?$/"
        return (new Regex())->validate($itemData, "/^-?(([1-9]\d*)|0)\.\d{1,$arg}$/", $column, $validate);
    }
}
