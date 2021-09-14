<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;

class Timestamp extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'Timestamp';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!is_numeric($itemData)) {
            return false;
        }

        if (strtotime(date('d-m-Y H:i:s', $itemData)) === (int)$itemData) {
            return true;
        }

        return false;
    }
}
