<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

abstract class AbstractValidateFunction
{
    abstract public function name(): string;

    abstract public function validate($itemData, $arg, $column, Validate $validate): bool;
}
