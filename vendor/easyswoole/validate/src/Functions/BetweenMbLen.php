<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;
use Psr\Http\Message\UploadedFileInterface;

class BetweenMbLen extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'BetweenMbLen';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $min = array_shift($arg);
        $max = array_shift($arg);


        if (is_numeric($itemData) || is_string($itemData)) {
            if (mb_strlen($itemData) >= $min && mb_strlen($itemData) <= $max) {
                return true;
            }
        } elseif (is_array($itemData)) {
            if (count($itemData) >= $min && count($itemData) <= $max) {
                return true;
            }
        } elseif ($itemData instanceof UploadedFileInterface) {
            $size = $itemData->getSize();
            if ($size >= $min && $size <= $max) {
                return true;
            }
        }

        return false;
    }
}
