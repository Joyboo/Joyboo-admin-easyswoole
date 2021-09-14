<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Spl\SplArray;
use EasySwoole\Validate\Validate;

class EqualWithColumn extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'EqualWithColumn';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $fieldName = array_shift($arg);
        $strict = array_shift($arg);

        $splArray = $validate->getVerifyData();
        if (!$splArray instanceof SplArray) {
            return false;
        }

        $value = $splArray->get($fieldName);

        return $strict ? $itemData === $value : $itemData == $value;
    }
}
