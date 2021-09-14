<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class ActiveUrl extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'ActiveUrl';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!is_string($itemData)) {
            return false;
        }

        if (!filter_var($itemData, FILTER_VALIDATE_URL)) {
            return false;
        }

        return checkdnsrr(parse_url($itemData, PHP_URL_HOST));
    }
}
