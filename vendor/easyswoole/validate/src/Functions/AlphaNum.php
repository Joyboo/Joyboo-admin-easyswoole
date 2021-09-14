<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class AlphaNum extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'AlphaNum';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return (new Regex())->validate($itemData, '/^[a-zA-Z0-9]+$/', $column, $validate);
    }
}
