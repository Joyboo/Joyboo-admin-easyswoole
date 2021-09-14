<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Spl\SplArray;
use EasySwoole\Validate\Validate;

class GreaterThanWithColumn extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'GreaterThanWithColumn';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $numericFunction = new Numeric();
        if (!$numericFunction->validate($itemData, null, $column, $validate)) {
            return false;
        }

        $splArray = $validate->getVerifyData();
        if (!$splArray instanceof SplArray) {
            return false;
        }
        $value = $splArray->get($arg);

        if (!$numericFunction->validate($value, null, $column, $validate)) {
            return false;
        }

        if ($itemData > $value) {
            return true;
        }

        return false;
    }
}
