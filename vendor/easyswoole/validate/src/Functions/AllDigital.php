<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class AllDigital extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'AllDigital';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        return (new Regex())->validate($itemData, '/^\d+$/', $column, $validate);
    }
}
